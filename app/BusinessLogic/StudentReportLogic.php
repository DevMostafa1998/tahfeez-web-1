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
            return [
                'full_name'       => $student->full_name,
                'id_number'       => $student->id_number,
                'date_of_birth'   => $student->date_of_birth,
                'birth_place'     => $student->birth_place,
                'phone_number'    => $student->phone_number,
                'whatsapp_number' => $student->whatsapp_number,
                'address'         => $student->address,
                'center_name'     => $student->center_name,
                'mosque_name'     => $student->mosque_name,
                'is_displaced'    => (bool)$student->is_displaced,
                'groups'          => $student->groups->map(function ($g) {
                    return ['GroupName' => $g->GroupName];
                }),
                'teacher_name'    => $student->groups->first() && $student->groups->first()->teacher
                    ? $student->groups->first()->teacher->full_name
                    : 'غير محدد'
            ];
        });
    }
}
