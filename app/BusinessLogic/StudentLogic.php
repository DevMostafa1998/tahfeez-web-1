<?php

namespace App\BusinessLogic;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StudentLogic
{

    public function storeStudent(array $data)
    {
        $studentData = [
            'full_name'       => $data['full_name'],
            'id_number'       => $data['id_number'],
            'father_id'       => $data['father_id'] ?? null,
            'date_of_birth'   => $data['date_of_birth'],
            'phone_number'    => $data['phone_number'],
            'address'         => $data['address'],
            'is_displaced'    => $data['is_displaced'],
            'user_id'         => Auth::id(),
            'creation_by'     => Auth::user()->id,
            'birth_place'     => $data['birth_place'] ?? null,
            'center_id'     => $data['center_id'] ?? null,
            'mosque_name'     => $data['mosque_name'] ?? null,
            'mosque_address'  => $data['mosque_address'] ?? null,
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
            'gender'          => $data['gender'],
            'categorie_id'    => $data['categorie_id'],
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

        // تطبيق فلتر المركز
        if (session()->has('selected_center_id') && session('selected_center_id') != '') {
            $query->where('center_id', session('selected_center_id'));
        }

        // جلب وتطبيق فلتر الفئة (الجنس) من الـ Session
        $selectedGender = session('selected_gender');
        if ($selectedGender) {
            $query->where('gender', $selectedGender);
        }
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

            if ($columnName && !in_array($columnName, ['actions', 'courses', 'category'])) {
                $query->orderBy($columnName, $columnDirection);
            }
        } else {
            $query->orderBy('full_name', 'asc');
        }

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $start = $request->filled('start') ? intval($request->input('start')) : 0;
        $length = $request->filled('length') ? intval($request->input('length')) : 10;

        $students = $query->offset($start)
            ->limit($length)
            ->get();

        // جلب التصنيفات كـ Array سريع لتجنب استعلامات N+1 داخل الـ Loop
        $categories = DB::table('categorie')->pluck('name', 'id')->toArray();

        $data = $students->map(function ($student) use ($showArchived, $categories) {
            return $this->formatStudentForTable($student, $showArchived, $categories);
        });

        return [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ];
    }

    private function formatStudentForTable($student, $showArchived, $categories = [])
    {
        $actionButtons = $this->generateActionButtons($student, $showArchived);

        // جلب اسم التصنيف بناءً على الـ ID المخزن عند الطالب من الجدول المرفق
        $categoryName = $categories[$student->categorie_id] ?? '-';

        return [
            'full_name' => '<span class="fw-bold">' . $student->full_name . '</span>',
            'id_number' => '<span class="badge bg-light text-dark border px-4 py-2">' . $student->id_number . '</span>',
            'father_id' => '<span class="badge bg-light text-dark border px-4 py-2">' . $student->father_id . '</span>',
            'gender'    => $student->gender == 'male' ? '<span class="badge bg-blue-subtle text-primary border px-3">ذكر</span>' : '<span class="badge bg-pink-subtle text-danger border px-3">أنثى</span>',
            'category'  => '<span class="badge bg-light text-secondary border px-3">' . $categoryName . '</span>', // شارة حقل التصنيف المحدثة
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

        $query = DB::table('student')
            ->select('student.*')
            ->addSelect([
                'courses_count' => DB::table('course_student')
                    ->whereColumn('student_id', 'student.id')
                    ->selectRaw('count(*)'),
                'course_ids' => DB::table('course_student')
                    ->whereColumn('student_id', 'student.id')
                    ->selectRaw("GROUP_CONCAT(course_id)")
            ])
            ->whereNull('student.deleted_at');

        if (!$isAdmin) {
            $query->join('student_group', 'student.id', '=', 'student_group.student_id')
                ->join('group', 'student_group.group_id', '=', 'group.id')
                ->where('group.UserId', $user->id)
                ->distinct();
        }

        if ($request->filter == 'not_memorized_today') {
            $today = \Carbon\Carbon::today();
            $who_memorized = DB::table('student_daily_memorizations')
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

    public function updateStudent($id, array $data)
    {
        $student = $this->getStudentById($id);

        $student->update([
            'full_name'       => $data['full_name'],
            'id_number'       => $data['id_number'],
            'father_id'       => $data['father_id'] ?? null,
            'date_of_birth'   => $data['date_of_birth'],
            'phone_number'    => $data['phone_number'],
            'address'         => $data['address'],
            'is_displaced'    => $data['is_displaced'],
            'birth_place'     => $data['birth_place'] ?? $student->birth_place,
            'center_id'     => $data['center_id'] ?? $student->center_id,
            'mosque_name'     => $data['mosque_name'] ?? $student->mosque_name,
            'mosque_address'  => $data['mosque_address'] ?? $student->mosque_address,
            'whatsapp_number' => $data['whatsapp_number'] ?? $student->whatsapp_number,
            'updated_by'      => Auth::id(),
            'gender'          => $data['gender'],
            'categorie_id'    => $data['categorie_id'],
        ]);

        return $student;
    }

    public function getStudentsForExport()
    {
        $students = Student::withCount('courses')->get();
        return $students->map(function ($student) {
            return [
                'full_name'     => $student->full_name,
                'id_number'     => $student->id_number,
                'gender'        => ($student->gender == 'male') ? 'ذكر' : 'أنثى',
                'status'        => ($student->is_displaced) ? 'نازح' : 'مقيم',
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

    public function storeStudentPublic(array $data)
    {
        $studentData = [
            'full_name'       => $data['full_name'],
            'id_number'       => $data['id_number'],
            'date_of_birth'   => $data['date_of_birth'],
            'phone_number'    => $data['phone_number'],
            'address'         => $data['address'],
            'is_displaced'    => $data['is_displaced'],
            'user_id'         => $data['user_id'] ?? $data['teacher_id'] ?? null,
            'creation_by'     => 1,
            'birth_place'     => $data['birth_place'] ?? null,
            'center_id'     => $data['center_id'] ?? null,
            'mosque_name'     => $data['mosque_name'] ?? null,
            'mosque_address'  => $data['mosque_address'] ?? null,
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
            'gender'          => $data['gender'],
            'categorie_id'    => $data['categorie_id'],
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
}
