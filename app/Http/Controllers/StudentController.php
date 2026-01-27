<?php

namespace App\Http\Controllers;

use App\BusinessLogic\StudentLogic;
use App\Models\Student;
use App\Models\Course;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    protected $studentLogic;

    public function __construct(StudentLogic $studentLogic)
    {
        $this->studentLogic = $studentLogic;
    }

    public function index(Request $request)
    {
        $query = Student::with(['courses', 'group']); // جلب الطلاب مع دوراتهم ومجموعاتهم

        if ($request->filter == 'not_memorized_today') {
            $today = \Carbon\Carbon::today();
            $who_memorized = DB::table('student_daily_memorizations')
                ->whereDate('date', $today)
                ->pluck('student_id');
            $query->whereNotIn('id', $who_memorized);
        }

        $students = $query->paginate(10)->withQueryString();

        // جلب المجموعات المتاحة للاختيار منها
        $groups = Group::all();

        $student_courses = Course::where(function($q) {
            $q->where('type', 'students')
              ->orWhereNull('type');
        })->get();

        return view('students.index', compact('students', 'student_courses', 'groups'));
    }

    public function create()
    {
        $groups = Group::all();

        $student_courses = Course::where(function($q) {
            $q->where('type', 'students')
              ->orWhereNull('type');
        })->get();

        return view('students.create', compact('student_courses', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'     => 'required|string|max:255',
            'id_number'     => 'required|string|unique:student,id_number|digits:9',
            'date_of_birth' => 'required|date',
            'phone_number'  => 'required|string',
            'address'       => 'required|string',
            'group_id'      => 'nullable|exists:group,id',
            'is_displaced'  => 'nullable',
            'courses'       => 'nullable|array',
        ]);

        $this->studentLogic->storeStudent($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم إضافة الطالب']);
        }
        return redirect()->route('student.index')->with('success', 'تم إضافة الطالب والدورات بنجاح');
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'full_name'     => 'required|string|max:255',
            'id_number'     => 'required|numeric|digits:9',
            'phone_number'  => 'required',
            'address'       => 'required|string',
            'is_displaced'  => 'nullable',
            'courses'       => 'nullable|array',
        ]);

        $this->studentLogic->updateStudent($id, $validatedData);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم التحديث بنجاح']);
        }
        return redirect()->route('student.index')->with('success', 'تم التحديث بنجاح');
    }

    public function destroy($id)
    {
        $this->studentLogic->deleteStudent($id);
        return redirect()->route('student.index')->with('success', 'تم حذف الطالب بنجاح');
    }
}
