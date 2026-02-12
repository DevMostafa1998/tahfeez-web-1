<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BusinessLogic\ReportLogic;
use App\BusinessLogic\ExportExcel;

class ReportController extends Controller
{
    protected $reportLogic;
    protected $exportExcel;
    public function __construct(ReportLogic $logic, ExportExcel $export)
    {
        $this->reportLogic = $logic;
        $this->exportExcel = $export;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->reportLogic->getRecitationQuery($request->all());

            // حساب العدد الكلي قبل التقسيم (Pagination)
            $totalData = $query->count();

            // تطبيق البحث الخاص بـ DataTable (Search Box)
            if ($search = $request->input('search.value')) {
                $query->where(function ($q) use ($search) {
                    $q->where('student_name', 'LIKE', "%{$search}%")
                        ->orWhere('sura_name', 'LIKE', "%{$search}%");
                });
            }

            $totalFiltered = $query->count();

            // التقسيم (Pagination)
            $start = $request->input('start');
            $length = $request->input('length');
            $data = $query->orderBy('recitation_date', 'desc')
                ->offset($start)
                ->limit($length)
                ->get();

            return response()->json([
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data
            ]);
        }

        $filterLists = $this->reportLogic->getFilterLists();
        return view('reports.memorization', $filterLists);
    }
    public function exportExcel(Request $request)
    {
        // جلب البيانات بناءً على الفلاتر فقط دون limit
        $data = $this->reportLogic->getRecitationQuery($request->all())
            ->orderBy('recitation_date', 'desc')
            ->get()
            ->toArray();

        $headers = ['التاريخ', 'اسم الطالب', 'رقم الهوية', 'المجموعة', 'المحفظ', 'السورة', 'من آية', 'إلى آية', 'الملاحظات'];
        $mapping = ['recitation_date', 'student_name', 'student_id_number', 'group_name', 'teacher_name', 'sura_name', 'verses_from', 'verses_to', 'note'];

        return $this->exportExcel->export(
            'Report_' . date('Y-m-d'),
            'تقرير التسميع للطلاب',
            $headers,
            $data,
            $mapping
        );
    }
    public function getFiltersData(Request $request)
    {
        $teacherId = $request->teacher_id;
        $groupId = $request->group_id;
        $studentId = $request->student_id;

        $teachers = \App\Models\User::query()
            ->where('is_admin', false)
            ->when($groupId, function ($q) use ($groupId) {
                return $q->whereHas('groups', fn($sq) => $sq->where('id', $groupId));
            })
            ->when($studentId, function ($q) use ($studentId) {
                return $q->whereHas('groups', fn($sq) => $sq->whereHas('students', fn($ssq) => $ssq->where('student.id', $studentId)));
            })
            ->get(['id', 'full_name']);

        $groups = \App\Models\Group::query()
            ->when($teacherId, fn($q) => $q->where('UserId', $teacherId))
            ->when($studentId, fn($q) => $q->whereHas('students', fn($sq) => $sq->where('student.id', $studentId)))
            ->get(['id', 'GroupName']);

        $students = \App\Models\Student::query()
            ->when($teacherId, function ($q) use ($teacherId) {
                return $q->whereHas('groups', fn($sq) => $sq->where('UserId', $teacherId));
            })
            ->when($groupId, function ($q) use ($groupId) {
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
