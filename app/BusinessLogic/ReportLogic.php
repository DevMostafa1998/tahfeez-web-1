<?php

namespace App\BusinessLogic;

use App\Models\RecitationReport;
use App\Models\Student;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReportLogic
{
    public function getRecitationReport($filters)
{
    $query = RecitationReport::query();

    if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
        $query->whereBetween('recitation_date', [$filters['date_from'], $filters['date_to']]);
    }


    if (!empty($filters['student_id'])) {
        $query->where('student_id', $filters['student_id']);
    }


    if (!empty($filters['group_id'])) {
        $query->where('group_id', $filters['group_id']);
    }

    if (Auth::user()->is_admin) {
        if (!empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }
    } else {
        $query->where('teacher_id', Auth::id());
    }

    return $query->orderBy('recitation_date', 'desc')->get();
}

    public function getFilterLists()
    {
        $user = Auth::user();

        if ($user->is_admin) {
            return [
                'students' => Student::orderBy('full_name')->get(),
                'groups'   => Group::orderBy('GroupName')->get(),
                'teachers' => User::where('is_admin', 0)->get(),
            ];
        }

       return [
        'groups'  => Group::where('UserId', $user->id)->get(),
        'students' => Student::whereHas('groups', function ($q) use ($user) {
            $q->where('UserId', $user->id); // جلب الطلاب الذين ينتمون لمجموعات هذا المحفظ
        })->orderBy('full_name')->get(),
        'teachers' => collect(),
    ];
    }
}
