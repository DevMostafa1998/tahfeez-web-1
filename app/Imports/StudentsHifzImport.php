<?php

namespace App\Imports;

use App\Models\StudentDailyMemorization;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StudentsHifzImport implements ToModel
{
    public function model(array $row)
    {
        if ($row[0] == 'رقم_الطالب_المخفي' || empty($row[3])) {
            return null;
        }

        return new StudentDailyMemorization([
            'student_id'  => $row[0], // العمود الأول A
            'date'        => $this->transformDate($row[2]), // العمود الثالث C (التاريخ)
            'sura_name'   => $row[3], // العمود الرابع D (السورة)
            'verses_from' => $row[4], // العمود الخامس E (من)
            'verses_to'   => $row[5], // العمود السادس F (إلى)
            'note'        => $row[6], // العمود السابع G (الملاحظات)
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
