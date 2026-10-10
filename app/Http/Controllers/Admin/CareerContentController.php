<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Support\AcademicProgramCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CareerContentController extends Controller
{
    public function update(Request $request, Career $career): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'display_name' => ['nullable', 'string', 'max:120'],
            'tagline' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:4000'],
            'responsibilities' => ['required', 'array', 'min:1', 'max:12'],
            'responsibilities.*' => ['required', 'string', 'max:255'],
            'difficulty' => ['required', 'string', 'max:50'],
            'accent' => [
                'required',
                'string',
                'regex:/^#[A-Fa-f0-9]{6}$/',
            ],
            'is_active' => ['required', 'boolean'],
        ]);

        $program = AcademicProgramCatalog::program($career->name);

        $isAcademic = $program !== null
            && $program['slug'] === $career->slug;

        if ($isAcademic) {
            if ($data['name'] !== $career->name) {
                throw ValidationException::withMessages([
                    'name' => 'Identitas jurusan akademik tidak boleh diubah. Gunakan nama tampilan.',
                ]);
            }

            if (count($data['responsibilities']) !== 3) {
                throw ValidationException::withMessages([
                    'responsibilities' => 'Jurusan akademik harus mempertahankan ketiga bidangnya.',
                ]);
            }

            if (! (bool) $data['is_active']) {
                throw ValidationException::withMessages([
                    'is_active' => 'Jurusan akademik tidak dapat dinonaktifkan karena masih digunakan dalam pembelajaran.',
                ]);
            }
        } else {
            if (AcademicProgramCatalog::program($data['name']) !== null) {
                throw ValidationException::withMessages([
                    'name' => 'Nama tersebut digunakan oleh katalog akademik.',
                ]);
            }

            if (
                Career::query()
                    ->where('name', $data['name'])
                    ->where('id', '!=', $career->id)
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'name' => 'Nama jurusan sudah digunakan.',
                ]);
            }
        }

        $areas = array_map(
            static fn (string $value): string => trim($value),
            $data['responsibilities'],
        );

        if (
            in_array('', $areas, true)
            || count(
                array_unique(
                    array_map('mb_strtolower', $areas),
                ),
            ) !== count($areas)
        ) {
            throw ValidationException::withMessages([
                'responsibilities' => 'Nama bidang tidak boleh kosong atau sama dengan bidang lainnya.',
            ]);
        }

        $alias = trim((string) ($data['display_name'] ?? ''));

        $career->update([
            'name' => $isAcademic ? $career->name : $data['name'],
            'display_name' => $alias !== ''
                && $alias !== $data['name']
                    ? $alias
                    : null,
            'tagline' => $data['tagline'],
            'description' => $data['description'],
            'responsibilities' => $areas,
            'difficulty' => 'Lintas tahap',
            'accent' => $data['accent'],
            'is_active' => (bool) $data['is_active'],
        ]);

        return back()->with(
            'success',
            'Data jurusan dan bidang berhasil diperbarui.',
        );
    }

    public function destroy(Career $career): RedirectResponse
    {
        $program = AcademicProgramCatalog::program($career->name);

        if (
            $program !== null
            && $program['slug'] === $career->slug
        ) {
            throw ValidationException::withMessages([
                'career' => 'Jurusan inti tidak boleh dihapus karena terhubung dengan Assessment, roadmap, dan proyek.',
            ]);
        }

        if (
            $career->users()->exists()
            || $career->assessments()->exists()
            || $career->projects()->exists()
            || DB::table('roadmaps')
                ->where('career_id', $career->id)
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'career' => 'Jurusan masih digunakan oleh mahasiswa atau data pembelajaran dan tidak dapat dihapus.',
            ]);
        }

        DB::transaction(static function () use ($career): void {
            $career->skills()->detach();

            $career->delete();
        });

        return back()->with(
            'success',
            'Jurusan tambahan berhasil dihapus.',
        );
    }
}
