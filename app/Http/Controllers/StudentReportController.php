<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\BusinessLogic\StudentReportLogic;
use Illuminate\Support\Facades\Auth;

class StudentReportController extends Controller
{
    protected $reportLogic;

    public function __construct(StudentReportLogic $reportLogic)
    {
        $this->reportLogic = $reportLogic;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $teachers = $this->reportLogic->getTeachers();
        $groups = $this->reportLogic->getGroupsForUser($user);

        if ($request->ajax()) {
            $students = $this->reportLogic->getFilteredStudents($user, $request->all());
            $formattedData = $this->reportLogic->formatStudentData($students);

            return response()->json($formattedData);
        }

        $students = $this->reportLogic->getFilteredStudents($user, []);

        return view('reports.students_report', compact('students', 'teachers', 'groups'));
    }

    public function getGroupsByTeacher($teacherId)
    {
        $groups = Group::where('UserId', $teacherId)->get(['id', 'GroupName']);
        return response()->json($groups);
    }

    public function getGroupTeacher($groupId)
    {
        $group = Group::find($groupId);
        if ($group) {
            return response()->json(['UserId' => $group->UserId]);
        }
        return response()->json(['error' => 'Not found'], 404);
    }
}
