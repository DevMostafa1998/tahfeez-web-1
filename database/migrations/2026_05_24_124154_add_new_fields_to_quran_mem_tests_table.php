<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quran_mem_tests', function (Blueprint $table) {
            $table->integer('from_juz')->after('date');
            $table->integer('to_juz')->after('from_juz');
            $table->integer('score_percentage')->after('juz_count');
            $table->string('teacher_name')->after('score_percentage');

            $table->string('result_status')->change();
        });
    }

    public function down(): void
    {
        Schema::table('quran_mem_tests', function (Blueprint $table) {
            $table->dropColumn(['from_juz', 'to_juz', 'score_percentage', 'teacher_name']);
        });
    }
};
