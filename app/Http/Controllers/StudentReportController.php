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

        if ($request->ajax()) {
            $filters = $request->all();
            $query = $this->reportLogic->buildFilteredQuery($user, $filters);

            $totalRecords = $query->count();

            if ($request->has('length') && $request->length != -1) {
                $query->skip($request->start)->take($request->length);
            }

            $students = $query->get();
            $formattedData = $this->reportLogic->formatStudentData($students);

            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $totalRecords,
                "data" => $formattedData
            ]);
        }

        $teachers = $this->reportLogic->getTeachers();
        $groups = $this->reportLogic->getGroupsForUser($user);

        return view('reports.students_report', compact('teachers', 'groups'));
    }


    public function getGroupsByTeacher($teacherId)
    {
        $groups = Group::where('UserId', $teacherId)->get(['id', 'GroupName']);
        return response()->json($groups);
    }

    public function getGroupTeacher($groupId)
    {
        $group = Group::with('teacher')->find($groupId);
        if ($group) {
            return response()->json([
                'UserId' => $group->UserId,
                'teacher_name' => $group->teacher ? $group->teacher->full_name : 'غير محدد'
            ]);
        }
        return response()->json(['error' => 'Not found'], 404);
    }
}
