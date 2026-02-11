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

    public function __construct(StudentLogic $studentLogic)
    {
        $this->studentLogic = $studentLogic;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. نبدا بالاستعلام الاساسي باستخدام المودل للاستفادة من SoftDeletes
        $query = Student::select('student.*')
            ->addSelect([
                'courses_count' => DB::table('course_student')
                    ->whereColumn('student_id', 'student.id')
                    ->selectRaw('count(*)'),
                'course_ids' => DB::table('course_student')
                    ->whereColumn('student_id', 'student.id')
                    ->selectRaw("GROUP_CONCAT(course_id)")
            ]);

        $user = Auth::user();
        $isAdmin = $user->is_admin == 1;

        // 2. التعامل مع حالة المحذوفين (الارشيف)
        if ($request->has('status') && $request->status == 'trash') {
            $query->onlyTrashed();
            $query->orderBy('deleted_at', 'desc');
        } else {
            $query->whereNull('deleted_at');
        }

        // 3. إذا لم يكن أدمن، نعرض طلابه فقط
        if (!$isAdmin) {
            $query->join('student_group', 'student.id', '=', 'student_group.student_id')
                ->join('group', 'student_group.group_id', '=', 'group.id')
                ->where('group.UserId', $user->id)
                ->distinct();
        }

        // 4. فلتر "لم يسمعوا اليوم"
        if ($request->filter == 'not_memorized_today') {
            $today = \Carbon\Carbon::today();
            $who_memorized = DB::table('student_daily_memorizations')
                ->whereDate('date', $today)
                ->pluck('student_id');

            $query->whereNotIn('student.id', $who_memorized);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%$search%")
                  ->orWhere('id_number', 'LIKE', "%$search%");
            });
        }

        $perPage = $request->get('length', 10);
        $students = $query->get();

        $groups = Group::all();
        $student_courses = Course::where(function ($q) {
            $q->where('type', 'students')
                ->orWhereNull('type');
        })->get();

        return view('students.index', compact('students', 'student_courses', 'groups'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Group::all();
        $student_courses = Course::where(function ($q) {
            $q->where('type', 'students')
                ->orWhereNull('type');
        })->get();

        return view('students.create', compact('student_courses', 'groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'       => 'required|string|max:255',
            'id_number'       => 'required|string|unique:student,id_number|digits:9',
            'date_of_birth'   => 'required|date',
            'phone_number'    => 'required|string|max:15',
            'address'         => 'required|string',
            'group_id'        => 'nullable|exists:group,id',
            'is_displaced'    => 'required|boolean',
            'courses'         => 'nullable|array',
            'birth_place'     => 'required|string|max:255',
            'center_name'     => 'required|string|max:255',
            'mosque_name'     => 'required|string|max:255',
            'mosque_address'  => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:15',
            'gender'          => 'sometimes|in:male,female',
        ]);
        $this->studentLogic->storeStudent($validated);
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم إضافة الطالب']);
        }
        return redirect()->route('student.index')->with('success', 'تم إضافة الطالب والدورات بنجاح');
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
            $student = Student::findOrFail($id);
            $student->courses()->sync($request->input('courses', []));
            return redirect()->back()->with('success', 'تم تحديث الدورات بنجاح');
        }

        $validatedData = $request->validate([
            'full_name'       => 'required|string|max:255',
            'id_number'       => 'required|string|digits:9|unique:student,id_number,' . $id,
            'date_of_birth'   => 'required|date',
            'phone_number'    => 'required|string|max:15',
            'address'         => 'required|string',
            'is_displaced'    => 'required',
            'birth_place'     => 'nullable|string|max:255',
            'center_name'     => 'nullable|string|max:255',
            'mosque_name'     => 'nullable|string|max:255',
            'mosque_address'  => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:15',
            'gender'          => 'sometimes|in:male,female',
        ]);

        $this->studentLogic->updateStudent($id, $validatedData);

        return redirect()->route('student.index')->with('success', 'تم تحديث بيانات الطالب بنجاح');
    }
    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy($id)
    {
        $this->studentLogic->deleteStudent($id);
        return redirect()->route('student.index')->with('success', 'تم حذف الطالب بنجاح');
    }

    public function restore($id)
    {
        // البحث عن الطالب حتى لو كان محذوفاً
        $student = Student::withTrashed()->find($id);

        if ($student) {
            $student->restore();
            return redirect()->back()->with('success', 'تم استعادة سجل الطالب بنجاح');
        }

        return redirect()->back()->with('error', 'الطالب غير موجود');
    }
}
