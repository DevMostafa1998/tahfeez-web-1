<?php

namespace App\Http\Controllers;

use App\BusinessLogic\AttendanceReportLogic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceReportController extends Controller
{
    protected $logic;

    public function __construct(AttendanceReportLogic $logic)
    {
        $this->logic = $logic;
    }

    public function index()
    {
        $data = $this->logic->getInitialData(Auth::user());
        return view('reports.attendance_report', $data);
    }

    public function getAttendanceData(Request $request)
    {
        if (!$request->date_from || !$request->date_to) {
            return response()->json([], 400);
        }

        $attendances = $this->logic->getFilteredAttendance(Auth::user(), $request->all());
        return response()->json($attendances);
    }

    public function getFiltersData(Request $request)
    {
        $filters = $this->logic->getDynamicFilters($request->all());
        return response()->json($filters);
    }
}
