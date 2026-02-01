<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    // اسم الجدول كما ظهر في الـ Migration
    protected $table = 'student_attendances';

    protected $fillable = [
        'student_id',
        'attendance_date',
        'status',
        'notes',
        'recorded_by'
    ];

    // علاقة السجل بالطالب (كل سجل حضور يتبع لطالب واحد)
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
