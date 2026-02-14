<?php

namespace App\BusinessLogic;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class StudentLogic
{

    public function storeStudent(array $data)
    {
        $studentData = [
            'full_name'       => $data['full_name'],
            'id_number'       => $data['id_number'],
            'date_of_birth'   => $data['date_of_birth'],
            'phone_number'    => $data['phone_number'],
            'address'         => $data['address'],
            'is_displaced'    => $data['is_displaced'],
            'user_id'         => Auth::id(),
            'creation_by'     => Auth::user()->id,
            'birth_place'     => $data['birth_place'] ?? null,
            'center_name'     => $data['center_name'] ?? null,
            'mosque_name'     => $data['mosque_name'] ?? null,
            'mosque_address'  => $data['mosque_address'] ?? null,
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
            'gender' => $data['gender'],
        ];

        $student = Student::create($studentData);

        if (!empty($data['group_id'])) {
            $student->groups()->attach($data['group_id']);
        }

        if (isset($data['courses']) && is_array($data['courses'])) {
            $student->courses()->attach($data['courses']);
        }

        return $student;
    }
    public function getDatatableStudents($request)
    {
        $showArchived = $request->input('archived') == 'true';
        $query = $showArchived ? Student::onlyTrashed() : Student::query();

        // 1. البحث
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('id_number', 'LIKE', "%{$search}%");
            });
        }

        // 2. الفرز
        if ($request->has('order')) {
            $columnIndex = $request->input('order.0.column');
            $columnName = $request->input("columns.{$columnIndex}.name");
            $columnDirection = $request->input('order.0.dir');

            if ($columnName && !in_array($columnName, ['actions', 'courses'])) {
                $query->orderBy($columnName, $columnDirection);
            }
        } else {
            $query->orderBy('full_name', 'asc');
        }

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $students = $query->offset($request->input('start'))
            ->limit($request->input('length'))
            ->get();

        // 3. التنسيق (Data Mapping)
        $data = $students->map(function ($student) use ($showArchived) {
            return $this->formatStudentForTable($student, $showArchived);
        });

        return [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ];
    }
    private function formatStudentForTable($student, $showArchived)
    {
        $actionButtons = $this->generateActionButtons($student, $showArchived);

        return [
            'full_name' => '<span class="fw-bold">' . $student->full_name . '</span>',
            'id_number' => '<span class="badge bg-light text-dark border px-4 py-2">' . $student->id_number . '</span>',
            'gender'    => $student->gender == 'male' ? '<span class="badge bg-blue-subtle text-primary border px-3">ذكر</span>' : '<span class="badge bg-pink-subtle text-danger border px-3">أنثى</span>',
            'status'    => $showArchived ? '<span class="badge rounded-pill bg-danger text-white">محذوف</span>' : ($student->is_displaced ? '<span class="badge rounded-pill bg-warning-subtle text-dark">نازح</span>' : '<span class="badge rounded-pill bg-success-subtle text-success">مقيم</span>'),
            'courses'   => '<span class="badge bg-warning text-dark rounded-pill px-3">' . ($student->courses()->count()) . ' دورات</span>',
            'actions'   => $actionButtons
        ];
    }
    private function generateActionButtons($student, $showArchived)
    {
        if ($showArchived) {
            return '<button onclick="restoreStudent(' . $student->id . ')" class="btn btn-sm btn-outline-success rounded-pill px-3"><i class="bi bi-arrow-counterclockwise"></i> استعادة</button>';
        }

        return '
            <div class="d-flex justify-content-center gap-2">
                <a href="' . route('parents.index', ['id_number' => $student->id_number]) . '" class="btn btn-sm btn-outline-secondary rounded-circle action-btn" title="عرض التقرير"><i class="bi bi-file-earmark-person"></i></a>
                <button class="btn btn-sm btn-outline-info rounded-circle action-btn course-btn" data-id="' . $student->id . '" data-name="' . $student->full_name . '" title="الدورات"><i class="bi bi-journal-plus"></i></button>
                <button class="btn btn-sm btn-outline-warning rounded-circle action-btn edit-student-btn" data-id="' . $student->id . '" title="تعديل"><i class="bi bi-pencil-square"></i></button>
                <button type="button" onclick="confirmDelete(' . $student->id . ')" class="btn btn-sm btn-outline-danger rounded-circle action-btn" title="حذف"><i class="bi bi-trash3"></i></button>
            </div>';
    }
    public function restoreStudent($id)
    {
        $student = Student::withTrashed()->find($id);
        return $student ? $student->restore() : false;
    }
    public function getAllStudents()
    {
        return Student::whereNull('deleted_at')->latest();
    }
    public function getFilteredStudents($request)
    {
        $user = Auth::user();
        $isAdmin = $user->is_admin == 1;

        $query = \Illuminate\Support\Facades\DB::table('student')
            ->select('student.*')
            ->addSelect([
                'courses_count' => \Illuminate\Support\Facades\DB::table('course_student')
                    ->whereColumn('student_id', 'student.id')
                    ->selectRaw('count(*)'),
                'course_ids' => \Illuminate\Support\Facades\DB::table('course_student')
                    ->whereColumn('student_id', 'student.id')
                    ->selectRaw("GROUP_CONCAT(course_id)")
            ])
            ->whereNull('student.deleted_at');

        // منطق الصلاحيات: إذا لم يكن أدمن يرى طلاب مجموعاته فقط
        if (!$isAdmin) {
            $query->join('student_group', 'student.id', '=', 'student_group.student_id')
                ->join('group', 'student_group.group_id', '=', 'group.id')
                ->where('group.UserId', $user->id)
                ->distinct();
        }

        // فلتر "لم يسمعوا اليوم"
        if ($request->filter == 'not_memorized_today') {
            $today = \Carbon\Carbon::today();
            $who_memorized = \Illuminate\Support\Facades\DB::table('student_daily_memorizations')
                ->whereDate('date', $today)
                ->pluck('student_id');

            $query->whereNotIn('student.id', $who_memorized);
        }

        return $query->get();
    }

    public function getStudentById($id)
    {
        return Student::with('courses')->findOrFail($id);
    }

    /**
     * تحديث بيانات الطالب
     */
    public function updateStudent($id, array $data)
    {
        $student = $this->getStudentById($id);

        $student->update([
            'full_name'       => $data['full_name'],
            'id_number'       => $data['id_number'],
            'date_of_birth'   => $data['date_of_birth'],
            'phone_number'    => $data['phone_number'],
            'address'         => $data['address'],
            'is_displaced'    => $data['is_displaced'],
            'birth_place'     => $data['birth_place'] ?? $student->birth_place,
            'center_name'     => $data['center_name'] ?? $student->center_name,
            'mosque_name'     => $data['mosque_name'] ?? $student->mosque_name,
            'mosque_address'  => $data['mosque_address'] ?? $student->mosque_address,
            'whatsapp_number' => $data['whatsapp_number'] ?? $student->whatsapp_number,
            'updated_by'      => Auth::id(),
            'gender' => $data['gender'],
        ]);

        return $student;
    }
    public function getStudentsForExport()
    {
        $students = Student::withCount('courses')->get();
        return $students->map(function ($student) {
            return [
                'full_name'    => $student->full_name,
                'id_number'    => $student->id_number,
                'gender'       => ($student->gender == 'male') ? 'ذكر' : 'أنثى',
                'status'       => ($student->is_displaced) ? 'نازح' : 'مقيم',
                'courses_count' => $student->courses_count,
            ];
        })->toArray();
    }
    public function exportStudentsData()
    {
        $logic = new ExportExcel();

        $data = $this->getStudentsForExport();

        $headers = [
            'اسم الطالب/ة',
            'رقم الهوية',
            'الجنس',
            'الحالة',
            'عدد الدورات'
        ];

        $columnsMapping = [
            'full_name',
            'id_number',
            'gender',
            'status',
            'courses_count'
        ];

        $fileName = "تقرير_الطلاب_" . date('Y-m-d');
        $reportTitle = "قائمة الطلاب المسجلين بتاريخ: " . date('Y-m-d');

        return $logic->export($fileName, $reportTitle, $headers, $data, $columnsMapping);
    }
    public function deleteStudent($id)
    {
        $student = $this->getStudentById($id);

        $student->update([
            'deleted_by' => Auth::user()->id,
            'deleted_at' => now(),
        ]);

        return $student->delete();
    }
}
