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

            // حساب العدد الكلي للجلاب قبل الفلترة بالبحث
            $totalData = $query->count();

            // تطبيق البحث الخاص بـ DataTable
            if ($search = $request->input('search.value')) {
                $query->where(function ($q) use ($search) {
                    $q->where('student_name', 'LIKE', "%{$search}%")
                        ->orWhere('student_id_number', 'LIKE', "%{$search}%")
                        ->orWhere('sura_name', 'LIKE', "%{$search}%")
                        ->orWhere('teacher_name', 'LIKE', "%{$search}%")
                        ->orWhere('group_name', 'LIKE', "%{$search}%");
                });
            }

            $totalFiltered = $query->count();

            // Pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $data = $query->offset($start)
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
        $rawData = $this->reportLogic->getRecitationQuery($request->all())
            ->get()
            ->toArray();

        $processedData = array_map(function ($row) {
            $row = (array) $row;

            $examTypeId = $row['examType_id'] ?? null;
            $noteText = $row['note'] ?? '';
            $suraName = $row['sura_name'] ?? '';

            if ($examTypeId == 0 || $examTypeId === '0' || str_contains($noteText, 'لم يقم بالتسميع اليوم') || $suraName === 'لم يسمع') {
                $row['recitation_status'] = 'لم يسمع';
                $row['sura_name'] = '';
                $row['verses_from'] = '';
                $row['verses_to'] = '';
            } else {
                $typeText = $row['exam_type_description'] ?? $row['exam_type_name'] ?? $row['description'] ?? '';

                if (empty(trim($typeText))) {
                    if (str_contains($noteText, 'مراجعة')) {
                        $typeText = 'مراجعة';
                    } elseif (str_contains($noteText, 'سرد') || str_contains($noteText, 'اختبار')) {
                        $typeText = 'سرد سورة';
                    } elseif ($examTypeId == 1 || $examTypeId === '1') {
                        $typeText = 'تسميع';
                    } elseif ($examTypeId == 2 || $examTypeId === '2') {
                        $typeText = 'مراجعة';
                    } elseif ($examTypeId == 3 || $examTypeId === '3') {
                        $typeText = 'سرد سورة';
                    } else {
                        $typeText = 'تسميع';
                    }
                }
                $row['recitation_status'] = $typeText;

                if (($row['verses_from'] ?? 0) == 0) {
                    $row['verses_from'] = '';
                }
                if (($row['verses_to'] ?? 0) == 0) {
                    $row['verses_to'] = '';
                }
            }

            return $row;
        }, $rawData);

        $headers = [
            'التاريخ',
            'اسم الطالب',
            'رقم الهوية',
            'المجموعة',
            'المحفظ',
            'حالة التسميع',
            'السورة',
            'من آية',
            'إلى آية',
            'الملاحظات'
        ];

        $mapping = [
            'recitation_date',
            'student_name',
            'student_id_number',
            'group_name',
            'teacher_name',
            'recitation_status',
            'sura_name',
            'verses_from',
            'verses_to',
            'note'
        ];

        return $this->exportExcel->export(
            'Report_' . date('Y-m-d'),
            'تقرير التسميع للطلاب',
            $headers,
            $processedData,
            $mapping
        );
    }

    public function getFiltersData(Request $request)
    {
        $teacherId = $request->teacher_id;
        $groupId = $request->group_id;
        $studentId = $request->student_id;
        $gender = $request->filled('gender') ? $request->gender : session('selected_gender');

        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : $request->center_id;

        $teachers = \App\Models\User::query()
            ->where('is_admin', false)
            ->when($selectedCenterId, fn($q) => $q->where('center_id', $selectedCenterId))
            ->when($groupId, fn($q) => $q->whereHas('groups', fn($sq) => $sq->where('id', $groupId)))
            ->when($studentId, fn($q) => $q->whereHas('groups', fn($sq) => $sq->whereHas('students', fn($ssq) => $ssq->where('student.id', $studentId))))
            ->when($gender, fn($q) => $q->whereHas('groups', fn($sq) => $sq->whereHas('students', fn($ssq) => $ssq->where('student.gender', $gender))))
            ->get(['id', 'full_name']);

        $groups = \App\Models\Group::query()
            ->when($selectedCenterId, fn($q) => $q->whereHas('teacher', fn($sq) => $sq->where('center_id', $selectedCenterId)))
            ->when($teacherId, fn($q) => $q->where('UserId', $teacherId))
            ->when($studentId, fn($q) => $q->whereHas('students', fn($sq) => $sq->where('student.id', $studentId)))
            ->when($gender, fn($q) => $q->whereHas('students', fn($sq) => $sq->where('student.gender', $gender)))
            ->get(['id', 'GroupName']);

        $students = \App\Models\Student::query()
            ->when($selectedCenterId, fn($q) => $q->whereHas('groups.teacher', fn($sq) => $sq->where('center_id', $selectedCenterId)))
            ->when($teacherId, fn($q) => $q->whereHas('groups', fn($sq) => $sq->where('UserId', $teacherId)))
            ->when($groupId, fn($q) => $q->whereHas('groups', fn($sq) => $sq->where('group.id', $groupId)))
            ->when($gender, fn($q) => $q->where('gender', $gender))
            ->get(['id', 'full_name']);

        return response()->json([
            'teachers' => $teachers,
            'groups'   => $groups,
            'students' => $students
        ]);
    }
}
