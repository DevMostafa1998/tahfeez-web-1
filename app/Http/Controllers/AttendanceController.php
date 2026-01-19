<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $today = date('Y-m-d');
        $students = Student::where('user_id', Auth::id())
            ->get()
            ->map(function($student) use ($today) {
                $attendance = DB::table('attendances')
                    ->where('student_id', $student->id)
                    ->where('attendance_date', $today)
                    ->first();
                $student->today_status = $attendance ? $attendance->status : null;
                $student->today_notes = $attendance ? $attendance->notes : '';
                return $student;
            });

        return view('students.attendance', compact('students'));
    }

    // عرض صفحة حضور المحفظين
    public function teachersAttendance(Request $request)
{
    // استقبال التاريخ المختار أو استخدام تاريخ اليوم كافتراضي
    $date = $request->input('date', date('Y-m-d'));

    $teachers = User::where('is_admin', 0)
        ->get()
        ->map(function($teacher) use ($date) {
            $att = DB::table('teacher_attendances')
                ->where('user_id', $teacher->id)
                ->where('attendance_date', $date) // البحث بناءا على التاريخ المختار
                ->first();

            $teacher->today_status = $att ? $att->status : null;
            $teacher->today_notes = $att ? $att->notes : '';
            return $teacher;
        });

    return view('users.teachers_attendance', compact('teachers', 'date'));
}

    // حفظ حضور المحفظين
   public function storeTeachersAttendance(Request $request)
{
    // استقبال التاريخ من الفورم المخفي لضمان الحفظ في اليوم الصحيح
    $selectedDate = $request->input('attendance_date', date('Y-m-d'));

    foreach ($request->teachers as $user_id => $data) {
        DB::table('teacher_attendances')->updateOrInsert(
            ['user_id' => $user_id, 'attendance_date' => $selectedDate],
            [
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'recorded_by' => Auth::user()->full_name,
                'updated_at' => now(),
                'created_at' => now(), // سيتم تجاهلها في حال التحديث
            ]
        );
    }
    return back()->with('success', 'تم حفظ السجل لتاريخ ' . $selectedDate);
}
}
