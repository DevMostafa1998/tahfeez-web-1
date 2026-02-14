<?php

namespace App\Http\Controllers;

use App\BusinessLogic\StudentLogic;
use App\Models\Student;
use App\Models\Course;
use App\Models\Group;
use Illuminate\Http\Request;

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
        if ($request->has('export_all')) {
            return $this->studentLogic->exportStudentsData();
        }

        if ($request->ajax()) {
            $data = $this->studentLogic->getDatatableStudents($request);
            return response()->json($data);
        }

        return view('students.index');
    }

    public function exportExcel()
    {
        return $this->studentLogic->exportStudentsData();
    }

    //  لجلب بيانات طالب واحد للمودال
    public function getStudentData($id)
    {
        $student = Student::findOrFail($id);
        return response()->json($student);
    }
    public function getStudentCourses($id)
    {
        $student = Student::findOrFail($id);
        $courseIds = $student->courses()->pluck('course_id')->toArray();
        return response()->json($courseIds);
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
            'is_displaced'    => 'required|in:0,1',
            'courses'         => 'nullable|array',
            'birth_place'     => 'required|string|max:255',
            'center_name'     => 'required|string|max:255',
            'mosque_name'     => 'required|string|max:255',
            'mosque_address'  => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:15',
            'gender'          => 'sometimes|in:male,female',
        ]);
        $this->studentLogic->storeStudent($validated);

        return $request->ajax()
            ? response()->json(['success' => true, 'message' => 'تم إضافة الطالب'])
            : redirect()->route('student.index')->with('success', 'تم إضافة الطالب بنجاح');
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
            'full_name'      => 'required|string|max:255',
            'id_number'      => 'required|string|digits:9',
            'date_of_birth'  => 'required|date',
            'phone_number'   => 'required|string|max:15',
            'address'        => 'required|string',
            'is_displaced'  => 'required|in:0,1',
            'birth_place'    => 'nullable|string|max:255',
            'center_name'    => 'nullable|string|max:255',
            'mosque_name'    => 'nullable|string|max:255',
            'mosque_address' => 'nullable|string|max:255',
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
        if ($this->studentLogic->restoreStudent($id)) {
            return redirect()->back()->with('success', 'تم استعادة سجل الطالب بنجاح');
        }
        return redirect()->back()->with('error', 'الطالب غير موجود');
    }
}
