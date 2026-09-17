<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->update([
            'remember_token' => null,
        ]);
    }

    public function down(): void
    {
    }
};
