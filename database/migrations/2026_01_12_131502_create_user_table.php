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
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('password');
            $table->string('id_number')->unique();
            $table->dateTime('date_of_birth');
            $table->string('phone_number');
            $table->string('address');
            $table->boolean('is_admin')->default(false);

            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');

            $table->string('creation_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
