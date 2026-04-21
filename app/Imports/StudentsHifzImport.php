<?php

namespace App\Imports;

use App\Models\StudentDailyMemorization;
use App\Models\StudentAttendance; 
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Auth;

class StudentsHifzImport implements ToModel
{
    public function model(array $row)
    {
        if ($row[0] == 'رقم_الطالب_المخفي' || empty($row[0])) {
            return null;
        }

        $attendanceDate = $this->transformDate($row[2]); 

        $hasMemorized = !empty($row[3]); 
        $status = $hasMemorized ? 'حاضر' : 'غائب';

        StudentAttendance::updateOrCreate(
            [
                'student_id'      => $row[0],
                'attendance_date' => $attendanceDate,
            ],
            [
                'status'          => $status,
                'recorded_by'     => Auth::id(), 
                'notes'           => $row[6] ?? 'تم التسجيل تلقائياً عبر رفع الملف',
            ]
        );

        if (!$hasMemorized) {
            return null;
        }

        return new StudentDailyMemorization([
            'student_id'  => $row[0], // العمود A
            'date'        => $attendanceDate, // العمود C
            'sura_name'   => $row[3], // العمود D
            'verses_from' => $row[4], // العمود E
            'verses_to'   => $row[5], // العمود F
            'note'        => $row[6], // العمود G
        ]);
    }

    private function transformDate($value)
    {
        if (empty($value)) return now()->format('Y-m-d');

        try {
            if (is_numeric($value)) {
                return Carbon::instance(Date::excelToDateTimeObject($value))->format('Y-m-d');
            }
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }
}