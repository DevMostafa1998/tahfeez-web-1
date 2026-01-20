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
        //  اخذ قيم الفلاتر من الطلب
        $date = $request->input('date', date('Y-m-d'));
        $searchId = $request->input('search_id');
        $teacherId = $request->input('teacher_id');
        $groupId = $request->input('group_id');

        $user = Auth::user();

        //  جلب المحفظين (للأدمن فقط)
        $teachers = User::where('is_admin', 0)->get();

        //   جلب المجموعات بناءً على المحفظ المختار (للفلترة)
        $groupsQuery = Group::query();
        if (!$user->is_admin) {
            // المحفظ العادي يرى مجموعاته فقط
            $groupsQuery->where('UserId', $user->id);
        } else {
            // الأدمن: إذا اختار محفظاً تظهر مجموعاته، وإلا تظهر كل المجموعات
            if ($teacherId) {
                $groupsQuery->where('UserId', $teacherId);
            }
        }
        $groups = $groupsQuery->get();

        // 4. بناء استعلام الطلاب مع العلاقات الأساسية
        $query = Student::query()->with(['groups.teacher', 'teacher']);

        // تصفية الصلاحيات: المحفظ يرى طلابه فقط
        if (!$user->is_admin) {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('groups', function($g) use ($user) {
                      $g->where('UserId', $user->id);
                  });
            });
        }

        //  تطبيق فلاتر البحث
        if ($searchId) {
            $query->where('id_number', 'like', "%{$searchId}%");
        }

        if ($teacherId && $user->is_admin) {
            $query->where(function($q) use ($teacherId) {
                $q->where('user_id', $teacherId)
                  ->orWhereHas('groups', function($g) use ($teacherId) {
                      $g->where('UserId', $teacherId);
                  });
            });
        }

        if ($groupId) {
            $query->whereHas('groups', function($g) use ($groupId) {
                $g->where('group.id', $groupId);
            });
        }

        // 6. جلب النتائج وربطها بحالة الحضور من جدول student_attendances
        $students = $query->whereNull('deleted_at')->get()->map(function($student) use ($date) {
            $attendance = DB::table('student_attendances')
                ->where('student_id', $student->id)
                ->where('attendance_date', $date)
                ->first();

            $student->today_status = $attendance ? $attendance->status : null;
            $student->today_notes = $attendance ? $attendance->notes : '';

            // تحديد اسم المجموعة والمحفظ للعرض في الجدول
            $student->group_name = $student->groups->first()->GroupName ?? 'بدون مجموعة';
            $student->teacher_name = $student->groups->first()->teacher->full_name ?? ($student->teacher->full_name ?? 'غير محدد');

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
                        'recorded_by' => Auth::user()->full_name,
                        'updated_at'  => now(),
                        'created_at'  => now(),
                    ]
                );
            }
        }

        return back()->with('success', 'تم حفظ سجل حضور الطلاب لتاريخ ' . $selectedDate);
    }

    //  حضور المحفظين
    public function teachersAttendance(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));

        $teachers = User::where('is_admin', 0)
            ->get()
            ->map(function($teacher) use ($date) {
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

        foreach ($request->teachers as $user_id => $data) {
            DB::table('teacher_attendances')->updateOrInsert(
                ['user_id' => $user_id, 'attendance_date' => $selectedDate],
                [
                    'status'      => $data['status'],
                    'notes'       => $data['notes'] ?? null,
                    'recorded_by' => Auth::user()->full_name,
                    'updated_at'  => now(),
                    'created_at'  => now(),
                ]
            );
        }
        return back()->with('success', 'تم حفظ سجل المحفظين لتاريخ ' . $selectedDate);
    }
}
