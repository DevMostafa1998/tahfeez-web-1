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
        Schema::table('student_daily_memorizations', function (Blueprint $table) {
           $table->unsignedBigInteger('examType_id')->nullable()->after('verses_to');
            $table->foreign('examType_id')->references('id')->on('lov');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_daily_memorizations', function (Blueprint $table) {
            //
        });
    }
};
