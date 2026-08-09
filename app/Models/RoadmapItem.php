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
    ];

    protected function casts(): array
    {
        return [
            'unlocked_at' => 'datetime',
            'completed_at' => 'datetime',
            'evaluation_score' => 'float',
        ];
    }

    public function roadmap(): BelongsTo
    {
        return $this->belongsTo(Roadmap::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(LearningMaterial::class, 'learning_material_id');
    }

    public function progressLogs(): HasMany
    {
        return $this->hasMany(ProgressLog::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
}
