<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function careers(): BelongsToMany
    {
        return $this->belongsToMany(Career::class)
            ->withPivot(['target_level', 'importance_weight', 'is_required'])
            ->withTimestamps();
    }

    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            Skill::class,
            'skill_prerequisites',
            'skill_id',
            'prerequisite_skill_id',
        )->withPivot('factor')->withTimestamps();
    }

    public function dependents(): BelongsToMany
    {
        return $this->belongsToMany(
            Skill::class,
            'skill_prerequisites',
            'prerequisite_skill_id',
            'skill_id',
        )->withPivot('factor')->withTimestamps();
    }

    public function materials(): HasMany
    {
        return $this->hasMany(LearningMaterial::class);
    }

    public function userSkills(): HasMany
    {
        return $this->hasMany(UserSkill::class);
    }
}
