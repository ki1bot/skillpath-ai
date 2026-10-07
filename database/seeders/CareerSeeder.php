<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Support\AcademicProgramCatalog;
use Illuminate\Database\Seeder;

class CareerSeeder extends Seeder
{
    public function run(): void
    {
        foreach (
            AcademicProgramCatalog::programs() as $name => $program
        ) {
            $career = Career::firstOrCreate(
                [
                    'slug' => $program['slug'],
                ],
                [
                    'name' => $name,
                    'tagline' => $program['tagline'],
                    'description' => $program['description'],
                    'responsibilities' => array_map(
                        fn (array $area): string => $area['name'],
                        $program['areas'],
                    ),
                    'difficulty' => 'Lintas tahap',
                    'accent' => $program['accent'],
                    'is_active' => true,
                ],
            );

            if ($career->difficulty !== 'Lintas tahap') {
                $career->update([
                    'difficulty' => 'Lintas tahap',
                ]);
            }
        }
    }
}
