<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\BusinessLogic\StudentReportLogic;
use Illuminate\Support\Facades\Auth;
use App\BusinessLogic\ExportExcel;

class StudentReportController extends Controller
{
    protected $reportLogic;
    protected $excelService;
    public function __construct(StudentReportLogic $reportLogic, ExportExcel $excelService)
    {
        $this->reportLogic = $reportLogic;
        $this->excelService = $excelService;
    }
    public function export(Request $request)
    {
        $user = Auth::user();
        $filters = $request->all();

        $query = $this->reportLogic->buildFilteredQuery($user, $filters);
        $students = $query->get();

        $data = $this->reportLogic->formatForExcel($students);

        $headers = [
            'الاسم الكامل',
            'رقم الهوية',
            'تاريخ الميلاد',
            'مكان الميلاد',
            'الهاتف',
            'واتساب',
            'العنوان',
            'المركز',
            'المسجد',
            'المجموعة',
            'المحفظ/ة',
            'الحالة'
        ];

        $columnsMapping = [
            'full_name',
            'id_number',
            'date_of_birth',
            'birth_place',
            'phone_number',
            'whatsapp_number',
            'address',
            'center_name',
            'mosque_name',
            'group_name',
            'teacher_name',
            'is_displaced'
        ];

        return $this->excelService->export(
            'تقرير_الطلاب_' . date('Y-m-d'),
            'تقرير بيانات الطلاب والمجموعات',
            $headers,
            $data,
            $columnsMapping
        );
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
