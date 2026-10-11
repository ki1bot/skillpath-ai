<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Skill;
use App\Support\AcademicProgramCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SkillContentController extends AdminController
{
    public function update(
        Request $request,
        Skill $skill,
    ): RedirectResponse {
        $isAcademicSkill = in_array(
            $skill->slug,
            AcademicProgramCatalog::allSkillSlugs(),
            true,
        );

        if (! $isAcademicSkill) {
            return parent::updateSkill($request, $skill);
        }

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
            ],
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

        $newName = trim($data['name']);

        $skill->update([
            'display_name' => $newName === $skill->name
                ? null
                : $newName,
            'category' => $data['category'],
            'description' => $data['description'],
            'difficulty' => $data['difficulty'],
        ]);

        return back()->with(
            'success',
            'Nama dan informasi kemampuan berhasil diperbarui.',
        );
    }
}
