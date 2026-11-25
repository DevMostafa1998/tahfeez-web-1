<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student', function (Blueprint $table) {
            $table->unsignedTinyInteger('parts_memorized')->default(0)->after('gender');
        });

        Schema::create('part_review_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->index();
            $table->unsignedBigInteger('keeper_id')->nullable()->index();
            $table->unsignedBigInteger('center_id')->nullable()->index();
            $table->unsignedTinyInteger('milestone');
            $table->string('status')->default('pending'); // pending | completed
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'milestone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('part_review_requests');
        Schema::table('student', function (Blueprint $table) {
            $table->dropColumn('parts_memorized');
        });
    }
};
