<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property-read Pivot|null $pivot
 */
class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'difficulty',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsToMany<Career, $this>
     */
    public function careers(): BelongsToMany
    {
        return $this->belongsToMany(Career::class)
            ->withPivot([
                'target_level',
                'importance_weight',
                'is_required',
            ])
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Skill, $this>
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            Skill::class,
            'skill_prerequisites',
            'skill_id',
            'prerequisite_skill_id',
        )
            ->withPivot('factor')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Skill, $this>
     */
    public function dependents(): BelongsToMany
    {
        return $this->belongsToMany(
            Skill::class,
            'skill_prerequisites',
            'prerequisite_skill_id',
            'skill_id',
        )
            ->withPivot('factor')
            ->withTimestamps();
    }

    /**
     * @return HasMany<LearningMaterial, $this>
     */
    public function materials(): HasMany
    {
        return $this->hasMany(LearningMaterial::class);
    }

    /**
     * @return HasMany<UserSkill, $this>
     */
    public function userSkills(): HasMany
    {
        return $this->hasMany(UserSkill::class);
    }
}
