<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/index', [
            'stats' => [
                'users' => User::query()->where('role', 'student')->count(),
                'careers' => Career::query()->count(),
                'skills' => Skill::query()->count(),
                'materials' => LearningMaterial::query()->count(),
                'projects' => PortfolioProject::query()->count(),
                'assessmentAttempts' => DB::table('assessment_results')
                    ->distinct('attempt_uuid')
                    ->count('attempt_uuid'),
            ],
            'careers' => Career::query()
                ->with('skills')
                ->orderBy('name')
                ->get(),
            'skills' => Skill::query()
                ->with('prerequisites')
                ->orderBy('name')
                ->get(),
            'prerequisites' => DB::table('skill_prerequisites')
                ->join(
                    'skills as skill',
                    'skill.id',
                    '=',
                    'skill_prerequisites.skill_id',
                )
                ->join(
                    'skills as prerequisite',
                    'prerequisite.id',
                    '=',
                    'skill_prerequisites.prerequisite_skill_id',
                )
                ->select([
                    'skill_prerequisites.id',
                    'skill_prerequisites.factor',
                    'skill.name as skill_name',
                    'prerequisite.name as prerequisite_name',
                ])
                ->orderBy('skill.name')
                ->get(),
            'assessments' => Assessment::query()
                ->with(['career', 'questions.skill'])
                ->orderBy('title')
                ->get(),
            'materials' => LearningMaterial::query()
                ->with('skill')
                ->orderBy('title')
                ->get(),
            'projects' => PortfolioProject::query()
                ->with(['career', 'skills'])
                ->orderBy('title')
                ->get(),
        ]);
    }

    public function storeCareer(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'tagline' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:4000'],
            'responsibilities' => ['required', 'array', 'min:1'],
            'responsibilities.*' => ['required', 'string', 'max:255'],
            'difficulty' => ['required', 'string', 'max:50'],
            'accent' => ['required', 'string', 'max:20'],
            'is_active' => ['required', 'boolean'],
        ]);

        Career::create([
            ...$data,
            'slug' => $this->uniqueSlug(
                Career::class,
                $data['name'],
            ),
        ]);

        return back()->with('success', 'Karier berhasil ditambahkan.');
    }

    public function updateCareer(
        Request $request,
        Career $career,
    ): RedirectResponse {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'tagline' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:4000'],
            'responsibilities' => ['required', 'array', 'min:1'],
            'responsibilities.*' => ['required', 'string', 'max:255'],
            'difficulty' => ['required', 'string', 'max:50'],
            'accent' => ['required', 'string', 'max:20'],
            'is_active' => ['required', 'boolean'],
        ]);

        $career->update([
            ...$data,
            'slug' => $this->uniqueSlug(
                Career::class,
                $data['name'],
                $career->id,
            ),
        ]);

        return back()->with('success', 'Karier berhasil diperbarui.');
    }

    public function destroyCareer(Career $career): RedirectResponse
    {
        $career->delete();

        return back()->with('success', 'Karier dihapus.');
    }

    public function attachCareerSkill(
        Request $request,
        Career $career,
    ): RedirectResponse {
        $data = $request->validate([
            'skill_id' => ['required', 'integer', 'exists:skills,id'],
            'target_level' => ['required', 'integer', 'min:1', 'max:100'],
            'importance_weight' => ['required', 'numeric', 'min:0.1', 'max:3'],
            'is_required' => ['required', 'boolean'],
        ]);

        $career->skills()->syncWithoutDetaching([
            $data['skill_id'] => [
                'target_level' => $data['target_level'],
                'importance_weight' => $data['importance_weight'],
                'is_required' => $data['is_required'],
            ],
        ]);

        return back()->with('success', 'Standar skill karier disimpan.');
    }

    public function removeCareerSkill(
        Career $career,
        Skill $skill,
    ): RedirectResponse {
        $career->skills()->detach($skill->id);

        return back()->with('success', 'Skill dilepas dari karier.');
    }

    public function storeSkill(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:2000'],
            'difficulty' => ['required', 'string', 'max:50'],
        ]);

        Skill::create([
            ...$data,
            'slug' => $this->uniqueSlug(
                Skill::class,
                $data['name'],
            ),
        ]);

        return back()->with('success', 'Skill berhasil ditambahkan.');
    }

    public function updateSkill(
        Request $request,
        Skill $skill,
    ): RedirectResponse {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:2000'],
            'difficulty' => ['required', 'string', 'max:50'],
        ]);

        $skill->update([
            ...$data,
            'slug' => $this->uniqueSlug(
                Skill::class,
                $data['name'],
                $skill->id,
            ),
        ]);

        return back()->with('success', 'Skill berhasil diperbarui.');
    }

    public function destroySkill(Skill $skill): RedirectResponse
    {
        $skill->delete();

        return back()->with('success', 'Skill dihapus.');
    }

    public function storePrerequisite(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'skill_id' => [
                'required',
                'integer',
                'exists:skills,id',
                'different:prerequisite_skill_id',
            ],
            'prerequisite_skill_id' => [
                'required',
                'integer',
                'exists:skills,id',
            ],
            'factor' => [
                'required',
                'numeric',
                'min:1',
                'max:2',
            ],
        ]);

        DB::table('skill_prerequisites')->updateOrInsert(
            [
                'skill_id' => $data['skill_id'],
                'prerequisite_skill_id' => $data['prerequisite_skill_id'],
            ],
            [
                'factor' => $data['factor'],
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );

        return back()->with('success', 'Relasi prasyarat disimpan.');
    }

    public function destroyPrerequisite(int $id): RedirectResponse
    {
        DB::table('skill_prerequisites')
            ->where('id', $id)
            ->delete();

        return back()->with('success', 'Relasi prasyarat dihapus.');
    }

    public function storeAssessment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'career_id' => ['required', 'integer', 'exists:careers,id'],
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:2000'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:180'],
            'is_active' => ['required', 'boolean'],
        ]);

        Assessment::create($data);

        return back()->with('success', 'Asesmen berhasil ditambahkan.');
    }

    public function updateAssessment(
        Request $request,
        Assessment $assessment,
    ): RedirectResponse {
        $data = $request->validate([
            'career_id' => ['required', 'integer', 'exists:careers,id'],
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:2000'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:180'],
            'is_active' => ['required', 'boolean'],
        ]);

        $assessment->update($data);

        return back()->with('success', 'Asesmen berhasil diperbarui.');
    }

    public function destroyAssessment(
        Assessment $assessment,
    ): RedirectResponse {
        $assessment->delete();

        return back()->with('success', 'Asesmen dihapus.');
    }

    public function storeQuestion(Request $request): RedirectResponse
    {
        AssessmentQuestion::create(
            $this->questionData($request),
        );

        return back()->with('success', 'Soal asesmen berhasil ditambahkan.');
    }

    public function updateQuestion(
        Request $request,
        AssessmentQuestion $question,
    ): RedirectResponse {
        $question->update(
            $this->questionData($request),
        );

        return back()->with('success', 'Soal asesmen berhasil diperbarui.');
    }

    public function destroyQuestion(
        AssessmentQuestion $question,
    ): RedirectResponse {
        $question->delete();

        return back()->with('success', 'Soal asesmen dihapus.');
    }

    public function storeMaterial(Request $request): RedirectResponse
    {
        $data = $this->materialData($request);

        LearningMaterial::create([
            ...$data,
            'slug' => $this->uniqueSlug(
                LearningMaterial::class,
                $data['title'],
            ),
        ]);

        return back()->with('success', 'Materi berhasil ditambahkan.');
    }

    public function updateMaterial(
        Request $request,
        LearningMaterial $learningMaterial,
    ): RedirectResponse {
        $data = $this->materialData($request);

        $learningMaterial->update([
            ...$data,
            'slug' => $this->uniqueSlug(
                LearningMaterial::class,
                $data['title'],
                $learningMaterial->id,
            ),
        ]);

        return back()->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroyMaterial(
        LearningMaterial $learningMaterial,
    ): RedirectResponse {
        $learningMaterial->delete();

        return back()->with('success', 'Materi dihapus.');
    }

    public function storeProject(Request $request): RedirectResponse
    {
        $data = $this->projectData($request);

        PortfolioProject::create([
            ...$data,
            'slug' => $this->uniqueSlug(
                PortfolioProject::class,
                $data['title'],
            ),
        ]);

        return back()->with('success', 'Proyek berhasil ditambahkan.');
    }

    public function updateProject(
        Request $request,
        PortfolioProject $portfolioProject,
    ): RedirectResponse {
        $data = $this->projectData($request);

        $portfolioProject->update([
            ...$data,
            'slug' => $this->uniqueSlug(
                PortfolioProject::class,
                $data['title'],
                $portfolioProject->id,
            ),
        ]);

        return back()->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroyProject(
        PortfolioProject $portfolioProject,
    ): RedirectResponse {
        $portfolioProject->delete();

        return back()->with('success', 'Proyek dihapus.');
    }

    public function attachProjectSkill(
        Request $request,
        PortfolioProject $portfolioProject,
    ): RedirectResponse {
        $data = $request->validate([
            'skill_id' => ['required', 'integer', 'exists:skills,id'],
            'required_level' => ['required', 'integer', 'min:1', 'max:100'],
            'weight' => ['required', 'numeric', 'min:0.1', 'max:3'],
        ]);

        $portfolioProject->skills()->syncWithoutDetaching([
            $data['skill_id'] => [
                'required_level' => $data['required_level'],
                'weight' => $data['weight'],
            ],
        ]);

        return back()->with('success', 'Kebutuhan skill proyek disimpan.');
    }

    public function removeProjectSkill(
        PortfolioProject $portfolioProject,
        Skill $skill,
    ): RedirectResponse {
        $portfolioProject->skills()->detach($skill->id);

        return back()->with('success', 'Kebutuhan skill proyek dihapus.');
    }

    private function questionData(Request $request): array
    {
        $data = $request->validate([
            'assessment_id' => ['required', 'integer', 'exists:assessments,id'],
            'skill_id' => ['required', 'integer', 'exists:skills,id'],
            'prompt' => ['required', 'string', 'max:2000'],
            'options' => ['required', 'array', 'size:4'],
            'options.*' => ['required', 'string', 'max:500'],
            'correct_answer' => ['required', 'in:A,B,C,D'],
            'explanation' => ['nullable', 'string', 'max:2000'],
            'difficulty' => ['required', 'string', 'max:50'],
        ]);

        $options = array_values($data['options']);

        $data['options'] = [
            'A' => $options[0],
            'B' => $options[1],
            'C' => $options[2],
            'D' => $options[3],
        ];

        return $data;
    }

    private function materialData(Request $request): array
    {
        $data = $request->validate([
            'skill_id' => ['required', 'integer', 'exists:skills,id'],
            'title' => ['required', 'string', 'max:180'],
            'summary' => ['required', 'string', 'max:3000'],
            'learning_objectives' => ['required', 'array', 'min:1'],
            'learning_objectives.*' => ['required', 'string', 'max:500'],
            'difficulty' => ['required', 'string', 'max:50'],
            'estimated_minutes' => [
                'required',
                'integer',
                'min:15',
                'max:3000',
            ],
            'resource_title' => ['nullable', 'string', 'max:180'],
            'resource_url' => ['nullable', 'url', 'max:1000'],
            'practice_task' => ['required', 'string', 'max:4000'],
            'quiz_question' => ['required', 'string', 'max:2000'],
            'quiz_options' => ['required', 'array', 'size:4'],
            'quiz_options.*' => ['required', 'string', 'max:500'],
            'quiz_answer' => ['required', 'in:A,B,C,D'],
            'quiz_explanation' => ['nullable', 'string', 'max:2000'],
        ]);

        $quizOptions = array_values($data['quiz_options']);

        $data['quiz_options'] = [
            'A' => $quizOptions[0],
            'B' => $quizOptions[1],
            'C' => $quizOptions[2],
            'D' => $quizOptions[3],
        ];

        return $data;
    }

    private function projectData(Request $request): array
    {
        return $request->validate([
            'career_id' => ['required', 'integer', 'exists:careers,id'],
            'title' => ['required', 'string', 'max:180'],
            'summary' => ['required', 'string', 'max:3000'],
            'problem_statement' => ['required', 'string', 'max:4000'],
            'difficulty' => ['required', 'string', 'max:50'],
            'minimum_features' => ['required', 'array', 'min:1'],
            'minimum_features.*' => ['required', 'string', 'max:500'],
            'stretch_features' => ['nullable', 'array'],
            'stretch_features.*' => ['required', 'string', 'max:500'],
            'completion_criteria' => ['required', 'array', 'min:1'],
            'completion_criteria.*' => ['required', 'string', 'max:500'],
            'estimated_hours' => ['required', 'integer', 'min:1', 'max:500'],
        ]);
    }

    private function uniqueSlug(
        string $modelClass,
        string $value,
        ?int $ignoreId = null,
    ): string {
        $base = Str::slug($value);
        $slug = $base;
        $counter = 2;

        while (
            $modelClass::query()
                ->where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) => $query->where('id', '!=', $ignoreId),
                )
                ->exists()
        ) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
