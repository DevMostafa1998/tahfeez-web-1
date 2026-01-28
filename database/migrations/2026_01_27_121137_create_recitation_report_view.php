<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRecitationReportView extends Migration
{
    public function up()
    {
        DB::statement("DROP VIEW IF EXISTS recitation_report_view");

        DB::statement("
            CREATE VIEW recitation_report_view AS
            SELECT
                sdm.id AS memorization_id,
                sdm.date AS recitation_date,
                sdm.sura_name,
                sdm.verses_from,
                sdm.verses_to,
                sdm.note,
                s.id AS student_id,
                s.full_name AS student_name,
                s.id_number AS student_id_number,
                g.id AS group_id,
                g.GroupName AS group_name,
                u.id AS teacher_id,
                u.full_name AS teacher_name
            FROM student_daily_memorizations sdm
            JOIN student s ON sdm.student_id = s.id
            LEFT JOIN student_group sg ON s.id = sg.student_id AND sg.deleted_at IS NULL
            LEFT JOIN `group` g ON sg.group_id = g.id
            LEFT JOIN user u ON g.UserId = u.id
            WHERE s.deleted_at IS NULL
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS recitation_report_view");
    }
}
