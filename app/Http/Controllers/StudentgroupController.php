<?php

namespace App\Http\Controllers;

// use Illuminate\Container\Attributes\DB;

use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\BusinessLogic\StudentgroupLogic;

class StudentgroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $studentGroupLogic;

    public function __construct(StudentgroupLogic $studentGroupLogic)
    {
        $this->studentGroupLogic = $studentGroupLogic;
    }
    public function index()
    {
        $data = $this->studentGroupLogic->getIndexData();
        return view('groups.index', $data);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:group,id',
            'student_ids' => 'array',
            'student_ids.*' => 'exists:student,id',
        ]);

        $this->studentGroupLogic->syncStudentsToGroup(
            $request->group_id,
            $request->input('student_ids', [])
        );

        return redirect()->back()->with('success', 'تم تحديث قائمة الطلاب بنجاح');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // في ملف StudentgroupController.php

    public function update(Request $request, string $id)
    {
        $request->validate([
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:student,id', // تأكد أن اسم الجدول student وليس students إذا كان المودل يستخدم ذلك
        ]);

        $this->studentGroupLogic->syncStudentsToGroup(
            $id,
            $request->input('student_ids', [])
        );

        return redirect()->back()->with('success', 'تم تحديث قائمة الطلاب بنجاح');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
