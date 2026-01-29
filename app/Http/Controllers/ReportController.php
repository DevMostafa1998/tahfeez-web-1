<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BusinessLogic\ReportLogic;

class ReportController extends Controller
{
    protected $reportLogic;

    public function __construct(ReportLogic $logic)
    {
        $this->reportLogic = $logic;
    }

   public function index(Request $request)
{
    $filterLists = $this->reportLogic->getFilterLists();

    $memorizations = $this->reportLogic->getRecitationReport($request->all());

    if ($request->ajax()) {
        return response()->json($memorizations);
    }

    return view('reports.memorization', array_merge(
        ['memorizations' => $memorizations],
        $filterLists
    ));
}
public function getFiltersData(Request $request)
{
    $teacherId = $request->teacher_id;
    $groupId = $request->group_id;
    $studentId = $request->student_id;

    $teachers = \App\Models\User::query()
        ->where('is_admin', false)
        ->when($groupId, function($q) use ($groupId) {
            return $q->whereHas('groups', fn($sq) => $sq->where('id', $groupId));
        })
        ->when($studentId, function($q) use ($studentId) {
            return $q->whereHas('groups', fn($sq) => $sq->whereHas('students', fn($ssq) => $ssq->where('student.id', $studentId)));
        })
        ->get(['id', 'full_name']);

    $groups = \App\Models\Group::query()
        ->when($teacherId, fn($q) => $q->where('UserId', $teacherId))
        ->when($studentId, fn($q) => $q->whereHas('students', fn($sq) => $sq->where('student.id', $studentId)))
        ->get(['id', 'GroupName']);

    $students = \App\Models\Student::query()
        ->when($teacherId, function($q) use ($teacherId) {
            return $q->whereHas('groups', fn($sq) => $sq->where('UserId', $teacherId));
        })
        ->when($groupId, function($q) use ($groupId) {
            return $q->whereHas('groups', fn($sq) => $sq->where('group.id', $groupId));
        })
        ->get(['id', 'full_name']);

    return response()->json([
        'teachers' => $teachers,
        'groups' => $groups,
        'students' => $students
    ]);
}
}
