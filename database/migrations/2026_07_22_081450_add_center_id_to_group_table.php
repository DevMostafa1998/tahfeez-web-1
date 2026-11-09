<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     */
    public function up(): void
    {
        Schema::table('group', function (Blueprint $table) {
            $table->unsignedBigInteger('center_id')->default(1)->after('UserId');
        });
    }

    /**
     */
    public function down(): void
    {
        Schema::table('group', function (Blueprint $table) {
            $table->dropColumn('center_id');
        });
    }
};
