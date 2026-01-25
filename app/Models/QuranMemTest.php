<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranMemTest extends Model
{
    protected $table = 'quran_mem_tests';
    const CREATED_AT = 'creation_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'studentId',
        'date',
        'juz_count',
        'examType',
        'result_status',
        'note',
        'creation_at',
        'creation_by',
        'updated_at',
        'updated_by',
        'deleted_at',
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
