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

        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;

        // --- التعديل: جلب الفلتر من الجلسة ---
        $selectedGender = session('selected_gender');

        // --- 1. إحصائية الطلاب ---
        $studentsQuery = DB::table('student')->whereNull('deleted_at');

        if ($selectedCenterId) {
            $studentsQuery->where('center_id', $selectedCenterId);
        }

        if ($selectedGender) {
            $studentsQuery->where('gender', $selectedGender);
        }

        if (!$isAdmin) {
            $myStudentIdsQuery = DB::table('student_group')
                ->join('group', 'student_group.group_id', '=', 'group.id')
                ->join('student', 'student_group.student_id', '=', 'student.id')
                ->where('group.UserId', $user->id)
                ->whereNull('group.deleted_at');

            if ($selectedGender) {
                $myStudentIdsQuery->where('student.gender', $selectedGender);
            }

            $myStudentIds = $myStudentIdsQuery->distinct()->pluck('student_group.student_id')->toArray();

            $studentsQuery->whereIn('id', $myStudentIds);
            $currentStudentIds = $myStudentIds;
        } else {
            // للأدمن: تجلب المعرفات المتبقية بعد فلترة المركز والجنس
            $currentStudentIds = (clone $studentsQuery)->pluck('id')->toArray();
        }

        $students_count = $studentsQuery->count();

        // --- 2. إحصائية المجموعات والمحفظين ---
        if ($isAdmin) {
            $usersQuery = DB::table('user')
                ->whereNull('deleted_at')
                ->where(function ($query) {
                    $query->where('is_admin', 0)->orWhereNull('is_admin');
                });

            $groupsQuery = DB::table('group')
                ->select('group.id', 'group.GroupName', 'group.center_id', 'group.UserId')
                ->whereNull('group.deleted_at');

            if ($selectedCenterId) {
                $usersQuery->where('center_id', $selectedCenterId);
                $groupsQuery->where('group.center_id', $selectedCenterId);
            }

            if ($selectedGender) {
                $usersQuery->where('gender', $selectedGender);
                $groupsQuery->join('user', 'group.UserId', '=', 'user.id')
                    ->where('user.gender', $selectedGender);
            }

            $users_count = $usersQuery->count();
            $groups_count = $groupsQuery->count();
        } else {
            $users_count = 0;
            $groupsQuery = DB::table('group')
                ->select('group.id', 'group.GroupName', 'group.center_id', 'group.UserId')
                ->where('group.UserId', $user->id)
                ->whereNull('group.deleted_at');

            if ($selectedCenterId) {
                $groupsQuery->where('group.center_id', $selectedCenterId);
            }

            if ($selectedGender) {
                $groupsQuery->join('user', 'group.UserId', '=', 'user.id')
                    ->where('user.gender', $selectedGender);
            }

            $groups_count = $groupsQuery->count();
        }

        // --- تصنيفات المحفظين ---
        $userCategoryQuery = DB::table('categorie')
            ->leftJoin('user', 'categorie.id', '=', 'user.category_id')
            ->where('user.is_admin', 0)
            ->whereNull('user.deleted_at');

        if ($selectedCenterId) {
            $userCategoryQuery->where('user.center_id', $selectedCenterId);
        }

        if ($selectedGender) {
            $userCategoryQuery->where('user.gender', $selectedGender);
        }

        $userCategoryData = $userCategoryQuery
            ->select('categorie.name as cat_name', DB::raw('count(user.id) as teacher_count'))
            ->groupBy('categorie.id', 'categorie.name')
            ->get();

        // --- 3. نسبة الحفظ اليومية ---
        $memorizationQuery = DB::table('student_daily_memorizations')
            ->whereDate('date', $today);

        if ($selectedCenterId || !$isAdmin || $selectedGender) {
            $memorizationQuery->whereIn('student_id', $currentStudentIds);
        }

        $students_who_memorized_today = $memorizationQuery->distinct('student_id')->count();
        $memorization_percentage = $students_count > 0 ? round(($students_who_memorized_today / $students_count) * 100) : 0;

        // --- 4. إحصائية الحضور والغياب ---
        $attendanceBaseQuery = DB::table('student_attendances')
            ->whereDate('attendance_date', $today);

        if ($selectedCenterId || !$isAdmin || $selectedGender) {
            $attendanceBaseQuery->whereIn('student_id', $currentStudentIds);
        }

        $present_count = (clone $attendanceBaseQuery)->where('status', 'حاضر')->distinct('student_id')->count();
        $absent_count = (clone $attendanceBaseQuery)->where('status', 'غائب')->distinct('student_id')->count();

        // --- 5. المخططات البيانية ---
        $ageQuery = DB::table('student')
            ->selectRaw("TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age, count(*) as count")
            ->whereNull('deleted_at');

        if ($selectedCenterId) {
            $ageQuery->where('center_id', $selectedCenterId);
        }

        if ($selectedGender) {
            $ageQuery->where('gender', $selectedGender);
        }

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

        if ($selectedCenterId) {
            $groupChartQuery->where('group.center_id', $selectedCenterId);
        }

        if ($selectedGender) {
            $groupChartQuery->leftJoin('user', 'group.UserId', '=', 'user.id')
                ->where('user.gender', $selectedGender)
                ->where('student.gender', $selectedGender);
        }

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
