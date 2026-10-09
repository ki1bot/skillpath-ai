<?php

namespace App\Http\Middleware;

use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Roadmap;
use App\Support\AcademicProgramCatalog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAcademicContentScope
{
    public function handle(
        Request $request,
        Closure $next,
    ): Response {
        $routeName = $request->route()?->getName();

        if (
            $routeName !== 'roadmap.material'
            && $routeName !== 'projects.show'
        ) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $user->loadMissing('targetCareer');

        $career = $user->targetCareer;

        abort_unless($career !== null, 404);

        $programName = (string) $career->name;

        $program = AcademicProgramCatalog::program(
            $programName,
        );

        abort_unless($program !== null, 404);

        if ($routeName === 'roadmap.material') {
            $material = $request->route('material');

            abort_unless(
                $material instanceof LearningMaterial,
                404,
            );

            $material->loadMissing('skill');

            abort_unless(
                $material->skill !== null,
                404,
            );

            $allowedSkills = AcademicProgramCatalog::skillSlugs(
                $programName,
            );

            abort_unless(
                in_array(
                    (string) $material->skill->slug,
                    $allowedSkills,
                    true,
                ),
                404,
            );

            abort_unless(
                $material->is_active,
                404,
            );

            $belongsToCurrentRoadmap = Roadmap::query()
                ->where('user_id', $user->id)
                ->where('career_id', $career->id)
                ->where('is_active', true)
                ->whereHas(
                    'items',
                    fn ($query) => $query
                        ->where(
                            'learning_material_id',
                            $material->id,
                        ),
                )
                ->exists();

            abort_unless(
                $belongsToCurrentRoadmap,
                404,
            );
        }

        if ($routeName === 'projects.show') {
            $project = $request->route('portfolioProject');

            abort_unless(
                $project instanceof PortfolioProject,
                404,
            );

            abort_unless(
                (int) $project->career_id === (int) $career->id,
                404,
            );

            $project->loadMissing('skills');

            $actualSkills = $project
                ->skills
                ->pluck('slug')
                ->sort()
                ->values()
                ->all();

            $matchesArea = false;

            foreach ($program['areas'] as $area) {
                $expectedSkills = AcademicProgramCatalog::areaSkillSlugs(
                    $programName,
                    (string) $area['name'],
                );

                sort($expectedSkills);

                if (
                    count($expectedSkills) === 3
                    && $actualSkills === $expectedSkills
                ) {
                    $matchesArea = true;

                    break;
                }
            }

            abort_unless(
                $matchesArea,
                404,
            );
        }

        return $next($request);
    }
}
