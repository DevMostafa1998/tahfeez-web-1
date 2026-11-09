<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    // اسم الجدول كما ظهر في الـ Migration
    protected $table = 'student_attendances';

    protected $fillable = [
        'student_id',
        'group_id',
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

    // علاقة السجل بالمجموعة (كل سجل حضور يتبع لمجموعة معينة)
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
