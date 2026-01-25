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
        Schema::create('quran_mem_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studentId')->constrained('student')->onDelete('cascade');
            $table->date('date');
            $table->integer('juz_count');
            $table->enum('examType', ['سرد', 'اجزاء مجتمعه']);
            $table->enum('result_status', ['ناجح', 'راسب']);
            $table->text('note')->nullable();
            $table->timestamp('creation_at')->nullable();
            $table->string('creation_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_mem_tests');
    }
};
