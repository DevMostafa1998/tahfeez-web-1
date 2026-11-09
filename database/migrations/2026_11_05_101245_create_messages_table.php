<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('user')->cascadeOnDelete();   // المُرسل (مدير أو محفظ)
            $table->foreignId('receiver_id')->constrained('user')->cascadeOnDelete(); // المُستقبل
            $table->text('body');                                                     // نص الرسالة
            $table->boolean('is_read')->default(false);                              // حالة القراءة
            $table->foreignId('center_id')->nullable()->constrained('centers')->nullOnDelete(); // ربط بالمركز للملفات المتعددة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
