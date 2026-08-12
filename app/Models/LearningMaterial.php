<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_id',
        'material_type',
        'reinforcement_for_material_id',
        'is_active',
        'title',
        'slug',
        'summary',
        'learning_objectives',
        'difficulty',
        'estimated_minutes',
        'resource_title',
        'resource_url',
        'practice_task',
        'quiz_question',
        'quiz_options',
        'quiz_answer',
        'quiz_explanation',
    ];

    protected function casts(): array
    {
        return [
            'learning_objectives' => 'array',
            'quiz_options' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo<Skill, $this>
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * @return HasMany<RoadmapItem, $this>
     */
    public function roadmapItems(): HasMany
    {
        return $this->hasMany(RoadmapItem::class);
    }

    /**
     * @return BelongsTo<LearningMaterial, $this>
     */
    public function reinforcementFor(): BelongsTo
    {
        return $this->belongsTo(
            LearningMaterial::class,
            'reinforcement_for_material_id',
        );
    }

    /**
     * @return HasMany<LearningMaterial, $this>
     */
    public function reinforcementMaterials(): HasMany
    {
        return $this->hasMany(
            LearningMaterial::class,
            'reinforcement_for_material_id',
        );
    }
}
