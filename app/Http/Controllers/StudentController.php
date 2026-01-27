<?php

namespace App\Http\Controllers;

use App\BusinessLogic\StudentLogic;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    protected $studentLogic;
    /**
     * Display a listing of the resource.
     */
    public function __construct(StudentLogic $studentLogic)
    {
        $this->studentLogic = $studentLogic;
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->is_admin == 1;
        $today = \Carbon\Carbon::today();

        // 1. نبدأ باستعلام جدول الطلاب (مع تحديد الأعمدة لتجنب تضارب الـ IDs)
        $query = DB::table('student')
            ->select('student.*')
            ->whereNull('student.deleted_at');

        // 2. إذا كان المستخدم "محفظ" (ليس أدمن)، نعرض طلابه فقط من خلال المجموعات
        if (!$isAdmin) {
            $query->join('student_group', 'student.id', '=', 'student_group.student_id')
                ->join('group', 'student_group.group_id', '=', 'group.id')
                ->where('group.UserId', $user->id)
                ->distinct(); // لمنع تكرار الطالب إذا كان مسجلاً في أكثر من مجموعة
        }

        // 3. تطبيق فلتر "لم يسمعوا اليوم" إذا تم طلبه من لوحة التحكم
        if ($request->filter == 'not_memorized_today') {
            // جلب معرفات الطلاب الذين سمعوا اليوم (من جدول التسميع اليومي)
            $who_memorized = DB::table('student_daily_memorizations')
                ->whereDate('date', $today)
                ->pluck('student_id');

            // استبعاد من قاموا بالتسميع من النتائج
            $query->whereNotIn('student.id', $who_memorized);
        }

        // 4. تنفيذ الاستعلام مع التقسيم لصفحات والحفاظ على روابط الفلتر
        $students = $query->paginate(10)->withQueryString();

        // 5. العودة للملف (تأكد من اسم الملف هل هو student.index أم students.index)
        return view('students.index', compact('students', 'isAdmin'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'     => 'required|string|max:255',
            'id_number'     => 'required|string|unique:student,id_number|digits:9',
            'date_of_birth' => 'required|date',
            'phone_number'  => 'required|string|max:15',
            'address'       => 'required|string',
            'is_displaced'  => 'required|boolean',
            'birth_place'   => 'required|string|max:255',
            'center_name'   => 'required|string|max:255',
            'mosque_name'   => 'required|string|max:255',
            'mosque_address' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:15',

        ]);
        $student = $this->studentLogic->storeStudent($validated);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الطالب بنجاح',
                'student' => $student
            ]);
        }
        return redirect()->route('student.index')->with('success', 'تم إضافة الطالب بنجاح');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $student = $this->studentLogic->getStudentById($id);
        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'full_name'     => 'required|string|max:255',
            'id_number'     => 'required|string|digits:9',
            'date_of_birth' => 'required|date',
            'phone_number'  => 'required|string|max:15',
            'address'       => 'required|string',
            'is_displaced'  => 'required|boolean',
            'birth_place'   => 'required|string|max:255',
            'center_name'   => 'required|string|max:255',
            'mosque_name'   => 'required|string|max:255',
            'mosque_address' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:15',
        ]);

        $student = $this->studentLogic->updateStudent($id, $validatedData);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث بيانات الطالب بنجاح',
                'student' => $student
            ]);
        }

        return redirect()->route('student.index')->with('success', 'تم تحديث بيانات الطالب بنجاح');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->studentLogic->deleteStudent($id);
        return redirect()->route('student.index')->with('success', 'تم حذف الطالب بنجاح');
    }
}
