<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Skill;
use App\Models\User;
use App\Support\AcademicProgramCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/index', [
            'stats' => [
                'users' => User::query()
                    ->where('role', 'student')
                    ->count(),
                'careers' => Career::query()->count(),
                'skills' => Skill::query()->count(),
                'materials' => LearningMaterial::query()
                    ->where('is_active', true)
                    ->count(),
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
        $data = $this->careerData($request);

        if (AcademicProgramCatalog::program($data['name']) !== null) {
            throw ValidationException::withMessages([
                'name' => 'Jurusan akademik ini sudah ditentukan melalui katalog SkillPath.',
            ]);
        }

        Career::create([
            ...$data,
            'slug' => $this->uniqueSlug(
                Career::class,
                $data['name'],
            ),
        ]);

        return back()->with(
            'success',
            'Jurusan berhasil ditambahkan.',
        );
    }

    public function updateCareer(
        Request $request,
        Career $career,
    ): RedirectResponse {
        $data = $this->careerData($request);

        if (
            $this->isAcademicCareer($career)
            && $data['name'] !== $career->name
        ) {
            throw ValidationException::withMessages([
                'name' => 'Nama jurusan akademik tidak dapat diubah karena digunakan oleh Assessment, roadmap, dan proyek.',
            ]);
        }

        if (
            $data['name'] !== $career->name
            && AcademicProgramCatalog::program($data['name']) !== null
        ) {
            throw ValidationException::withMessages([
                'name' => 'Nama ini sudah digunakan oleh katalog akademik.',
            ]);
        }

        $career->update([
            ...$data,
            'slug' => $this->isAcademicCareer($career)
                ? $career->slug
                : $this->uniqueSlug(
                    Career::class,
                    $data['name'],
                    $career->id,
                ),
        ]);

        return back()->with(
            'success',
            'Jurusan berhasil diperbarui.',
        );
    }

    public function destroyCareer(Career $career): RedirectResponse
    {
        if ($this->isAcademicCareer($career)) {
            throw ValidationException::withMessages([
                'career' => 'Jurusan akademik tidak dapat dihapus karena menjadi bagian dari katalog pembelajaran.',
            ]);
        }

        $career->delete();

        return back()->with(
            'success',
            'Jurusan dihapus.',
        );
    }

    public function attachCareerSkill(
        Request $request,
        Career $career,
    ): RedirectResponse {
        if ($this->isAcademicCareer($career)) {
            throw ValidationException::withMessages([
                'skill_id' => 'Hubungan sembilan kemampuan jurusan akademik sudah ditentukan oleh katalog.',
            ]);
        }

        $data = $request->validate([
            'skill_id' => [
                'required',
                'integer',
                'exists:skills,id',
            ],
            'target_level' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],
            'importance_weight' => [
                'required',
                'numeric',
                'min:0.1',
                'max:3',
            ],
            'is_required' => [
                'required',
                'boolean',
            ],
        ]);

        $career->skills()->syncWithoutDetaching([
            $data['skill_id'] => [
                'target_level' => $data['target_level'],
                'importance_weight' => $data['importance_weight'],
                'is_required' => $data['is_required'],
            ],
        ]);

        return back()->with(
            'success',
            'Standar skill jurusan disimpan.',
        );
    }

    public function removeCareerSkill(
        Career $career,
        Skill $skill,
    ): RedirectResponse {
        if ($this->isAcademicCareer($career)) {
            throw ValidationException::withMessages([
                'skill_id' => 'Kemampuan wajib tidak dapat dilepas dari jurusan akademik.',
            ]);
        }

        $career->skills()->detach($skill->id);

        return back()->with(
            'success',
            'Skill dilepas dari jurusan.',
        );
    }

    public function storeSkill(Request $request): RedirectResponse
    {
        $data = $this->skillData($request);

        Skill::create([
            ...$data,
            'slug' => $this->uniqueSlug(
                Skill::class,
                $data['name'],
            ),
        ]);

        return back()->with(
            'success',
            'Skill berhasil ditambahkan.',
        );
    }

    public function updateSkill(
        Request $request,
        Skill $skill,
    ): RedirectResponse {
        $data = $this->skillData($request);

        if (
            $this->isAcademicSkill($skill)
            && $data['name'] !== $skill->name
        ) {
            throw ValidationException::withMessages([
                'name' => 'Nama kemampuan akademik sudah digunakan oleh katalog jurusan dan tidak dapat diganti.',
            ]);
        }

        $skill->update([
            ...$data,
            'slug' => $this->isAcademicSkill($skill)
                ? $skill->slug
                : $this->uniqueSlug(
                    Skill::class,
                    $data['name'],
                    $skill->id,
                ),
        ]);

        return back()->with(
            'success',
            'Skill berhasil diperbarui.',
        );
    }

    public function destroySkill(Skill $skill): RedirectResponse
    {
        if ($this->isAcademicSkill($skill)) {
            throw ValidationException::withMessages([
                'skill' => 'Kemampuan akademik tidak dapat dihapus karena digunakan dalam 27 materi dan tiga proyek jurusan.',
            ]);
        }

        if ($skill->materials()->exists()) {
            throw ValidationException::withMessages([
                'skill' => 'Kemampuan ini masih mempunyai materi. Periksa hubungan datanya sebelum menghapus.',
            ]);
        }

        $skill->delete();

        return back()->with(
            'success',
            'Skill dihapus.',
        );
    }

    public function storePrerequisite(
        Request $request,
    ): RedirectResponse {
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

        return back()->with(
            'success',
            'Relasi prasyarat disimpan.',
        );
    }

    public function destroyPrerequisite(int $id): RedirectResponse
    {
        DB::table('skill_prerequisites')
            ->where('id', $id)
            ->delete();

        return back()->with(
            'success',
            'Relasi prasyarat dihapus.',
        );
    }

    public function storeAssessment(
        Request $request,
    ): RedirectResponse {
        $data = $this->assessmentData($request);

        Assessment::create($data);

        return back()->with(
            'success',
            'Assessment berhasil ditambahkan.',
        );
    }

    public function updateAssessment(
        Request $request,
        Assessment $assessment,
    ): RedirectResponse {
        $data = $this->assessmentData($request);

        $assessment->update($data);

        return back()->with(
            'success',
            'Assessment berhasil diperbarui.',
        );
    }

    public function destroyAssessment(
        Assessment $assessment,
    ): RedirectResponse {
        $assessment->delete();

        return back()->with(
            'success',
            'Assessment dihapus.',
        );
    }

    public function storeQuestion(
        Request $request,
    ): RedirectResponse {
        AssessmentQuestion::create(
            $this->questionData($request),
        );

        return back()->with(
            'success',
            'Soal Assessment berhasil ditambahkan.',
        );
    }

    public function updateQuestion(
        Request $request,
        AssessmentQuestion $question,
    ): RedirectResponse {
        $question->update(
            $this->questionData($request),
        );

        return back()->with(
            'success',
            'Soal Assessment berhasil diperbarui.',
        );
    }

    public function destroyQuestion(
        AssessmentQuestion $question,
    ): RedirectResponse {
        $question->delete();

        return back()->with(
            'success',
            'Soal Assessment dihapus.',
        );
    }

    public function storeMaterial(
        Request $request,
    ): RedirectResponse {
        $data = $this->materialData($request);

        $skill = Skill::query()
            ->whereKey($data['skill_id'])
            ->firstOrFail();

        if ($this->isAcademicSkill($skill)) {
            throw ValidationException::withMessages([
                'skill_id' => 'Setiap kemampuan akademik sudah mempunyai materi Amatir, Menengah, dan Ahli. Pilih materi yang tersedia untuk memperbarui tugasnya.',
            ]);
        }

        LearningMaterial::create([
            ...$data,
            'slug' => $this->uniqueSlug(
                LearningMaterial::class,
                $data['title'],
            ),
            'material_type' => 'core',
            'reinforcement_for_material_id' => null,
            'is_active' => true,
            'quiz_question' => 'Penilaian dilakukan melalui tugas praktik.',
            'quiz_options' => [
                'A' => 'Tidak digunakan',
                'B' => 'Tidak digunakan',
                'C' => 'Tidak digunakan',
                'D' => 'Tidak digunakan',
            ],
            'quiz_answer' => 'A',
            'quiz_explanation' => null,
        ]);

        return back()->with(
            'success',
            'Materi berhasil ditambahkan.',
        );
    }

    public function updateMaterial(
        Request $request,
        LearningMaterial $learningMaterial,
    ): RedirectResponse {
        $data = $this->materialData($request);

        $isAcademicMaterial = $this->isAcademicMaterial(
            $learningMaterial,
        );

        if ($isAcademicMaterial) {
            if (
                (int) $data['skill_id']
                !== (int) $learningMaterial->skill_id
            ) {
                throw ValidationException::withMessages([
                    'skill_id' => 'Kemampuan materi akademik tidak dapat dipindahkan.',
                ]);
            }

            if (
                $data['difficulty']
                !== $learningMaterial->difficulty
            ) {
                throw ValidationException::withMessages([
                    'difficulty' => 'Tahap materi akademik tidak dapat dipindahkan. Silakan edit tugas pada materi dari tahap yang ingin diperbarui.',
                ]);
            }
        }

        if (
            ! $isAcademicMaterial
            && $learningMaterial->roadmapItems()->exists()
            && (
                (int) $data['skill_id']
                    !== (int) $learningMaterial->skill_id
                || $data['difficulty']
                    !== $learningMaterial->difficulty
            )
        ) {
            throw ValidationException::withMessages([
                'difficulty' => 'Kemampuan dan tahap materi yang sudah digunakan pada roadmap tidak dapat diubah.',
            ]);
        }

        $learningMaterial->update($data);

        return back()->with(
            'success',
            'Materi berhasil diperbarui.',
        );
    }

    public function destroyMaterial(
        LearningMaterial $learningMaterial,
    ): RedirectResponse {
        if ($this->isAcademicMaterial($learningMaterial)) {
            throw ValidationException::withMessages([
                'material' => 'Materi akademik tidak dapat dihapus karena dibutuhkan untuk membentuk roadmap.',
            ]);
        }

        if (
            $learningMaterial->roadmapItems()->exists()
            || $learningMaterial->reinforcementMaterials()->exists()
        ) {
            throw ValidationException::withMessages([
                'material' => 'Materi masih digunakan oleh roadmap atau materi penguatan.',
            ]);
        }

        $learningMaterial->delete();

        return back()->with(
            'success',
            'Materi dihapus.',
        );
    }

    public function storeProject(
        Request $request,
    ): RedirectResponse {
        $data = $this->projectData($request);

        $career = Career::query()
            ->whereKey($data['career_id'])
            ->firstOrFail();

        if ($this->isAcademicCareer($career)) {
            throw ValidationException::withMessages([
                'career_id' => 'Tiga proyek jurusan akademik sudah tersedia. Perbarui proyek yang sesuai dengan bidangnya.',
            ]);
        }

        PortfolioProject::create([
            ...$data,
            'slug' => $this->uniqueSlug(
                PortfolioProject::class,
                $data['title'],
            ),
        ]);

        return back()->with(
            'success',
            'Proyek berhasil ditambahkan.',
        );
    }

    public function updateProject(
        Request $request,
        PortfolioProject $portfolioProject,
    ): RedirectResponse {
        $data = $this->projectData($request);

        $newCareer = Career::query()
            ->whereKey($data['career_id'])
            ->firstOrFail();

        if (
            $this->isAcademicProject($portfolioProject)
            && (int) $data['career_id']
                !== (int) $portfolioProject->career_id
        ) {
            throw ValidationException::withMessages([
                'career_id' => 'Jurusan proyek akademik tidak dapat dipindahkan.',
            ]);
        }

        if (
            ! $this->isAcademicProject($portfolioProject)
            && $this->isAcademicCareer($newCareer)
        ) {
            throw ValidationException::withMessages([
                'career_id' => 'Proyek tambahan tidak dapat dimasukkan ke dalam jurusan akademik yang sudah mempunyai tiga proyek.',
            ]);
        }

        $portfolioProject->update([
            ...$data,
            'slug' => $portfolioProject->slug,
        ]);

        return back()->with(
            'success',
            'Proyek berhasil diperbarui.',
        );
    }

    public function destroyProject(
        PortfolioProject $portfolioProject,
    ): RedirectResponse {
        if ($this->isAcademicProject($portfolioProject)) {
            throw ValidationException::withMessages([
                'project' => 'Proyek akademik tidak dapat dihapus karena setiap jurusan harus mempunyai tiga proyek.',
            ]);
        }

        if ($portfolioProject->userProjects()->exists()) {
            throw ValidationException::withMessages([
                'project' => 'Proyek ini mempunyai riwayat pengerjaan mahasiswa dan tidak dapat dihapus.',
            ]);
        }

        $portfolioProject->delete();

        return back()->with(
            'success',
            'Proyek dihapus.',
        );
    }

    public function attachProjectSkill(
        Request $request,
        PortfolioProject $portfolioProject,
    ): RedirectResponse {
        if ($this->isAcademicProject($portfolioProject)) {
            throw ValidationException::withMessages([
                'skill_id' => 'Hubungan tiga kemampuan proyek akademik sudah ditentukan berdasarkan bidang jurusan.',
            ]);
        }

        $data = $request->validate([
            'skill_id' => [
                'required',
                'integer',
                'exists:skills,id',
            ],
            'required_level' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],
            'weight' => [
                'required',
                'numeric',
                'min:0.1',
                'max:3',
            ],
        ]);

        $portfolioProject->skills()->syncWithoutDetaching([
            $data['skill_id'] => [
                'required_level' => $data['required_level'],
                'weight' => $data['weight'],
            ],
        ]);

        return back()->with(
            'success',
            'Kebutuhan skill proyek disimpan.',
        );
    }

    public function removeProjectSkill(
        PortfolioProject $portfolioProject,
        Skill $skill,
    ): RedirectResponse {
        if ($this->isAcademicProject($portfolioProject)) {
            throw ValidationException::withMessages([
                'skill_id' => 'Kemampuan wajib proyek akademik tidak dapat dilepas.',
            ]);
        }

        $portfolioProject->skills()->detach($skill->id);

        return back()->with(
            'success',
            'Kebutuhan skill proyek dihapus.',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function careerData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'tagline' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:4000'],
            'responsibilities' => [
                'required',
                'array',
                'min:1',
            ],
            'responsibilities.*' => [
                'required',
                'string',
                'max:255',
            ],
            'difficulty' => [
                'required',
                'string',
                'max:50',
            ],
            'accent' => [
                'required',
                'string',
                'max:20',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function skillData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => [
                'required',
                'string',
                'max:120',
            ],
            'description' => [
                'required',
                'string',
                'max:2000',
            ],
            'difficulty' => [
                'required',
                'string',
                'max:50',
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function assessmentData(Request $request): array
    {
        return $request->validate([
            'career_id' => [
                'required',
                'integer',
                'exists:careers,id',
            ],
            'title' => [
                'required',
                'string',
                'max:180',
            ],
            'description' => [
                'required',
                'string',
                'max:2000',
            ],
            'duration_minutes' => [
                'required',
                'integer',
                'min:5',
                'max:180',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);
    }

    private function questionData(Request $request): array
    {
        $data = $request->validate([
            'assessment_id' => [
                'required',
                'integer',
                'exists:assessments,id',
            ],
            'skill_id' => [
                'required',
                'integer',
                'exists:skills,id',
            ],
            'prompt' => [
                'required',
                'string',
                'max:2000',
            ],
            'options' => [
                'required',
                'array',
                'size:4',
            ],
            'options.*' => [
                'required',
                'string',
                'max:500',
            ],
            'correct_answer' => [
                'required',
                'in:A,B,C,D',
            ],
            'explanation' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'difficulty' => [
                'required',
                'string',
                'max:50',
            ],
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
        return $request->validate([
            'skill_id' => [
                'required',
                'integer',
                'exists:skills,id',
            ],
            'title' => [
                'required',
                'string',
                'max:180',
            ],
            'summary' => [
                'required',
                'string',
                'max:3000',
            ],
            'learning_objectives' => [
                'required',
                'array',
                'min:1',
            ],
            'learning_objectives.*' => [
                'required',
                'string',
                'max:500',
            ],
            'difficulty' => [
                'required',
                'in:Amatir,Menengah,Ahli',
            ],
            'estimated_minutes' => [
                'required',
                'integer',
                'min:15',
                'max:3000',
            ],
            'resource_title' => [
                'nullable',
                'string',
                'max:180',
            ],
            'resource_url' => [
                'nullable',
                'url',
                'max:1000',
            ],
            'practice_task' => [
                'required',
                'string',
                'max:4000',
            ],
        ]);
    }

    private function projectData(Request $request): array
    {
        $stretchFeatures = array_values(
            array_filter(
                (array) $request->input(
                    'stretch_features',
                    [],
                ),
                fn ($value): bool => is_string($value)
                    && trim($value) !== '',
            ),
        );

        $request->merge([
            'stretch_features' => $stretchFeatures,
        ]);

        return $request->validate([
            'career_id' => [
                'required',
                'integer',
                'exists:careers,id',
            ],
            'title' => [
                'required',
                'string',
                'max:180',
            ],
            'summary' => [
                'required',
                'string',
                'max:3000',
            ],
            'problem_statement' => [
                'required',
                'string',
                'max:4000',
            ],
            'difficulty' => [
                'required',
                'string',
                'max:50',
            ],
            'minimum_features' => [
                'required',
                'array',
                'min:1',
            ],
            'minimum_features.*' => [
                'required',
                'string',
                'max:500',
            ],
            'stretch_features' => [
                'nullable',
                'array',
            ],
            'stretch_features.*' => [
                'required',
                'string',
                'max:500',
            ],
            'completion_criteria' => [
                'required',
                'array',
                'min:1',
            ],
            'completion_criteria.*' => [
                'required',
                'string',
                'max:500',
            ],
            'estimated_hours' => [
                'required',
                'integer',
                'min:1',
                'max:500',
            ],
        ]);
    }

    private function isAcademicCareer(Career $career): bool
    {
        return AcademicProgramCatalog::program(
            $career->name,
        ) !== null;
    }

    private function isAcademicSkill(Skill $skill): bool
    {
        return in_array(
            $skill->slug,
            AcademicProgramCatalog::allSkillSlugs(),
            true,
        );
    }

    private function isAcademicMaterial(
        LearningMaterial $material,
    ): bool {
        if (
            preg_match(
                '/^(belajar|penguatan)-(amatir|menengah|ahli)-/',
                $material->slug,
            ) !== 1
        ) {
            return false;
        }

        $skill = Skill::query()
            ->find($material->skill_id);

        return $skill !== null
            && $this->isAcademicSkill($skill);
    }

    private function isAcademicProject(
        PortfolioProject $project,
    ): bool {
        $career = Career::query()
            ->find($project->career_id);

        return $career !== null
            && $this->isAcademicCareer($career);
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
                    fn ($query) => $query->where(
                        'id',
                        '!=',
                        $ignoreId,
                    ),
                )
                ->exists()
        ) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
