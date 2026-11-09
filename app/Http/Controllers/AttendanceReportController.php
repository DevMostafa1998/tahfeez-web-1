<?php

namespace App\Http\Controllers;

use App\BusinessLogic\AttendanceReportLogic;
use App\BusinessLogic\ExportExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceReportController extends Controller
{
    protected $logic;
    protected $exportExcel;

    public function __construct(AttendanceReportLogic $logic, ExportExcel $export)
    {
        $this->logic = $logic;
        $this->exportExcel = $export;
    }

    public function index()
    {
        $data = $this->logic->getInitialData(Auth::user());
        return view('reports.attendance_report', $data);
    }

    public function getAttendanceData(Request $request)
    {
        $response = $this->logic->getDataTableAttendance(Auth::user(), $request->all());
        return response()->json($response);
    }

    public function getFiltersData(Request $request)
    {
        $filters = $this->logic->getDynamicFilters($request->all());
        return response()->json($filters);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->logic->getAllAttendanceForExport(Auth::user(), $request->all());

        return $this->exportExcel->export(
            'تقرير_الحضور_' . date('Y-m-d'),
            'تقرير حضور وغياب الطلاب المفصل',
            ['التاريخ', 'اسم الطالب', 'رقم الهوية', 'الهاتف', 'الحالة', 'الملاحظات'],
            $data,
            ['date', 'name', 'id', 'phone', 'status', 'notes'] 
        );
    }
}
