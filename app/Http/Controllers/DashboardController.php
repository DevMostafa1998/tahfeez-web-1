<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $students_count = DB::table('student')->count();

        // حساب المحفظين بدون المحذوفين
        $users_count = DB::table('user')
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->where('is_admin', 0)
                    ->orWhereNull('is_admin');
            })
            ->count();

        //  عدد المجموعات
        $groups_count = DB::table('group')->count();

        $today = Carbon::today();

        $students_who_memorized_today = DB::table('student_daily_memorizations')
            ->whereDate('date', $today)
            ->distinct('student_id')
            ->count();

        // المعادلة: (عدد الذين سمعوا اليوم ÷ عدد الطلاب الكلي) * 100
        if ($students_count > 0) {
            $memorization_percentage = round(($students_who_memorized_today / $students_count) * 100);
        } else {
            $memorization_percentage = 0;
        }


        $ageData = DB::table('student')
            ->selectRaw("TIMESTAMPDIFF(YEAR,date_of_birth, CURDATE()) AS age, count(*) as count")
            ->whereNull('deleted_at')
            ->groupBy('age')
            ->orderBy('age')
            ->get();
        $groupData = DB::table('group')
            ->leftJoin('student_group', 'group.id', '=', 'student_group.group_id')
            ->whereNull('group.deleted_at')
            ->select('group.GroupName as name', DB::raw('count(student_group.student_id) as students_count'))
            ->groupBy('group.id', 'group.GroupName')
            ->get();
        $age_labels = $ageData->pluck('age')->map(fn($age) => $age . ' سنة');
        $age_counts = $ageData->pluck('count');
        $group_labels = $groupData->pluck('name');
        $group_students_counts = $groupData->pluck('students_count');


        // إرسال البيانات للصفحة
        return view('layouts.dashboard', compact(
            'students_count',
            'users_count',
            'groups_count',
            'memorization_percentage',
            'age_labels',
            'age_counts',
            'group_labels',
            'group_students_counts'
        ));
    }
}
