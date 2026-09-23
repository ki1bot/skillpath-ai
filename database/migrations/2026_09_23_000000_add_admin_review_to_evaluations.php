<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table
                ->string('review_status')
                ->default('pending')
                ->index();

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

        DB::table('evaluations')->update([
            'review_status' => 'reviewed',
            'reviewed_at' => DB::raw('updated_at'),
        ]);
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');

            $table->dropColumn([
                'review_status',
                'reviewed_at',
                'admin_notes',
            ]);
        });
    }
};
