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
        Schema::create('teacher_attendances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('user')->onDelete('cascade'); 
        $table->date('attendance_date');
        $table->enum('status', ['حاضر', 'غائب', 'مستأذن']);
        $table->string('notes')->nullable();
        $table->string('recorded_by');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_attendances');
    }
};
