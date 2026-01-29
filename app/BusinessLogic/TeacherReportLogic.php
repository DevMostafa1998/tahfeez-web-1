<?php

namespace App\BusinessLogic;

use App\Models\TeacherCourseReport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TeacherReportLogic
{
   public function getTeacherCoursesReport($filters)
{
    $query = TeacherCourseReport::query();

    if (Auth::user()->is_admin) {
        if (!empty($filters['teacher_id'])) {
            $query->where('id', $filters['teacher_id']);
        }
    } else {
        $query->where('id', Auth::id());
    }

    return $query->orderBy('full_name', 'asc')->get();
}

    public function getFilterLists()
    {
        if (Auth::user()->is_admin) {
            return [
                'teachers' => User::where('is_admin', 0)->orderBy('full_name')->get(),
            ];
        }

        return [
            'teachers' => collect(),
        ];
    }
}
