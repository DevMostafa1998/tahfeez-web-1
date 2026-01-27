<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['name', 'type'];

    public function users() { return $this->belongsToMany(User::class, 'course_user'); }

    public function students() { return $this->belongsToMany(Student::class, 'course_student'); }
}
