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
        // جلب البيانات (تعتمد على الفلترة داخل اللوجيك)
        $data = $this->logic->getTeacherCoursesReport($request->all());

        if ($request->ajax()) {
            return response()->json($data);
        }

        // جلب القوائم (Dropdowns)
        $filterLists = $this->logic->getFilterLists();

        return view('reports.teachers_courses', array_merge(
            ['reportData' => $data],
            $filterLists
        ));
    }
}
