<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\SessionHeartbeatController;
use App\Http\Controllers\SkillGapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('home');

Route::get('/tentang', [PublicPageController::class, 'about'])
    ->name('about');

Route::get('/karier', [PublicPageController::class, 'careers'])
    ->name('careers.public');

Route::get('/karier/{career}', [PublicPageController::class, 'career'])
    ->name('careers.public.show');

Route::middleware(['auth', 'idle'])->group(function () {
    Route::get(
        '/session/heartbeat',
        SessionHeartbeatController::class,
    )->name('session.heartbeat');
});

Route::middleware(['auth', 'verified', 'idle'])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    Route::get('/onboarding', [OnboardingController::class, 'show'])
        ->name('onboarding.show');

    Route::put('/onboarding', [OnboardingController::class, 'update'])
        ->name('onboarding.update');

    Route::get('/assessment', [AssessmentController::class, 'show'])
        ->name('assessment.show');

    Route::post('/assessment', [AssessmentController::class, 'submit'])
        ->name('assessment.submit');

    Route::get('/skills', [SkillGapController::class, 'index'])
        ->name('skills.index');

    Route::get('/roadmap', [RoadmapController::class, 'index'])
        ->name('roadmap.index');

    Route::get(
        '/roadmap/materials/{material}',
        [RoadmapController::class, 'material'],
    )->name('roadmap.material');

    Route::patch(
        '/roadmap/items/{roadmapItem}/progress',
        [RoadmapController::class, 'logProgress'],
    )->name('roadmap.progress');

    Route::post(
        '/roadmap/items/{roadmapItem}/evaluate',
        [RoadmapController::class, 'evaluate'],
    )->name('roadmap.evaluate');

    Route::get('/projects', [ProjectController::class, 'index'])
        ->name('projects.index');

    Route::get(
        '/projects/{portfolioProject}',
        [ProjectController::class, 'show'],
    )->name('projects.show');

    Route::post(
        '/projects/{portfolioProject}/start',
        [ProjectController::class, 'start'],
    )->name('projects.start');

    Route::patch(
        '/projects/{portfolioProject}',
        [ProjectController::class, 'update'],
    )->name('projects.update');

    Route::get('/progress', [ProgressController::class, 'index'])
        ->name('progress.index');

    Route::prefix('admin')
        ->middleware('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/', [AdminController::class, 'index'])
                ->name('index');

            Route::post(
                '/careers',
                [AdminController::class, 'storeCareer'],
            )->name('careers.store');

            Route::put(
                '/careers/{career}',
                [AdminController::class, 'updateCareer'],
            )->name('careers.update');

            Route::delete(
                '/careers/{career}',
                [AdminController::class, 'destroyCareer'],
            )->name('careers.destroy');

            Route::post(
                '/careers/{career}/skills',
                [AdminController::class, 'attachCareerSkill'],
            )->name('careers.skills.store');

            Route::delete(
                '/careers/{career}/skills/{skill}',
                [AdminController::class, 'removeCareerSkill'],
            )->name('careers.skills.destroy');

            Route::post(
                '/skills',
                [AdminController::class, 'storeSkill'],
            )->name('skills.store');

            Route::put(
                '/skills/{skill}',
                [AdminController::class, 'updateSkill'],
            )->name('skills.update');

            Route::delete(
                '/skills/{skill}',
                [AdminController::class, 'destroySkill'],
            )->name('skills.destroy');

            Route::post(
                '/prerequisites',
                [AdminController::class, 'storePrerequisite'],
            )->name('prerequisites.store');

            Route::delete(
                '/prerequisites/{id}',
                [AdminController::class, 'destroyPrerequisite'],
            )->name('prerequisites.destroy');

            Route::post(
                '/assessments',
                [AdminController::class, 'storeAssessment'],
            )->name('assessments.store');

            Route::put(
                '/assessments/{assessment}',
                [AdminController::class, 'updateAssessment'],
            )->name('assessments.update');

            Route::delete(
                '/assessments/{assessment}',
                [AdminController::class, 'destroyAssessment'],
            )->name('assessments.destroy');

            Route::post(
                '/questions',
                [AdminController::class, 'storeQuestion'],
            )->name('questions.store');

            Route::put(
                '/questions/{question}',
                [AdminController::class, 'updateQuestion'],
            )->name('questions.update');

            Route::delete(
                '/questions/{question}',
                [AdminController::class, 'destroyQuestion'],
            )->name('questions.destroy');

            Route::post(
                '/materials',
                [AdminController::class, 'storeMaterial'],
            )->name('materials.store');

            Route::put(
                '/materials/{learningMaterial}',
                [AdminController::class, 'updateMaterial'],
            )->name('materials.update');

            Route::delete(
                '/materials/{learningMaterial}',
                [AdminController::class, 'destroyMaterial'],
            )->name('materials.destroy');

            Route::post(
                '/projects',
                [AdminController::class, 'storeProject'],
            )->name('projects.store');

            Route::put(
                '/projects/{portfolioProject}',
                [AdminController::class, 'updateProject'],
            )->name('projects.update');

            Route::delete(
                '/projects/{portfolioProject}',
                [AdminController::class, 'destroyProject'],
            )->name('projects.destroy');

            Route::post(
                '/projects/{portfolioProject}/skills',
                [AdminController::class, 'attachProjectSkill'],
            )->name('projects.skills.store');

            Route::delete(
                '/projects/{portfolioProject}/skills/{skill}',
                [AdminController::class, 'removeProjectSkill'],
            )->name('projects.skills.destroy');
        });
});

require __DIR__.'/settings.php';
