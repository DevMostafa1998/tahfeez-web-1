<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseStudent extends Pivot
{
    protected $table = 'course_student';
    public $timestamps = true;
}
