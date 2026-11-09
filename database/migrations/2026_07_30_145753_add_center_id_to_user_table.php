<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->foreignId('center_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('centers')
                  ->nullOnDelete();
        });

        DB::table('user')
            ->where('is_admin', 0)
            ->orWhereNull('is_admin')
            ->update(['center_id' => 2]);
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropForeign(['center_id']);
            $table->dropColumn('center_id');
        });
    }
};
