<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';

    protected $fillable = ['name', 'type'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'course_user', 'course_id', 'user_id')
                    ->using(CourseUser::class)
                    ->withTimestamps();
    }


    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_student', 'course_id', 'student_id')
                    ->using(CourseStudent::class)
                    ->withTimestamps();
    }
    protected static function boot()
{
    parent::boot();

    static::deleting(function ($course) {
        $course->users()->detach();
        $course->students()->detach();
    });
}
}
