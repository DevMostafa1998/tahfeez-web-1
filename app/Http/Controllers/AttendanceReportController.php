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

        $exporter = new \App\BusinessLogic\ExportExcel();
        return $exporter->export(
            'تقرير_الحضور',                    // اسم الملف
            'تقرير حضور وغياب الطلاب المفصل',   // عنوان التقرير داخل الملف
            ['التاريخ', 'اسم الطالب', 'الهوية', 'الهاتف', 'الحالة'], // الهيدرز
            $data,                             // البيانات
            ['date', 'name', 'id', 'phone', 'status'] // المابينج
        );
    }

    // تحديث ميثود جلب البيانات لتناسب DataTables

}
