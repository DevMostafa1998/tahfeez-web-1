<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lov', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('description');
            $table->boolean('is_valid')->default(true);
            $table->timestamps();
            $table->index(['type', 'is_valid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lov');
    }
};
