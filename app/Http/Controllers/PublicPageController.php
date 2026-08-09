<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\Skill;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('welcome', [
            'careers' => Career::query()
                ->where('is_active', true)
                ->withCount('skills')
                ->orderBy('id')
                ->limit(3)
                ->get(),
            'stats' => [
                'careers' => Career::query()->where('is_active', true)->count(),
                'skills' => Skill::query()->count(),
                'materials' => LearningMaterial::query()->count(),
            ],
        ]);
    }

    public function careers(): Response
    {
        return Inertia::render('public/careers', [
            'careers' => Career::query()
                ->where('is_active', true)
                ->with([
                    'skills' => fn ($query) => $query
                        ->orderByPivot('importance_weight', 'desc'),
                ])
                ->get(),
        ]);
    }

    public function career(Career $career): Response
    {
        abort_unless($career->is_active, 404);

        return Inertia::render('public/career-show', [
            'career' => $career->load([
                'skills' => fn ($query) => $query
                    ->orderByPivot('importance_weight', 'desc'),
                'projects',
            ]),
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('public/about');
    }
}
