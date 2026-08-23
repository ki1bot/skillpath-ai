<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\Roadmap;
use App\Models\User;
use Illuminate\Database\Seeder;

class AcademicProgramTransitionSeeder extends Seeder
{
    public function run(): void
    {
        $legacyCareerIds = Career::query()
            ->whereIn(
                'slug',
                [
                    'backend-developer',
                    'frontend-developer',
                    'data-analyst',
                ],
            )
            ->pluck('id');

        if ($legacyCareerIds->isEmpty()) {
            return;
        }

        $programs = Career::query()
            ->whereIn(
                'slug',
                [
                    'sistem-informasi',
                    'manajemen',
                    'teknik-informatika',
                    'psikologi',
                    'ilmu-komunikasi',
                ],
            )
            ->where(
                'is_active',
                true,
            )
            ->get()
            ->keyBy('name');

        foreach ($programs as $studyProgram => $career) {
            $userIds = User::query()
                ->whereIn(
                    'target_career_id',
                    $legacyCareerIds,
                )
                ->where(
                    'study_program',
                    $studyProgram,
                )
                ->pluck('id');

            if ($userIds->isEmpty()) {
                continue;
            }

            User::query()
                ->whereIn(
                    'id',
                    $userIds,
                )
                ->update([
                    'target_career_id' => $career->id,
                ]);

            Roadmap::query()
                ->whereIn(
                    'user_id',
                    $userIds,
                )
                ->where(
                    'is_active',
                    true,
                )
                ->update([
                    'is_active' => false,
                ]);
        }

        $unresolvedUserIds = User::query()
            ->whereIn(
                'target_career_id',
                $legacyCareerIds,
            )
            ->pluck('id');

        if ($unresolvedUserIds->isEmpty()) {
            return;
        }

        User::query()
            ->whereIn(
                'id',
                $unresolvedUserIds,
            )
            ->update([
                'target_career_id' => null,
                'onboarding_completed_at' => null,
            ]);

        Roadmap::query()
            ->whereIn(
                'user_id',
                $unresolvedUserIds,
            )
            ->where(
                'is_active',
                true,
            )
            ->update([
                'is_active' => false,
            ]);
    }
}
