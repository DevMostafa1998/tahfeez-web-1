<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = Schema::hasTable('student_attendance') ? 'student_attendance' : 'student_attendances';

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'group_id')) {
                $table->unsignedBigInteger('group_id')->nullable()->after('student_id');

                $table->foreign('group_id')
                      ->references('id')
                      ->on('group')
                      ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        $tableName = Schema::hasTable('student_attendance') ? 'student_attendance' : 'student_attendances';

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (Schema::hasColumn($tableName, 'group_id')) {
                $table->dropForeign(['group_id']);
                $table->dropColumn('group_id');
            }
        });
    }
};
