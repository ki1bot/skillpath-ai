<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Career extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'description',
        'responsibilities',
        'difficulty',
        'accent',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'responsibilities' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class)
            ->withPivot(['target_level', 'importance_weight', 'is_required'])
            ->withTimestamps();
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(PortfolioProject::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'target_career_id');
    }
}
