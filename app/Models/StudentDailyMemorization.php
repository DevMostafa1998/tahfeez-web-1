<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDailyMemorization extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'sura_name',
        'verses_from',
        'verses_to',
        'note',
    ];

    // علاقة الربط مع مودل الطالب
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
