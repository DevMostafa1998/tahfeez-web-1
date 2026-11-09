<?php

namespace App\BusinessLogic;

use App\Models\Group;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentgroupLogic
{
    /**
     * جلب البيانات اللازمة لعرض مجموعات الطلاب
     */
    public function getIndexData()
    {
        $user = Auth::user();

        // معرف المركز المحدد من الـ Session
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;

        // --- التعديل: جلب اختيار الجنس من الجلسة ---
        $selectedGender = session('selected_gender');

        // 1. استعلام المجموعات
        $groupsQuery = Group::with(['students', 'teacher'])
            ->withCount('students')
            ->whereNull('deleted_at')
            ->orderBy('creation_at', 'desc');

        if ($selectedCenterId) {
            $groupsQuery->where(function ($q) use ($selectedCenterId) {
                $q->where('center_id', $selectedCenterId)
                    ->orWhereHas('teacher', function ($teacherQuery) use ($selectedCenterId) {
                        $teacherQuery->where('center_id', $selectedCenterId);
                    });
            });
        }

        if ($user->is_admin == 0 && $user->is_admin_rouls == 0) {
            $groupsQuery->where('UserId', $user->id);
        }

        // --- التعديل: فلترة المجموعات بناءً على جنس المعلم ---
        if ($selectedGender) {
            $groupsQuery->whereHas('teacher', function ($q) use ($selectedGender) {
                $q->where('gender', $selectedGender);
            });
        }

        // 2. استعلام المحفظين / المعلمين
        $teachersQuery = DB::table('user')
            ->select('id', 'full_name', 'gender')
            ->where('is_admin', 0)
            ->whereNull('deleted_at');

        if ($selectedCenterId) {
            $teachersQuery->where('center_id', $selectedCenterId);
        }

        // --- التعديل: فلترة المعلمين حسب الجنس المختار ---
        if ($selectedGender) {
            $teachersQuery->where('gender', $selectedGender);
        }

        // 3.  جلب الطلاب المتاحين للربط بالمجموعات
        $assignedStudentIds = DB::table('student_group')
            ->whereNull('deleted_at')
            ->pluck('student_id')
            ->toArray();

        // ب: جلب الطلاب غير المسجلين بمجموعات وغير المحذوفين
        $availableStudentsQuery = DB::table('student')
            ->whereNull('deleted_at')
            ->whereNotIn('id', $assignedStudentIds);

        if ($selectedCenterId) {
            $availableStudentsQuery->where(function ($q) use ($selectedCenterId) {
                $q->where('center_id', $selectedCenterId)
                    ->orWhereNull('center_id');
            });
        }

        // --- التعديل: فلترة الطلاب المتاحين حسب الجنس المختار ---
        if ($selectedGender) {
            $availableStudentsQuery->where('gender', $selectedGender);
        }

        $availableStudents = $availableStudentsQuery->get();

        return [
            'groups'            => $groupsQuery->get(),
            'teachers'          => $teachersQuery->get(),
            'availableStudents' => $availableStudents,
        ];
    }

    /**
     * مزامنة الطلاب مع مجموعة معينة
     */
    public function syncStudentsToGroup($groupId, array $studentIds)
    {
        $group = Group::findOrFail($groupId);

        return $group->students()->sync($studentIds);
    }
}
