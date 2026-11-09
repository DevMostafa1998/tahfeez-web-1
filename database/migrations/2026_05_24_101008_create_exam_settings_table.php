<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::create('exam_settings', function (Blueprint $table) {
            $table->id();
            $table->string('grade_name');
            $table->integer('min_score');
            $table->integer('max_score');
            $table->enum('status', ['ناجح', 'راسب']);
            $table->string('text_class');
            $table->timestamps();
        });

        DB::table('exam_settings')->insert([
            ['grade_name' => 'ممتاز', 'min_score' => 95, 'max_score' => 100, 'status' => 'ناجح', 'text_class' => 'text-success'],
            ['grade_name' => 'جيد جدا', 'min_score' => 85, 'max_score' => 94, 'status' => 'ناجح', 'text_class' => 'text-primary'],
            ['grade_name' => 'جيد', 'min_score' => 75, 'max_score' => 84, 'status' => 'ناجح', 'text_class' => 'text-info'],
            ['grade_name' => 'مقبول', 'min_score' => 50, 'max_score' => 74, 'status' => 'ناجح', 'text_class' => 'text-warning'],
            ['grade_name' => 'راسب', 'min_score' => 0, 'max_score' => 49, 'status' => 'راسب', 'text_class' => 'text-danger'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('exam_settings');
    }
};
