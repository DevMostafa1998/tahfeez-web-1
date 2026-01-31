<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->is_admin == 1;
        $today = Carbon::today();

        $studentsQuery = DB::table('student')->whereNull('deleted_at');

        if (!$isAdmin) {
            $myStudentIds = DB::table('student_group')
                ->join('group', 'student_group.group_id', '=', 'group.id')
                ->where('group.UserId', $user->id)
                ->whereNull('group.deleted_at')
                ->distinct()
                ->pluck('student_group.student_id')
                ->toArray();

            $studentsQuery->whereIn('id', $myStudentIds);
            $currentStudentIds = $myStudentIds;
        } else {
            $currentStudentIds = null;
        }

        $students_count = $studentsQuery->count();

        // --- 2. إحصائية المجموعات والمحفظين ---
        if ($isAdmin) {
            $users_count = DB::table('user')
                ->whereNull('deleted_at')
                ->where(function ($query) {
                    $query->where('is_admin', 0)->orWhereNull('is_admin');
                })->count();
            $groups_count = DB::table('group')->whereNull('deleted_at')->count();
        } else {
            $users_count = 0;
            $groups_count = DB::table('group')
                ->where('UserId', $user->id)
                ->whereNull('deleted_at')
                ->count();
        }
        $userCategoryData = DB::table('categorie')
            ->leftJoin('user', 'categorie.id', '=', 'user.category_id')
            ->where('user.is_admin', 0)
            ->whereNull('user.deleted_at')
            ->select('categorie.name as cat_name', DB::raw('count(user.id) as teacher_count'))
            ->groupBy('categorie.id', 'categorie.name')
            ->get();
        // --- 3. نسبة الحفظ اليومية ---
        $memorizationQuery = DB::table('student_daily_memorizations')
            ->whereDate('date', $today);

        if (!$isAdmin) {
            $memorizationQuery->whereIn('student_id', $currentStudentIds);
        }

        $students_who_memorized_today = $memorizationQuery->distinct('student_id')->count();
        $memorization_percentage = $students_count > 0 ? round(($students_who_memorized_today / $students_count) * 100) : 0;

        // --- 4. إحصائية الحضور والغياب
        $attendanceBaseQuery = DB::table('student_attendances')
            ->whereDate('attendance_date', $today);

        if (!$isAdmin) {
            $attendanceBaseQuery->whereIn('student_id', $currentStudentIds);
        }

        $present_count = (clone $attendanceBaseQuery)->where('status', 'حاضر')->distinct('student_id')->count();
        $absent_count = (clone $attendanceBaseQuery)->where('status', 'غائب')->distinct('student_id')->count();
        //-----المخططات------
        $ageQuery = DB::table('student')
            ->selectRaw("TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age, count(*) as count")
            ->whereNull('deleted_at');

        if (!$isAdmin) {
            $ageQuery->whereIn('id', $currentStudentIds);
        }
        $ageData = $ageQuery->groupBy('age')->orderBy('age')->get();

        // مخطط المجموعات
        $groupChartQuery = DB::table('group')
            ->leftJoin('student_group', 'group.id', '=', 'student_group.group_id')
            ->leftJoin('student', 'student.id', '=', 'student_group.student_id')
            ->whereNull('group.deleted_at')
            ->whereNull('student.deleted_at');

        if (!$isAdmin) {
            $groupChartQuery->where('group.UserId', $user->id);
        }

        $groupData = $groupChartQuery->select('group.GroupName as name', DB::raw('count(student.id) as students_count'))
            ->groupBy('group.id', 'group.GroupName')
            ->get();

        return view('layouts.dashboard', [
            'students_count' => $students_count,
            'users_count' => $users_count,
            'groups_count' => $groups_count,
            'memorization_percentage' => $memorization_percentage,
            'present_count' => $present_count,
            'absent_count' => $absent_count,
            'age_labels' => $ageData->pluck('age')->map(fn($a) => $a . ' سنة'),
            'age_counts' => $ageData->pluck('count'),
            'group_labels' => $groupData->pluck('name'),
            'group_students_counts' => $groupData->pluck('students_count'),
            'isAdmin' => $isAdmin,
            'user_cat_labels' => $userCategoryData->pluck('cat_name'),
            'user_cat_counts' => $userCategoryData->pluck('teacher_count'),
        ]);
    }
}
