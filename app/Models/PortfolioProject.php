<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PortfolioProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'career_id',
        'title',
        'slug',
        'summary',
        'problem_statement',
        'difficulty',
        'minimum_features',
        'stretch_features',
        'completion_criteria',
        'estimated_hours',
    ];

    protected function casts(): array
    {
        return [
            'minimum_features' => 'array',
            'stretch_features' => 'array',
            'completion_criteria' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'portfolio_project_skill')
            ->withPivot(['required_level', 'weight'])
            ->withTimestamps();
    }

    public function userProjects(): HasMany
    {
        return $this->hasMany(UserProject::class);
    }
}
