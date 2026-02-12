<?php

namespace App\BusinessLogic;

use App\Models\Student;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

class StudentReportLogic
{
    /**
     * جلب قائمة المعلمين (المحفظين)
     */
    public function getTeachers()
    {
        return DB::table('user')
            ->select('id', 'full_name')
            ->where('is_admin', 0)
            ->whereNull('deleted_at')
            ->get();
    }

    /**
     * جلب المجموعات بناءً على صلاحيات المستخدم
     */
    public function getGroupsForUser($user)
    {
        return ($user->is_admin == 1)
            ? Group::all()
            : Group::where('UserId', $user->id)->get();
    }
    public function buildFilteredQuery($user, $filters)
    {
        $query = Student::with(['groups.teacher']);

        if ($user->is_admin != 1) {
            $query->whereHas('groups', function ($q) use ($user) {
                $q->where('UserId', $user->id);
            });
        }

        if ($user->is_admin == 1 && !empty($filters['UserId'])) {
            $query->whereHas('groups', function ($q) use ($filters) {
                $q->where('UserId', $filters['UserId']);
            });
        }

        if (!empty($filters['group_id'])) {
            $query->whereHas('groups', function ($q) use ($filters) {
                $q->where('group_id', $filters['group_id']);
            });
        }
        if (isset($filters['order']) && !empty($filters['order'])) {
            $columnIndex = $filters['order'][0]['column'];
            $columnName = $filters['columns'][$columnIndex]['data'];
            $columnDir = $filters['order'][0]['dir'];

            $sortableColumns = ['full_name', 'id_number', 'date_of_birth', 'birth_place', 'phone_number', 'address', 'center_name', 'mosque_name', 'is_displaced'];

            if (in_array($columnName, $sortableColumns)) {
                $query->orderBy($columnName, $columnDir);
            }
        } else {
            $query->orderBy('created_at', 'desc'); // ترتيب افتراضي
        }
        // البحث السريع الخاص بـ DataTables
        if (!empty($filters['search']['value'])) {
            $search = $filters['search']['value'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('id_number', 'LIKE', "%{$search}%")
                    ->orWhere('phone_number', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }
    /**
     * الفلترة وجلب الطلاب بناءً على المعايير المرسلة
     */
    public function getFilteredStudents($user, $filters)
    {
        $query = Student::with(['groups.teacher']);

        if ($user->is_admin != 1) {
            $query->whereHas('groups', function ($q) use ($user) {
                $q->where('UserId', $user->id);
            });
        }

        if ($user->is_admin == 1 && !empty($filters['UserId'])) {
            $query->whereHas('groups', function ($q) use ($filters) {
                $q->where('UserId', $filters['UserId']);
            });
        }

        if (!empty($filters['group_id'])) {
            $query->whereHas('groups', function ($q) use ($filters) {
                $q->where('group_id', $filters['group_id']);
            });
        }

        return $query->get();
    }

    /**
     * تنسيق بيانات الطالب للإخراج (JSON)
     */

    public function formatStudentData($students)
    {
        return $students->map(function ($student) {
            $groupsHtml = '';
            if ($student->groups) {
                foreach ($student->groups as $group) {
                    $groupsHtml .= '<span class="badge-custom">' . $group->GroupName . '</span> ';
                }
            }

            // 2. تحويل حالة النزوح إلى نص ملون
            $statusHtml = $student->is_displaced
                ? '<span class="status-badge bg-info">نازح</span>'
                : '<span class="status-badge bg-success">مقيم</span>';

            return [
                'full_name'       => $student->full_name,
                'id_number'       => $student->id_number,
                'date_of_birth'   => $student->date_of_birth ? substr($student->date_of_birth, 0, 10) : '-',
                'birth_place'     => $student->birth_place,
                'phone_number'    => $student->phone_number,
                'whatsapp_number' => $student->whatsapp_number,
                'address'         => $student->address,
                'center_name'     => $student->center_name,
                'mosque_name'     => $student->mosque_name,
                'groups'          => $groupsHtml,
                'teacher_name'    => $student->groups->first() && $student->groups->first()->teacher
                    ? $student->groups->first()->teacher->full_name
                    : 'غير محدد',
                'is_displaced'    => $statusHtml,
            ];
        });
    }
}
