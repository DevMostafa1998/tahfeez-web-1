<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student', function (Blueprint $table) {
            $table->foreignId('categorie_id')
                ->nullable()
                ->after('is_displaced')
                ->constrained('categorie')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student', function (Blueprint $table) {
            $table->dropForeign(['categorie_id']);
            $table->dropColumn('categorie_id');
        });
    }
};
