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
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function roadmapItems(): HasMany
    {
        return $this->hasMany(RoadmapItem::class);
    }
}
