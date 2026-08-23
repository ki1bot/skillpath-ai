<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table
                ->string('study_program', 120)
                ->nullable();

            $table->unique(
                ['career_id', 'study_program'],
                'assessments_career_program_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropUnique('assessments_career_program_unique');
            $table->dropColumn('study_program');
        });
    }
};
