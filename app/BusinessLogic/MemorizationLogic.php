<?php

namespace App\BusinessLogic;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MemorizationLogic
{
    public function storeDailyMemorization(array $data)
    {
        return DB::table('student_daily_memorizations')->insert([
            'student_id'  => $data['student_id'],
            'date'        => $data['date'],
            'sura_name'   => $data['sura_name'],
            'verses_from' => $data['verses_from'],
            'verses_to'   => $data['verses_to'],
            'note'        => $data['note'] ?? null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}
