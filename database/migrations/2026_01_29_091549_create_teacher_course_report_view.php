<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTeacherCourseReportView extends Migration
{
public function up()
{
    DB::statement("DROP VIEW IF EXISTS teacher_course_report_view");

    DB::statement("
        CREATE VIEW teacher_course_report_view AS
        SELECT
            u.*,
            c.id AS course_id,
            c.name AS course_name
        FROM user u
        LEFT JOIN course_user cu ON u.id = cu.user_id
        LEFT JOIN courses c ON cu.course_id = c.id
        WHERE u.deleted_at IS NULL
        AND u.is_admin = 0
    ");
}

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS teacher_course_report_view");
    }
}
