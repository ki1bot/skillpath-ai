<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoadmapItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'roadmap_id',
        'learning_material_id',
        'stage',
        'stage_title',
        'position',
        'status',
        'progress_percentage',
        'unlocked_at',
        'completed_at',
        'evaluation_score',
        'evaluation_attempts',
        'reinforcement_count',
        'reinforcement_for_roadmap_item_id',
    ];

    protected function casts(): array
    {
        return [
            'unlocked_at' => 'datetime',
            'completed_at' => 'datetime',
            'evaluation_score' => 'float',
            'evaluation_attempts' => 'integer',
            'reinforcement_count' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Roadmap, $this>
     */
    public function roadmap(): BelongsTo
    {
        return $this->belongsTo(Roadmap::class);
    }

    /**
     * @return BelongsTo<LearningMaterial, $this>
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(
            LearningMaterial::class,
            'learning_material_id',
        );
    }

    /**
     * @return HasMany<ProgressLog, $this>
     */
    public function progressLogs(): HasMany
    {
        return $this->hasMany(ProgressLog::class);
    }

    /**
     * @return HasMany<Evaluation, $this>
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * @return BelongsTo<RoadmapItem, $this>
     */
    public function reinforcementForRoadmapItem(): BelongsTo
    {
        return $this->belongsTo(
            RoadmapItem::class,
            'reinforcement_for_roadmap_item_id',
        );
    }

    /**
     * @return HasMany<RoadmapItem, $this>
     */
    public function reinforcementItems(): HasMany
    {
        return $this->hasMany(
            RoadmapItem::class,
            'reinforcement_for_roadmap_item_id',
        );
    }
}
