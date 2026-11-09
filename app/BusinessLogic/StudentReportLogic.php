<?php

namespace App\BusinessLogic;

use App\Models\Student;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StudentReportLogic
{
    /**
     * جلب قائمة المعلمين (المحفظين) مفلترة حسب المركز المختار في الـ Session
     */
    public function getTeachers()
    {
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;

        $query = User::where('is_admin', 0)->whereNull('deleted_at');

        if ($selectedCenterId) {
            $query->where('center_id', $selectedCenterId);
        }

        $selectedGender = session('selected_gender');
        if ($selectedGender) {
            $query->where('gender', $selectedGender);
        }

        return $query->select('id', 'full_name')->get();
    }

    /**
     * جلب قائمة المراكز المتاحة من قاعدة البيانات
     */
    public function getCenters()
    {
        return DB::table('centers')->select('id', 'name')->get();
    }

    /**
     * جلب المجموعات بناءً على صلاحيات المستخدم والمركز المختار في الـ Session
     */
    public function getGroupsForUser($user)
    {
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;

        $query = Group::query();

        if ($user->is_admin == 1) {
            if ($selectedCenterId) {
                $query->whereHas('teacher', function ($q) use ($selectedCenterId) {
                    $q->where('center_id', $selectedCenterId);
                });
            }
        } else {
            $query->where('UserId', $user->id);
        }

        $selectedGender = session('selected_gender');
        if ($selectedGender) {
            $query->whereHas('teacher', function ($q) use ($selectedGender) {
                $q->where('gender', $selectedGender);
            });
        }

        return $query->get();
    }

    /**
     * بناء استعلام الطلاب المفلتر
     */
    public function buildFilteredQuery($user, $filters)
    {
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;

        $query = Student::with([
            'groups.teacher.center',
            'quranMemTests' => function ($q) {
                $q->orderBy('date', 'desc')->orderBy('id', 'desc');
            }
        ]);

        // 1. الفلترة حسب المركز المختار في الـ Session
        if ($selectedCenterId) {
            $query->whereHas('groups.teacher', function ($teacherQuery) use ($selectedCenterId) {
                $teacherQuery->where('center_id', $selectedCenterId);
            });
        }

        // 2. الفلترة حسب صلاحيات المعلم أو الأدمن
        if ($user->is_admin != 1) {
            $query->whereHas('groups', function ($q) use ($user) {
                $q->where('UserId', $user->id);
            });
        } else {
            if (!empty($filters['UserId'])) {
                $query->whereHas('groups', function ($q) use ($filters) {
                    $q->where('UserId', $filters['UserId']);
                });
            }
        }

        // 3. فلترة المجموعة
        if (!empty($filters['group_id'])) {
            $query->whereHas('groups', function ($q) use ($filters) {
                // دالة whereKey مخصصة للمفاتيح الأساسية وتمنع تداخل أسماء الأعمدة تماماً
                $q->whereKey($filters['group_id']);
            });
        }

        // 4. فلترة الجنس (فلتر الصفحة له الأولوية، وإلا فاختيار الجلسة طلاب/طالبات)
        $gender = !empty($filters['gender']) ? $filters['gender'] : session('selected_gender');
        if (!empty($gender)) {
            $query->where('gender', $gender);
        }

        // 5. فلترة المركز من القائمة المنسدلة للفلتر
        if (!empty($filters['center_id'])) {
            $query->whereHas('groups.teacher', function ($q) use ($filters) {
                $q->where('center_id', $filters['center_id']);
            });
        }

        // 6. البحث الخاص بـ DataTable
        if (!empty($filters['search']['value'])) {
            $search = $filters['search']['value'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('id_number', 'LIKE', "%{$search}%")
                    ->orWhere('father_id', 'LIKE', "%{$search}%")
                    ->orWhere('phone_number', 'LIKE', "%{$search}%");
            });
        }

        // 7. الترتيب الديناميكي
        if (isset($filters['order']) && !empty($filters['order'])) {
            $columnIndex = $filters['order'][0]['column'];
            $columnName = $filters['columns'][$columnIndex]['data'] ?? 'created_at';
            $columnDir = $filters['order'][0]['dir'] ?? 'desc';

            $sortableColumns = ['full_name', 'id_number', 'father_id', 'gender', 'date_of_birth', 'birth_place', 'phone_number', 'address', 'mosque_name', 'is_displaced'];

            if (in_array($columnName, $sortableColumns)) {
                $query->orderBy($columnName, $columnDir);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    /**
     * تنسيق البيانات المخصصة للـ DataTable
     */
    public function formatStudentData($students)
    {
        $categories = DB::table('categorie')->pluck('name', 'id')->toArray();

        return $students->map(function ($student) use ($categories) {
            $groupsHtml = '';
            if ($student->groups) {
                foreach ($student->groups as $group) {
                    $groupsHtml .= '<span class="badge-custom">' . htmlspecialchars($group->GroupName) . '</span> ';
                }
            }

            $statusHtml = $student->is_displaced
                ? '<span class="status-badge bg-info">نازح</span>'
                : '<span class="status-badge bg-success">مقيم</span>';

            $genderHtml = $student->gender == 'male'
                ? '<span class="badge bg-blue-subtle text-primary border px-3">ذكر</span>'
                : '<span class="badge bg-pink-subtle text-danger border px-3">أنثى</span>';

            $categoryName = $categories[$student->categorie_id] ?? '-';
            $categoryHtml = '<span class="badge bg-light text-secondary border px-3">' . htmlspecialchars($categoryName) . '</span>';

            $latestTest = null;
            if ($student->relationLoaded('quranMemTests') && $student->quranMemTests->isNotEmpty()) {
                $latestTest = $student->quranMemTests->first();
            }

            $resultHtml = '-';
            if ($latestTest) {
                $resultHtml = $latestTest->result_status == 'ناجح'
                    ? '<span class="badge bg-success text-white px-2 py-1">ناجح</span>'
                    : '<span class="badge bg-danger text-white px-2 py-1">راسب</span>';
            }

            // استخراج اسم المركز
            $firstGroup = $student->groups ? $student->groups->first() : null;
            $teacher = $firstGroup ? $firstGroup->teacher : null;
            $centerName = ($teacher && $teacher->center) ? $teacher->center->name : '-';

            return [
                'full_name'        => $student->full_name,
                'id_number'        => '<span class="badge bg-light text-dark border px-4 py-2">' . $student->id_number . '</span>',
                'father_id'        => '<span class="badge bg-light text-dark border px-4 py-2">' . ($student->father_id ?? '-') . '</span>',
                'gender'           => $genderHtml,
                'category'         => $categoryHtml,
                'date_of_birth'    => $student->date_of_birth ? substr($student->date_of_birth, 0, 10) : '-',
                'birth_place'      => $student->birth_place,
                'phone_number'     => $student->phone_number,
                'whatsapp_number'  => $student->whatsapp_number,
                'address'          => $student->address,
                'center_name'      => $centerName,
                'mosque_name'      => $student->mosque_name,
                'groups'           => $groupsHtml,
                'teacher_name'     => $teacher ? $teacher->full_name : 'غير محدد',
                'is_displaced'     => $statusHtml,

                'exam_type'        => $latestTest ? '<span class="badge bg-light text-dark border">' . htmlspecialchars($latestTest->examType) . '</span>' : '-',
                'exam_date'        => ($latestTest && $latestTest->date) ? \Carbon\Carbon::parse($latestTest->date)->format('Y-m-d') : '-',
                'score_percentage' => $latestTest ? '<span class="text-success fw-bold">' . $latestTest->score_percentage . '%</span>' : '-',
                'result_status'    => $resultHtml,
                'testing_sheikh'   => $latestTest ? $latestTest->teacher_name : '-',
            ];
        });
    }

    /**
     * تنسيق البيانات للتصدير في Excel
     */
    public function formatForExcel($students)
    {
        $categories = DB::table('categorie')->pluck('name', 'id')->toArray();

        return $students->map(function ($student) use ($categories) {
            $categoryName = $categories[$student->categorie_id] ?? '-';

            $latestTest = null;
            if ($student->relationLoaded('quranMemTests') && $student->quranMemTests->isNotEmpty()) {
                $latestTest = $student->quranMemTests->first();
            }

            $firstGroup = $student->groups ? $student->groups->first() : null;
            $teacher = $firstGroup ? $firstGroup->teacher : null;
            $centerName = ($teacher && $teacher->center) ? $teacher->center->name : '-';

            return [
                'full_name'        => $student->full_name,
                'id_number'        => $student->id_number,
                'father_id'        => $student->father_id ?? '-',
                'gender'           => $student->gender == 'male' ? 'ذكر' : 'أنثى',
                'category'         => $categoryName,
                'date_of_birth'    => $student->date_of_birth ? substr($student->date_of_birth, 0, 10) : '-',
                'birth_place'      => $student->birth_place,
                'phone_number'     => $student->phone_number,
                'whatsapp_number'  => $student->whatsapp_number,
                'address'          => $student->address,
                'center_name'      => $centerName,
                'mosque_name'      => $student->mosque_name,
                'group_name'       => $student->groups ? $student->groups->pluck('GroupName')->implode(' - ') : '-',
                'teacher_name'     => $teacher ? $teacher->full_name : 'غير محدد',
                'is_displaced'     => $student->is_displaced ? 'نازح' : 'مقيم',

                'exam_type'        => $latestTest ? $latestTest->examType : '-',
                'exam_date'        => ($latestTest && $latestTest->date) ? \Carbon\Carbon::parse($latestTest->date)->format('Y-m-d') : '-',
                'score_percentage' => $latestTest ? $latestTest->score_percentage . '%' : '-',
                'result_status'    => $latestTest ? $latestTest->result_status : '-',
                'testing_sheikh'   => $latestTest ? $latestTest->teacher_name : '-',
            ];
        })->toArray();
    }
}
