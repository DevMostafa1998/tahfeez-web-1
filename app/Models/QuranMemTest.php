<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuranMemTest extends Model
{
    use SoftDeletes;
    protected $table = 'quran_mem_tests';
    const CREATED_AT = 'creation_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'studentId',
        'date',
        'from_juz',
        'to_juz',
        'juz_count',
        'score_percentage',
        'teacher_name',
        'tester_name',
        'examType',
        'result_status',
        'note',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    protected $casts = [
        'date' => 'date',
    ];
    // علاقة لاختبار الطالب (بفرض وجود مودل Student)
    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
}
