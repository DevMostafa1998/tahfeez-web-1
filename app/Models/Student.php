<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;
    protected $table = 'student';
    protected $fillable = [
        'full_name',
        'id_number',
        'date_of_birth',
        'phone_number',
        'address',
        'is_displaced',
        'user_id',
        'creation_by',
        'updated_by',
        'deleted_by',
        'birth_place',
        'center_name',
        'mosque_name',
        'mosque_address',
        'whatsapp_number'
    ];
    protected $casts = [
        'date_of_birth' => 'date',
        'is_displaced' => 'boolean',
    ];

    // العلاقة مع الدورات
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'student_group', 'student_id', 'group_id');
    }
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function latestMemorization()
    {
        // هذه العلاقة تجلب آخر سجل مضاف للطالب في جدول الحفظ اليومي
        return $this->hasOne(StudentDailyMemorization::class, 'student_id')->latestOfMany('date');
    }
}
