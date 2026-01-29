<?php

namespace App\Http\Controllers;

use App\BusinessLogic\StudentLogic;
use App\Models\Student;
use App\Models\Course;
use App\Models\Group;
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
         $query = Student::with(['courses', 'group']); // جلب الطلاب مع دوراتهم ومجموعاتهم
        $user = Auth::user();
        $isAdmin = $user->is_admin == 1;
        $today = \Carbon\Carbon::today();

        // 1. نبدأ باستعلام جدول الطلاب (مع تحديد الأعمدة لتجنب تضارب الـ IDs)
$query = DB::table('student')
    ->select('student.*')
    ->addSelect([
        'courses_count' => DB::table('course_student')
            ->whereColumn('student_id', 'student.id')
            ->selectRaw('count(*)'),
        'course_ids' => DB::table('course_student')
            ->whereColumn('student_id', 'student.id')
            ->selectRaw("GROUP_CONCAT(course_id)")
    ])
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
            $today = \Carbon\Carbon::today();
            $who_memorized = DB::table('student_daily_memorizations')
                ->whereDate('date', $today)
                ->pluck('student_id');
           $query->whereNotIn('id', $who_memorized);
            // استبعاد من قاموا بالتسميع من النتائج
            $query->whereNotIn('student.id', $who_memorized);
        }

        // 4. تنفيذ الاستعلام مع التقسيم لصفحات والحفاظ على روابط الفلتر
        $students = $query->paginate(10)->withQueryString();

                $groups = Group::all();
                $student_courses = Course::where(function($q) {
            $q->where('type', 'students')
              ->orWhereNull('type');
        })->get();

        // 5. العودة للملف (تأكد من اسم الملف هل هو student.index أم students.index)
        return view('students.index', compact('students', 'student_courses', 'groups'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
$groups = Group::all();

        $student_courses = Course::where(function($q) {
            $q->where('type', 'students')
              ->orWhereNull('type');
        })->get();

        return view('students.create', compact('student_courses', 'groups'));    }

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
            'group_id'      => 'nullable|exists:group,id',
            'is_displaced'  => 'nullable',
            'courses'       => 'nullable|array',
            'birth_place'   => 'required|string|max:255',
            'center_name'   => 'required|string|max:255',
            'mosque_name'   => 'required|string|max:255',
            'mosque_address' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:15',

        ]);
$this->studentLogic->storeStudent($validated);        if ($request->ajax()) {
                        return response()->json(['success' => true, 'message' => 'تم إضافة الطالب']);

        }
        return redirect()->route('student.index')->with('success', 'تم إضافة الطالب والدورات بنجاح');
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
    if ($request->has('update_courses_only')) {
        $student = \App\Models\Student::findOrFail($id);
        $student->courses()->sync($request->input('courses', []));
        return redirect()->back()->with('success', 'تم تحديث الدورات بنجاح');
    }

    // تعديل الطالب: جعلنا الحقول التي قد تكون فارغة nullable
    $validatedData = $request->validate([
        'full_name'      => 'required|string|max:255',
        'id_number'      => 'required|string|digits:9',
        'date_of_birth'  => 'required|date',
        'phone_number'   => 'required|string|max:15',
        'address'        => 'required|string',
        'is_displaced'   => 'required',
        'birth_place'    => 'nullable|string|max:255',
        'center_name'    => 'nullable|string|max:255',
        'mosque_name'    => 'nullable|string|max:255',
        'mosque_address' => 'nullable|string|max:255',
        'whatsapp_number' => 'nullable|string|max:15',
    ]);

    $this->studentLogic->updateStudent($id, $validatedData);

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
