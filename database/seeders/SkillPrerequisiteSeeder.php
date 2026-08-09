<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillPrerequisiteSeeder extends Seeder
{
    public function run(): void
    {
        $pairs = [
            ['sql', 'database-fundamentals'],
            ['php-laravel', 'programming-fundamentals'],
            ['rest-api', 'http-fundamentals'],
            ['rest-api', 'php-laravel'],
            ['rest-api', 'eloquent-orm'],
            ['authentication-authorization', 'rest-api'],
            ['eloquent-orm', 'database-fundamentals'],
            ['eloquent-orm', 'php-laravel'],
            ['validation-error-handling', 'rest-api'],
            ['logging-monitoring', 'rest-api'],
            ['web-security-basics', 'authentication-authorization'],
            ['deployment-basics', 'git-github'],
            ['deployment-basics', 'terminal-cli'],
            ['css-responsive', 'html-semantics'],
            ['javascript', 'html-semantics'],
            ['typescript', 'javascript'],
            ['react', 'javascript'],
            ['react', 'typescript'],
            ['state-management', 'react'],
            ['accessibility', 'html-semantics'],
            ['accessibility', 'css-responsive'],
            ['web-performance', 'react'],
            ['python-data', 'programming-fundamentals'],
            ['pandas', 'python-data'],
            ['data-cleaning', 'spreadsheet-analysis'],
            ['data-visualization', 'data-cleaning'],
            ['sql-analytics', 'sql'],
            ['sql-analytics', 'statistics-fundamentals'],
        ];

        $skills = Skill::query()
            ->get()
            ->keyBy('slug');

        DB::table('skill_prerequisites')->delete();

        foreach ($pairs as [$skillSlug, $prerequisiteSlug]) {
            $skill = $skills->get($skillSlug);
            $prerequisite = $skills->get($prerequisiteSlug);

            if (! $skill || ! $prerequisite) {
                continue;
            }

            DB::table('skill_prerequisites')->insert([
                'skill_id' => $skill->id,
                'prerequisite_skill_id' => $prerequisite->id,
                'factor' => 1.20,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
