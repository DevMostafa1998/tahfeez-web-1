<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseUser extends Pivot
{
    protected $table = 'course_user';
    public $timestamps = true; // لأن الجدول يحتوي على created_at و updated_at
}
