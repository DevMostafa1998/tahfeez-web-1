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
        Schema::create('student_daily_memorizations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained('student')->onDelete('cascade');

            $table->date('date'); // تاريخ يوم الحفظ
            $table->string('sura_name'); // اسم السورة
            $table->integer('verses_from'); // من آية
            $table->integer('verses_to'); // إلى آية
            $table->text('note')->nullable(); // ملاحظات 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_daily_memorizations');
    }
};
