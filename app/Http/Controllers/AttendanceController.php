<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        // 1. أخذ قيمة المركز المحدد والجنس من الـ Session
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;

        $selectedGender = session('selected_gender');

        // أخذ قيم الفلاتر من الطلب
        $date = $request->input('date', date('Y-m-d'));
        $searchId = $request->input('search_id');
        $teacherId = $request->input('teacher_id');
        $groupId = $request->input('group_id');

        $user = Auth::user();

        // 2. جلب المحفظين (مفلترين حسب المركز والجنس)
        $teachersQuery = User::where('is_admin', 0)->whereNull('deleted_at');
        if ($selectedCenterId) {
            $teachersQuery->where('center_id', $selectedCenterId);
        }
        if ($selectedGender) {
            $teachersQuery->where('gender', $selectedGender);
        }
        $teachers = $teachersQuery->get();

        // 3. جلب المجموعات (مفلترة حسب المركز والجنس والصلاحيات)
        $groupsQuery = Group::query()->whereNull('deleted_at');

        if ($selectedCenterId) {
            $groupsQuery->where(function ($q) use ($selectedCenterId) {
                $q->where('center_id', $selectedCenterId)
                    ->orWhereHas('teacher', function ($teacherQuery) use ($selectedCenterId) {
                        $teacherQuery->where('center_id', $selectedCenterId);
                    });
            });
        }

        if ($selectedGender) {
            $groupsQuery->whereHas('teacher', function ($q) use ($selectedGender) {
                $q->where('gender', $selectedGender);
            });
        }

        if (!$user->is_admin) {
            $groupsQuery->where('UserId', $user->id);
        } else {
            if ($teacherId) {
                $groupsQuery->where('UserId', $teacherId);
            }
        }
        $groups = $groupsQuery->get();

        // 4. بناء استعلام الطلاب مفلتر بالمركز والجنس والعلاقات
        $query = Student::query()->with(['groups.teacher', 'teacher'])->whereNull('deleted_at');

        if ($selectedCenterId) {
            $query->where(function ($q) use ($selectedCenterId) {
                $q->where('center_id', $selectedCenterId)
                    ->orWhereHas('groups', function ($g) use ($selectedCenterId) {
                        $g->where('center_id', $selectedCenterId);
                    });
            });
        }

        if ($selectedGender) {
            $query->where('gender', $selectedGender);
        }

        // تصفية الصلاحيات: المحفظ يرى طلابه فقط
        if (!$user->is_admin) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('groups', function ($g) use ($user) {
                        $g->where('UserId', $user->id);
                    });
            });
        }

        // تطبيق فلاتر البحث الأخرى
        if ($searchId) {
            $query->where('id_number', 'like', "%{$searchId}%");
        }

        if ($teacherId && $user->is_admin) {
            $query->where(function ($q) use ($teacherId) {
                $q->where('user_id', $teacherId)
                    ->orWhereHas('groups', function ($g) use ($teacherId) {
                        $g->where('UserId', $teacherId);
                    });
            });
        }

        if ($groupId) {
            $query->whereHas('groups', function ($g) use ($groupId) {
                $g->where('group.id', $groupId);
            });
        }

        // 5. جلب النتائج وربطها بحالة الحضور اليومية
        $students = $query->get()->map(function ($student) use ($date) {
            $attendance = DB::table('student_attendances')
                ->where('student_id', $student->id)
                ->where('attendance_date', $date)
                ->first();

            $student->today_status = $attendance ? $attendance->status : null;
            $student->today_notes = $attendance ? $attendance->notes : '';

            $firstGroup = $student->groups->first();
            $student->group_name = $firstGroup->GroupName ?? 'بدون مجموعة';
            $student->teacher_name = $firstGroup->teacher->full_name ?? ($student->teacher->full_name ?? 'غير محدد');

            return $student;
        });

        return view('students.attendance', compact('students', 'date', 'groups', 'teachers'));
    }

    public function store(Request $request)
    {
        $selectedDate = $request->input('attendance_date', date('Y-m-d'));

        if (!$request->has('students')) {
            return back()->with('error', 'لا يوجد طلاب لتسجيل حضورهم.');
        }

        foreach ($request->students as $student_id => $data) {
            if (isset($data['status'])) {
                DB::table('student_attendances')->updateOrInsert(
                    ['student_id' => $student_id, 'attendance_date' => $selectedDate],
                    [
                        'status'      => $data['status'],
                        'notes'       => $data['notes'] ?? null,
                        'recorded_by' => Auth::user()->full_name ?? Auth::user()->name,
                        'updated_at'  => now(),
                        'created_at'  => now(),
                    ]
                );
            }
        }

        return back()->with('success', 'تم حفظ سجل حضور الطلاب لتاريخ ' . $selectedDate);
    }

    public function teachersAttendance(Request $request)
    {
        // جلب معرف المركز المحدد والجنس من Session
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;

        $selectedGender = session('selected_gender');

        $date = $request->input('date', date('Y-m-d'));

        $teachersQuery = User::where('is_admin', 0)->whereNull('deleted_at');

        if ($selectedCenterId) {
            $teachersQuery->where('center_id', $selectedCenterId);
        }

        if ($selectedGender) {
            $teachersQuery->where('gender', $selectedGender);
        }

        $teachers = $teachersQuery->get()->map(function ($teacher) use ($date) {
            $att = DB::table('teacher_attendances')
                ->where('user_id', $teacher->id)
                ->where('attendance_date', $date)
                ->first();

            $teacher->today_status = $att ? $att->status : null;
            $teacher->today_notes = $att ? $att->notes : '';
            return $teacher;
        });

        return view('users.teachers_attendance', compact('teachers', 'date'));
    }

    public function storeTeachersAttendance(Request $request)
    {
        $selectedDate = $request->input('attendance_date', date('Y-m-d'));

        if ($request->has('teachers')) {
            foreach ($request->teachers as $user_id => $data) {
                if (isset($data['status'])) {
                    DB::table('teacher_attendances')->updateOrInsert(
                        ['user_id' => $user_id, 'attendance_date' => $selectedDate],
                        [
                            'status'      => $data['status'],
                            'notes'       => $data['notes'] ?? null,
                            'recorded_by' => Auth::user()->full_name ?? Auth::user()->name,
                            'updated_at'  => now(),
                            'created_at'  => now(),
                        ]
                    );
                }
            }
        }

        return back()->with('success', 'تم حفظ سجل المحفظين لتاريخ ' . $selectedDate);
    }
}
