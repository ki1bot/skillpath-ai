<?php

namespace App\Services;

use App\Models\LearningMaterial;
use App\Models\ProgressLog;
use App\Models\Roadmap;
use App\Models\RoadmapItem;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdaptiveRoadmapService
{
    public function handleFailedEvaluation(
        User $user,
        RoadmapItem $failedItem,
    ): ?RoadmapItem {
        $failedItem->loadMissing([
            'roadmap',
            'material.skill',
        ]);

        if (
            ! $failedItem->roadmap
            || $failedItem->roadmap->user_id !== $user->id
        ) {
            return null;
        }

        return DB::transaction(
            function () use (
                $user,
                $failedItem,
            ) {
                $item = RoadmapItem::query()
                    ->with([
                        'roadmap',
                        'material.skill',
                    ])
                    ->lockForUpdate()
                    ->findOrFail(
                        $failedItem->id,
                    );

                if (
                    $item->material
                        ->material_type
                    === 'reinforcement'
                ) {
                    $item->update([
                        'status' => 'needs_reinforcement',
                        'reinforcement_count' => (
                            $item->reinforcement_count
                            + 1
                        ),
                    ]);

                    $this->logAdaptation(
                        $user,
                        $item,
                        'roadmap_reinforcement_retry',
                        'Evaluasi materi penguatan belum lulus. Materi penguatan tetap dibuka untuk dipelajari dan dievaluasi ulang.',
                    );

                    return null;
                }

                $reinforcement = LearningMaterial::query()
                    ->where(
                        'reinforcement_for_material_id',
                        $item->learning_material_id,
                    )
                    ->where(
                        'material_type',
                        'reinforcement',
                    )
                    ->where(
                        'is_active',
                        true,
                    )
                    ->first();

                if (! $reinforcement) {
                    $item->update([
                        'status' => 'needs_reinforcement',
                        'reinforcement_count' => (
                            $item->reinforcement_count
                            + 1
                        ),
                    ]);

                    $this->logAdaptation(
                        $user,
                        $item,
                        'roadmap_reinforcement_missing',
                        'Evaluasi belum lulus, tetapi materi penguatan khusus belum tersedia. Materi utama tetap dapat dipelajari ulang.',
                    );

                    return null;
                }

                $item->update([
                    'status' => 'reinforcement_required',
                    'reinforcement_count' => (
                        $item->reinforcement_count
                        + 1
                    ),
                ]);

                $existing = RoadmapItem::query()
                    ->where(
                        'roadmap_id',
                        $item->roadmap_id,
                    )
                    ->where(
                        'learning_material_id',
                        $reinforcement->id,
                    )
                    ->first();

                if ($existing) {
                    $existing->update([
                        'status' => 'available',
                        'progress_percentage' => 0,
                        'completed_at' => null,
                        'evaluation_score' => null,
                        'unlocked_at' => now(),
                        'reinforcement_for_roadmap_item_id' => $item->id,
                    ]);

                    $this->recalculateEstimatedWeeks(
                        $item->roadmap,
                        $user,
                    );

                    $this->logAdaptation(
                        $user,
                        $existing,
                        'roadmap_reinforcement_reopened',
                        "Materi penguatan {$reinforcement->title} dibuka kembali karena evaluasi {$item->material->title} belum lulus.",
                    );

                    return $existing;
                }

                $insertPosition = $item->position;

                RoadmapItem::query()
                    ->where(
                        'roadmap_id',
                        $item->roadmap_id,
                    )
                    ->where(
                        'position',
                        '>=',
                        $insertPosition,
                    )
                    ->increment('position');

                $reinforcementItem = RoadmapItem::create([
                    'roadmap_id' => $item->roadmap_id,
                    'learning_material_id' => $reinforcement->id,
                    'stage' => $item->stage,
                    'stage_title' => $item->stage_title,
                    'position' => $insertPosition,
                    'status' => 'available',
                    'progress_percentage' => 0,
                    'unlocked_at' => now(),
                    'reinforcement_for_roadmap_item_id' => $item->id,
                ]);

                $this->recalculateEstimatedWeeks(
                    $item->roadmap,
                    $user,
                );

                $this->logAdaptation(
                    $user,
                    $reinforcementItem,
                    'roadmap_reinforcement_added',
                    "Materi penguatan {$reinforcement->title} ditambahkan sebelum {$item->material->title} karena evaluasi belum lulus.",
                );

                return $reinforcementItem;
            },
        );
    }

    public function handlePassedReinforcement(
        User $user,
        RoadmapItem $reinforcementItem,
    ): void {
        $reinforcementItem->loadMissing([
            'roadmap',
            'material',
        ]);

        if (
            $reinforcementItem
                ->material
                ->material_type
            !== 'reinforcement'
        ) {
            return;
        }

        if (
            ! $reinforcementItem
                ->reinforcement_for_roadmap_item_id
        ) {
            return;
        }

        $blockedItem = RoadmapItem::query()
            ->where(
                'id',
                $reinforcementItem
                    ->reinforcement_for_roadmap_item_id,
            )
            ->where(
                'roadmap_id',
                $reinforcementItem->roadmap_id,
            )
            ->first();

        if (! $blockedItem) {
            return;
        }

        if (
            $blockedItem->status
            === 'reinforcement_required'
        ) {
            $blockedItem->update([
                'status' => 'available',
                'unlocked_at' => now(),
            ]);

            $blockedItem->loadMissing('material');

            $this->logAdaptation(
                $user,
                $reinforcementItem,
                'roadmap_reinforcement_completed',
                "Materi penguatan selesai. {$blockedItem->material->title} dapat dievaluasi kembali.",
            );
        }
    }

    public function adaptForInactivity(
        User $user,
        int $inactiveDays = 14,
    ): bool {
        $roadmap = Roadmap::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->where(
                'is_active',
                true,
            )
            ->with(
                'items.material',
            )
            ->first();

        if (! $roadmap) {
            return false;
        }

        $lastLearningActivity = ProgressLog::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->whereIn(
                'activity_type',
                [
                    'learning',
                    'evaluation_passed',
                    'evaluation_failed',
                    'project_progress',
                    'project_completed',
                ],
            )
            ->latest(
                'logged_at',
            )
            ->value(
                'logged_at',
            );

        $anchor = $lastLearningActivity
            ? Carbon::parse(
                $lastLearningActivity,
            )
            : $roadmap->created_at;

        if (
            ! $anchor
            || $anchor->greaterThan(
                now()->subDays(
                    $inactiveDays,
                ),
            )
        ) {
            return false;
        }

        $alreadyAdjusted = ProgressLog::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->where(
                'activity_type',
                'roadmap_inactivity_adjusted',
            )
            ->where(
                'logged_at',
                '>=',
                $anchor,
            )
            ->exists();

        if ($alreadyAdjusted) {
            return false;
        }

        $remainingMinutes = $roadmap
            ->items
            ->where(
                'status',
                '!=',
                'completed',
            )
            ->sum(
                fn ($item) => (
                    (int) $item
                        ->material
                        ->estimated_minutes
                ),
            );

        $weeklyMinutes = max(
            (
                (int) $user
                    ->weekly_study_hours
            ) * 60,
            60,
        );

        $baseWeeks = max(
            (int) ceil(
                $remainingMinutes
                / $weeklyMinutes,
            ),
            1,
        );

        $newEstimate = max(
            $roadmap->estimated_weeks,
            $baseWeeks + 1,
        );

        $roadmap->update([
            'estimated_weeks' => $newEstimate,
        ]);

        ProgressLog::create([
            'user_id' => $user->id,
            'activity_type' => 'roadmap_inactivity_adjusted',
            'minutes_spent' => 0,
            'progress_percentage' => 0,
            'notes' => "Estimasi roadmap disesuaikan menjadi {$newEstimate} minggu setelah tidak ada aktivitas belajar selama minimal {$inactiveDays} hari.",
            'logged_at' => now(),
        ]);

        return true;
    }

    private function recalculateEstimatedWeeks(
        Roadmap $roadmap,
        User $user,
    ): void {
        $roadmap->loadMissing(
            'items.material',
        );

        $remainingMinutes = $roadmap
            ->items
            ->where(
                'status',
                '!=',
                'completed',
            )
            ->sum(
                fn ($item) => (
                    (int) $item
                        ->material
                        ->estimated_minutes
                ),
            );

        $weeklyMinutes = max(
            (
                (int) $user
                    ->weekly_study_hours
            ) * 60,
            60,
        );

        $roadmap->update([
            'estimated_weeks' => max(
                (int) ceil(
                    $remainingMinutes
                    / $weeklyMinutes,
                ),
                1,
            ),
        ]);
    }

    private function logAdaptation(
        User $user,
        RoadmapItem $item,
        string $activityType,
        string $notes,
    ): void {
        ProgressLog::create([
            'user_id' => $user->id,
            'roadmap_item_id' => $item->id,
            'activity_type' => $activityType,
            'minutes_spent' => 0,
            'progress_percentage' => $item->progress_percentage,
            'notes' => $notes,
            'logged_at' => now(),
        ]);
    }
}
