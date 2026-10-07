<?php

namespace App\Models;

use App\Support\AcademicProgramCatalog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'career_id',
        'study_program',
        'title',
        'description',
        'duration_minutes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (self $assessment): void {
            if (! $assessment->isAcademicCatalogAssessment()) {
                return;
            }

            if (
                $assessment->isDirty('career_id')
                || $assessment->isDirty('study_program')
            ) {
                throw ValidationException::withMessages([
                    'assessment' => 'Assessment akademik tidak dapat dipindahkan ke jurusan lain. Hubungan jurusan dan 50 soal wajib tetap dipertahankan.',
                ]);
            }

            if (
                $assessment->isDirty('is_active')
                && ! $assessment->is_active
            ) {
                throw ValidationException::withMessages([
                    'assessment' => 'Assessment akademik wajib tetap aktif agar mahasiswa dapat mengikuti penilaian awal.',
                ]);
            }
        });

        static::deleting(function (self $assessment): void {
            if (! $assessment->isAcademicCatalogAssessment()) {
                return;
            }

            throw ValidationException::withMessages([
                'assessment' => 'Assessment akademik tidak dapat dihapus karena menjadi bagian dari 50 soal wajib jurusan.',
            ]);
        });
    }

    private function isAcademicCatalogAssessment(): bool
    {
        $studyProgram = (string) $this->getRawOriginal(
            'study_program',
        );

        return AcademicProgramCatalog::program(
            $studyProgram,
        ) !== null;
    }

    /**
     * @return BelongsTo<Career, $this>
     */
    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    /**
     * @return HasMany<AssessmentQuestion, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class);
    }

    /**
     * @return HasMany<AssessmentResult, $this>
     */
    public function results(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }
}
