<?php

namespace App\BusinessLogic;

use App\Models\TeacherCourseReport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TeacherReportLogic
{
    public function getTeacherCoursesReport($filters)
    {
        //  جلب معرف المركز المحدد من الـ Session
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;

        // فلتر الجنس: فلتر الصفحة له الأولوية، وإلا فاختيار الجلسة طلاب/طالبات
        $gender = !empty($filters['gender']) ? $filters['gender'] : session('selected_gender');

        $query = TeacherCourseReport::query();

        // 1. فلترة المركز إن وجد (ربط المعلم برقم المركز في جدول user)
        if ($selectedCenterId) {
            $query->whereIn('id', function ($q) use ($selectedCenterId) {
                $q->select('id')
                  ->from('user')
                  ->where('center_id', $selectedCenterId);
            });
        }

        // 2. الفلترة حسب الجنس
        if (!empty($gender)) {
            $query->whereIn('id', function ($q) use ($gender) {
                $q->select('id')
                    ->from('user')
                    ->where('gender', $gender);
            });
        }

        // 3. الفلترة الأساسية (الأدمن والمحفظ)
        if (!Auth::user()->is_admin) {
            $query->where('id', Auth::id());
        } else {
            if (!empty($filters['teacher_id'])) {
                $query->where('id', $filters['teacher_id']);
            }
        }

        // 3. معالجة البحث الخاص بـ DataTable 
        if (!empty($filters['search']['value'])) {
            $search = $filters['search']['value'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('id_number', 'LIKE', "%{$search}%");
            });
        }

        // 4. حساب الإحصائيات بأمان
        $totalDataQuery = TeacherCourseReport::query();
        if ($selectedCenterId) {
            $totalDataQuery->whereIn('id', function ($q) use ($selectedCenterId) {
                $q->select('id')->from('user')->where('center_id', $selectedCenterId);
            });
        }
        $totalData = $totalDataQuery->count();
        $totalFiltered = $query->count();

        // 5. الترتيب الديناميكي للـ DataTable
        if (isset($filters['order']) && count($filters['order'])) {
            $columnIndex = $filters['order'][0]['column'];
            $columnName = $filters['columns'][$columnIndex]['data'];
            $columnSortOrder = $filters['order'][0]['dir'];

            $query->orderBy($columnName, $columnSortOrder);
        } else {
            $query->orderBy('full_name', 'asc');
        }

        // 6. التقسيم (Pagination)
        $limit = $filters['length'] ?? 10;
        $start = $filters['start'] ?? 0;

        $data = $query->offset($start)
            ->limit($limit)
            ->get();

        return [
            "draw"            => intval($filters['draw'] ?? 1),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ];
    }

   public function exportToExcel($filters)
{
    $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
        ? session('selected_center_id')
        : null;

    $gender = !empty($filters['gender']) ? $filters['gender'] : session('selected_gender');

    $query = TeacherCourseReport::query();

    // فلترة حسب المركز المختار
    if ($selectedCenterId) {
        $query->whereIn('id', function ($q) use ($selectedCenterId) {
            $q->select('id')
              ->from('user')
              ->where('center_id', $selectedCenterId);
        });
    }

    // فلترة حسب الجنس
    if (!empty($gender)) {
        $query->whereIn('id', function ($q) use ($gender) {
            $q->select('id')
                ->from('user')
                ->where('gender', $gender);
        });
    }

    if (!Auth::user()->is_admin) {
        $query->where('id', Auth::id());
    } else {
        if (!empty($filters['teacher_id'])) {
            $query->where('id', $filters['teacher_id']);
        }
    }

    if (!empty($filters['search_value'])) {
        $search = $filters['search_value'];
        $query->where(function ($q) use ($search) {
            $q->where('full_name', 'LIKE', "%{$search}%")
                ->orWhere('id_number', 'LIKE', "%{$search}%");
        });
    }

    $data = $query->orderBy('full_name', 'asc')->get()->toArray();

    $exporter = new ExportExcel();

    $headers = [
        'اسم المحفظ/ة',
        'رقم الهوية',
        'تاريخ الميلاد',
        'مكان الميلاد',
        'رقم الجوال',
        'رقم المحفظة',
        'رقم الواتساب',
        'المؤهل',
        'التخصص',
        'المحفوظ',
        'المسجد',
        'العنوان',
        'اسم الدورة'
    ];

    $mapping = [
        'full_name',
        'id_number',
        'date_of_birth',
        'birth_place',
        'phone_number',
        'wallet_number',
        'whatsapp_number',
        'qualification',
        'specialization',
        'parts_memorized',
        'mosque_name',
        'address',
        'course_name'
    ];

    return $exporter->export(
        'تقرير_المحفظين_' . date('Y-m-d'),
        'تقرير بيانات المحفظين والدورات الشامل',
        $headers,
        $data,
        $mapping
    );
}

    public function getFilterLists()
    {
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;
        $selectedGender = session('selected_gender');

        if (Auth::user()->is_admin) {
            $teachersQuery = User::where('is_admin', 0)->whereNull('deleted_at');

            //  تصفية قائمة المحفظين في الفلتر بحسب المركز المختار
            if ($selectedCenterId) {
                $teachersQuery->where('center_id', $selectedCenterId);
            }

            if ($selectedGender) {
                $teachersQuery->where('gender', $selectedGender);
            }

            return [
                'teachers' => $teachersQuery->orderBy('full_name')->get(),
            ];
        }

        return [
            'teachers' => collect(),
        ];
    }
}
