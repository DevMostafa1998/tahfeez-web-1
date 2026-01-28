<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDailyMemorization extends Model
{
    use HasFactory;

    protected $table = 'student_daily_memorizations';

    protected $fillable = [
        'student_id',
        'date',
        'sura_name',
        'verses_from',
        'verses_to',
        'note',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
