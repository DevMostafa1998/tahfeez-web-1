<?php

namespace App\BusinessLogic;

use App\Models\TeacherCourseReport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TeacherReportLogic
{
    public function getTeacherCoursesReport($filters)
    {
        $query = TeacherCourseReport::query();

        //  الفلترة الأساسية (الأدمن والمحفظ)
        if (!Auth::user()->is_admin) {
            $query->where('id', Auth::id());
        } elseif (!empty($filters['teacher_id'])) {
            $query->where('id', $filters['teacher_id']);
        }

        //  معالجة البحث الخاص بـ DataTable (Global Search)
        if (!empty($filters['search']['value'])) {
            $search = $filters['search']['value'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('id_number', 'LIKE', "%{$search}%");
            });
        }

        $totalData = TeacherCourseReport::count();
        $totalFiltered = $query->count();
        if (isset($filters['order']) && count($filters['order'])) {
            $columnIndex = $filters['order'][0]['column'];
            $columnName = $filters['columns'][$columnIndex]['data'];
            $columnSortOrder = $filters['order'][0]['dir'];

            $query->orderBy($columnName, $columnSortOrder);
        } else {
            $query->orderBy('full_name', 'asc');
        }
        //  الترتيب والتقسيم (Pagination)
        $limit = $filters['length'] ?? 10;
        $start = $filters['start'] ?? 0;

        $data = $query->orderBy('full_name', 'asc')
            ->offset($start)
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
        $query = TeacherCourseReport::query();

        if (!Auth::user()->is_admin) {
            $query->where('id', Auth::id());
        } elseif (!empty($filters['teacher_id'])) {
            $query->where('id', $filters['teacher_id']);
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
        if (Auth::user()->is_admin) {
            return [
                'teachers' => User::where('is_admin', 0)->orderBy('full_name')->get(),
            ];
        }

        return [
            'teachers' => collect(),
        ];
    }
}
