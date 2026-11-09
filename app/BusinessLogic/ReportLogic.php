<?php

namespace App\BusinessLogic;

use App\Models\RecitationReport;
use App\Models\Student;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportLogic
{
    /**
     * بناء استعلام تقرير التسميع المفلتر
     */
    public function getRecitationQuery($filters)
    {
        $query = RecitationReport::query();

        // 1. الفلترة حسب المركز المختار في الـ Session أو في الفلتر
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : ($filters['center_id'] ?? null);

        if ($selectedCenterId) {
            $query->whereIn('teacher_id', function ($q) use ($selectedCenterId) {
                $q->select('id')
                    ->from('user')
                    ->where('center_id', $selectedCenterId);
            });
        }

        // 2. الفلترة حسب التاريخ
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('recitation_date', [$filters['date_from'], $filters['date_to']]);
        }

        // 3. الفلترة حسب الطالب
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        // 4. الفلترة حسب المجموعة
        if (!empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        // 5. الفلترة حسب الجنس (فلتر الصفحة له الأولوية، وإلا فاختيار الجلسة طلاب/طالبات)
        $gender = !empty($filters['gender']) ? $filters['gender'] : session('selected_gender');
        if (!empty($gender)) {
            $query->whereIn('student_id', function ($q) use ($gender) {
                $q->select('id')
                    ->from('student')
                    ->where('gender', $gender);
            });
        }

        if (Auth::user()->is_admin) {
            if (!empty($filters['teacher_id'])) {
                $query->where('teacher_id', $filters['teacher_id']);
            }
        } else {
            $query->where('teacher_id', Auth::id());
        }

        if (isset($filters['order']) && is_array($filters['order']) && count($filters['order']) > 0) {
            $columnIndex = $filters['order'][0]['column'] ?? null;
            $columnName = isset($columnIndex, $filters['columns'][$columnIndex]['data'])
                ? $filters['columns'][$columnIndex]['data']
                : 'recitation_date';

            $columnSortOrder = $filters['order'][0]['dir'] ?? 'desc';

            $sortableColumns = ['recitation_date', 'student_name', 'student_id_number', 'group_name', 'teacher_name', 'sura_name'];

            if (in_array($columnName, $sortableColumns)) {
                $query->orderBy($columnName, $columnSortOrder);
            } else {
                $query->orderBy('recitation_date', 'desc');
            }
        } else {
            $query->orderBy('recitation_date', 'desc');
        }

        return $query;
    }

    /**
     * جلب القوائم الأولية لفلترة التقرير
     */
    public function getFilterLists()
    {
        $user = Auth::user();
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;
        $selectedGender = session('selected_gender');

        if ($user->is_admin) {
            $teachersQuery = User::where('is_admin', 0)->whereNull('deleted_at');
            $groupsQuery = Group::query();
            $studentsQuery = Student::query();

            if ($selectedCenterId) {
                $teachersQuery->where('center_id', $selectedCenterId);

                $groupsQuery->whereHas('teacher', function ($q) use ($selectedCenterId) {
                    $q->where('center_id', $selectedCenterId);
                });

                $studentsQuery->whereHas('groups.teacher', function ($q) use ($selectedCenterId) {
                    $q->where('center_id', $selectedCenterId);
                });
            }

            if ($selectedGender) {
                $teachersQuery->where('gender', $selectedGender);

                $groupsQuery->whereHas('teacher', function ($q) use ($selectedGender) {
                    $q->where('gender', $selectedGender);
                });

                $studentsQuery->where('gender', $selectedGender);
            }

            return [
                'students' => $studentsQuery->orderBy('full_name')->get(),
                'groups'   => $groupsQuery->orderBy('GroupName')->get(),
                'teachers' => $teachersQuery->get(),
                'centers'  => DB::table('centers')->select('id', 'name')->get(),
            ];
        }

        $groupsQuery = Group::where('UserId', $user->id);
        $studentsQuery = Student::whereHas('groups', function ($q) use ($user) {
            $q->where('UserId', $user->id);
        });

        if ($selectedGender) {
            $groupsQuery->whereHas('teacher', function ($q) use ($selectedGender) {
                $q->where('gender', $selectedGender);
            });
            $studentsQuery->where('gender', $selectedGender);
        }

        return [
            'groups'   => $groupsQuery->get(),
            'students' => $studentsQuery->orderBy('full_name')->get(),
            'teachers' => collect(),
            'centers'  => collect(),
        ];
    }
}
