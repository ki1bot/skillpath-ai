<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assessment_id',
        'assessment_question_id',
        'skill_id',
        'attempt_uuid',
        'score',
        'is_correct',
        'self_rating',
        'answer',
        'response_text',
        'evidence_url',
        'experience_notes',
        'experience_evidence_url',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'float',
            'is_correct' => 'boolean',
            'self_rating' => 'integer',
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
     * @return BelongsTo<Assessment, $this>
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * @return BelongsTo<AssessmentQuestion, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(
            AssessmentQuestion::class,
            'assessment_question_id',
        );
    }

    /**
     * @return BelongsTo<Skill, $this>
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
