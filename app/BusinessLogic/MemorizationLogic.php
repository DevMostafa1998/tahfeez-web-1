<?php

namespace App\BusinessLogic;

use Illuminate\Support\Facades\DB;

class MemorizationLogic
{
    public function storeDailyMemorization(array $data)
    {
        return DB::table('student_daily_memorizations')->insert([
            'student_id'   => $data['student_id'],
            'date'         => $data['date'],
            'sura_name'    => $data['sura_name'] ?? null,
            'verses_from'  => $data['verses_from'] ?? null,
            'verses_to'    => $data['verses_to'] ?? null,
            'examType_id'  => $data['examType_id'] ?? null,
            'note'         => $data['note'] ?? null,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
}
