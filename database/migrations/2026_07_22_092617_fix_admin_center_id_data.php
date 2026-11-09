<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('user')
            ->where('is_admin', 1)
            ->update(['center_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
