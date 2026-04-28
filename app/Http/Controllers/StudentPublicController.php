<?php

namespace App\Http\Controllers;

use App\BusinessLogic\StudentLogic;
use App\Models\Student;
use App\Models\Course;
use App\Models\Group;
use Illuminate\Http\Request;
Use App\BusinessLogic\UserLogic;

class StudentPublicController extends Controller
{   
    protected $studentLogic;
    private $userLogic;
    public function __construct(StudentLogic $studentLogic, UserLogic $userLogic)
    {
        $this->studentLogic = $studentLogic;
        $this->userLogic = $userLogic;
    }


     public function index(Request $request)
    {
         $groups = Group::all();
         $teachers = $this->userLogic->getAllTeachers();
        $student_courses = Course::where(function ($q) {
            $q->where('type', 'students')
                ->orWhereNull('type');
        })->get();

        return view('students.create_public', compact('student_courses', 'groups', 'teachers'));
    }


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
            'user_id'      => 'required',
        ]);
        $this->studentLogic->storeStudentPublic($validated);
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => 'تم إضافة الطالب'])
            : redirect()->route('student_public.index')->with('success', 'تم إضافة الطالب بنجاح');
    }
}
?>