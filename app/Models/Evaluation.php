<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'roadmap_item_id',
        'score',
        'knowledge_score',
        'evidence_score',
        'reflection_score',
        'passed',
        'answer',
        'evidence_url',
        'reflection',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'float',
            'knowledge_score' => 'float',
            'evidence_score' => 'float',
            'reflection_score' => 'float',
            'passed' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<RoadmapItem, $this>
     */
    public function roadmapItem(): BelongsTo
    {
        return $this->belongsTo(RoadmapItem::class);
    }
}
