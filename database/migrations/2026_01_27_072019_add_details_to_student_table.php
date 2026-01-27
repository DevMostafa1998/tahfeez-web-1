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
            $table->string('birth_place')->nullable()->after('date_of_birth');
            $table->string('center_name')->nullable()->after('address');
            $table->string('mosque_name')->nullable()->after('center_name');
            $table->string('mosque_address')->nullable()->after('mosque_name');
            $table->string('whatsapp_number')->nullable()->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student', function (Blueprint $table) {
            $table->dropColumn([
                'birth_place',
                'center_name',
                'mosque_name',
                'mosque_address',
                'whatsapp_number'
            ]);
        });
    }
};
