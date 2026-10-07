<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_projects', function (Blueprint $table) {
            $table
                ->decimal('evaluation_score', 5, 2)
                ->nullable();

            $table
                ->string('review_status')
                ->default('not_submitted')
                ->index();

            $table
                ->timestamp('submitted_at')
                ->nullable();

            $table
                ->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table
                ->timestamp('reviewed_at')
                ->nullable();

            $table
                ->text('admin_notes')
                ->nullable();
        });

        DB::table('user_projects')
            ->whereNotNull('repository_url')
            ->where(
                'repository_url',
                '<>',
                '',
            )
            ->update([
                'status' => 'submitted',
                'progress_percentage' => 95,
                'review_status' => 'pending',
                'submitted_at' => DB::raw(
                    'COALESCE(completed_at, updated_at, created_at)',
                ),
                'completed_at' => null,
            ]);
    }

    public function down(): void
    {
        DB::table('user_projects')
            ->whereIn(
                'status',
                [
                    'submitted',
                    'needs_revision',
                ],
            )
            ->update([
                'status' => 'in_progress',
            ]);

        Schema::table('user_projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId(
                'reviewed_by',
            );

            $table->dropColumn([
                'evaluation_score',
                'review_status',
                'submitted_at',
                'reviewed_at',
                'admin_notes',
            ]);
        });
    }
};
