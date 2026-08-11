<?php

namespace App\Http\Controllers;

use App\Models\Career;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller
{
    public function home(): Response
    {
        $stats = (array) DB::selectOne(
            'SELECT
                (SELECT COUNT(*) FROM careers WHERE is_active = TRUE) AS careers,
                (SELECT COUNT(*) FROM skills) AS skills,
                (SELECT COUNT(*) FROM learning_materials) AS materials',
        );

        return Inertia::render('welcome', [
            'careers' => Career::query()
                ->select([
                    'id',
                    'name',
                    'slug',
                    'tagline',
                    'difficulty',
                    'accent',
                ])
                ->where('is_active', true)
                ->withCount('skills')
                ->orderBy('id')
                ->limit(3)
                ->get(),
            'stats' => [
                'careers' => (int) ($stats['careers'] ?? 0),
                'skills' => (int) ($stats['skills'] ?? 0),
                'materials' => (int) ($stats['materials'] ?? 0),
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
