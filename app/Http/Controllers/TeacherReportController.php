<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BusinessLogic\TeacherReportLogic;

class TeacherReportController extends Controller
{
    protected $logic;

    public function __construct(TeacherReportLogic $logic)
    {
        $this->logic = $logic;
    }

    public function index(Request $request)
    {
        // جلب القوائم للفلاتر
        $filterLists = $this->logic->getFilterLists();

        // إذا كان الطلب تصدير إكسل
        if ($request->has('export')) {
            return $this->logic->exportToExcel($request->all());
        }

        // إذا كان طلب Ajax من DataTable
        if ($request->ajax()) {
            return response()->json($this->logic->getTeacherCoursesReport($request->all()));
        }

        return view('reports.teachers_courses', array_merge(
            ['reportData' => []],
            $filterLists
        ));
    }
}
