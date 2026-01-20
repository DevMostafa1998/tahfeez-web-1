<?php

namespace App\Http\Controllers;

use App\BusinessLogic\StudentLogic;
use App\Models\Student;
use Illuminate\Http\Request;

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
    public function index()
    {
        $students = $this->studentLogic->getAllStudents();
        return view('students.index', compact('students'));
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
            'id_number'     => 'required|numeric|digits:9',
            'phone_number'  => 'required|numeric|digits:10',
            'date_of_birth' => 'required|date',
            'address'       => 'required|string',
            'is_displaced'  => 'required|boolean',
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
