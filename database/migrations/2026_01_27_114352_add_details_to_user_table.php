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
        Schema::table('user', function (Blueprint $table) {
            $table->string('birth_place')->nullable()->after('date_of_birth');
            $table->string('wallet_number')->nullable()->after('phone_number');
            $table->string('whatsapp_number')->nullable()->after('wallet_number');
            $table->string('qualification')->nullable()->after('whatsapp_number'); // المؤهل العلمي
            $table->string('specialization')->nullable()->after('qualification'); // التخصص
            $table->integer('parts_memorized')->default(0)->after('specialization'); // عدد الأجزاء المحفوظة
            $table->string('mosque_name')->nullable()->after('parts_memorized');
            $table->boolean('is_displaced')->default(false)->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn([
                'birth_place',
                'wallet_number',
                'whatsapp_number',
                'qualification',
                'specialization',
                'parts_memorized',
                'mosque_name',
                'is_displaced'
            ]);
        });
    }
};
