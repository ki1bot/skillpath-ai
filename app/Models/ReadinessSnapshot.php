<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadinessSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'career_id',
        'trigger',
        'score',
        'skill_mastery',
        'roadmap_completion',
        'project_score',
        'consistency',
        'evaluation_score',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'float',
            'skill_mastery' => 'float',
            'roadmap_completion' => 'float',
            'project_score' => 'float',
            'consistency' => 'float',
            'evaluation_score' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }
}
