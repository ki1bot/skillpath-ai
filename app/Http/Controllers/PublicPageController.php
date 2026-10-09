<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Services\AcademicStatisticsService;
use App\Services\CareerCompatibilityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller
{
    public function home(
        AcademicStatisticsService $statistics,
    ): Response {
        return Inertia::render(
            'welcome',
            [
                'careers' => Career::query()
                    ->select([
                        'id',
                        'name',
                        'slug',
                        'tagline',
                        'difficulty',
                        'accent',
                    ])
                    ->where(
                        'is_active',
                        true,
                    )
                    ->withCount('skills')
                    ->orderBy('id')
                    ->get(),

                'stats' => [
                    'careers' => $statistics->careers,
                    'skills' => $statistics->areas,
                    'materials' => $statistics->topics,
                ],
            ],
        );
    }

    public function careers(
        Request $request,
        CareerCompatibilityService $compatibilityService,
    ): Response {
        $careers = Career::query()
            ->where(
                'is_active',
                true,
            )
            ->with([
                'skills' => fn ($query) => $query
                    ->orderByPivot(
                        'importance_weight',
                        'desc',
                    ),
            ])
            ->get();

        $compatibilities = $request->user()
            ? $compatibilityService
                ->calculateForCareers(
                    $request->user(),
                    $careers,
                )
            : [];

        $payload = $careers
            ->map(
                fn (Career $career) => [
                    ...$career->toArray(),
                    'compatibility' => $compatibilities[
                        $career->id
                    ] ?? null,
                ],
            )
            ->values();

        return Inertia::render(
            'public/careers',
            [
                'careers' => $payload,
            ],
        );
    }

    public function career(
        Request $request,
        Career $career,
        CareerCompatibilityService $compatibilityService,
    ): Response {
        abort_unless(
            $career->is_active,
            404,
        );

        $career->load([
            'skills' => fn ($query) => $query
                ->orderByPivot(
                    'importance_weight',
                    'desc',
                ),
            'projects',
        ]);

        return Inertia::render(
            'public/career-show',
            [
                'career' => [
                    ...$career->toArray(),

                    'compatibility' => $request->user()
                        ? $compatibilityService
                            ->calculate(
                                $request->user(),
                                $career,
                            )
                        : null,
                ],
            ],
        );
    }

    public function about(): Response
    {
        return Inertia::render(
            'public/about',
        );
    }
}
