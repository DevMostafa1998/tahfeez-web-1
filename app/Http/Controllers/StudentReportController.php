<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. التحضير للبيانات الأولية للقوائم المنسدلة (Dropdowns)
        $teachers = DB::table('user')
            ->select('id', 'full_name')
            ->where('is_admin', 0)
            ->whereNull('deleted_at')
            ->get();

        // تم تغيير اسم العمود إلى UserId بناءً على ملف الـ Migration
        $groups = ($user->is_admin == 1)
            ? Group::all()
            : Group::where('UserId', $user->id)->get();

        // 2. إذا كان طلب AJAX للفلترة (هذا الجزء المسؤول عن تحديث الجدول)
        if ($request->ajax()) {
            $query = Student::with(['groups.teacher']);

            // أ. فلترة الطلاب حسب صلاحية المستخدم:
            // إذا كان المستخدم محفظاً، يرى فقط الطلاب المنتمين لمجموعاته
            if ($user->is_admin != 1) {
                $query->whereHas('groups', function ($q) use ($user) {
                    $q->where('UserId', $user->id);
                });
            }

            // ب. فلتر المحفظ المسؤول (خاص بالمدير فقط):
            // نبحث عن الطلاب المرتبطين بمجموعات تابعة للمحفظ المختار
            if ($user->is_admin == 1 && $request->filled('UserId')) {
                $query->whereHas('groups', function ($q) use ($request) {
                    $q->where('UserId', $request->UserId);
                });
            }

            // ج. فلتر المجموعة المحددة:
            if ($request->filled('group_id')) {
                $query->whereHas('groups', function ($q) use ($request) {
                    $q->where('group_id', $request->group_id); // الربط عبر جدول student_group
                });
            }

            $students = $query->get();

            // تنسيق البيانات لإرجاعها بصيغة JSON لـ JavaScript
            return response()->json($students->map(function ($student) {
                return [
                    'full_name'      => $student->full_name,
                    'id_number'      => $student->id_number,
                    'date_of_birth'  => $student->date_of_birth,
                    'birth_place'    => $student->birth_place,
                    'phone_number'   => $student->phone_number,
                    'whatsapp_number' => $student->whatsapp_number,
                    'address'        => $student->address,
                    'center_name'    => $student->center_name,
                    'mosque_name'    => $student->mosque_name,
                    'is_displaced'   => (bool)$student->is_displaced,
                    'groups'         => $student->groups->map(function ($g) {
                        return ['GroupName' => $g->GroupName];
                    }),
                    // جلب اسم المحفظ من أول مجموعة مرتبطة بالطالب
                    'teacher_name'   => $student->groups->first() && $student->groups->first()->teacher
                        ? $student->groups->first()->teacher->full_name
                        : 'غير محدد'
                ];
            }));
        }

        // 3. الطلب العادي (عند تحميل الصفحة لأول مرة)
        $initialQuery = Student::with(['groups.teacher']);

        if ($user->is_admin != 1) {
            $initialQuery->whereHas('groups', function ($q) use ($user) {
                $q->where('UserId', $user->id);
            });
        }

        $students = $initialQuery->get();

        return view('reports.students_report', compact('students', 'teachers', 'groups'));
    }
    // جلب المجموعات الخاصة بمحفظ معين (لاستخدامها في الفلتر)
    public function getGroupsByTeacher($teacherId)
    {
        // البحث عن المجموعات المرتبطة بـ UserId
        $groups = Group::where('UserId', $teacherId)->get(['id', 'GroupName']);
        return response()->json($groups);
    }
    public function getGroupTeacher($groupId)
    {
        // جلب المجموعة ومعرفة المحفظ المرتبط بها
        $group = Group::find($groupId);
        if ($group) {
            return response()->json(['UserId' => $group->UserId]);
        }
        return response()->json(['error' => 'Not found'], 404);
    }
}
