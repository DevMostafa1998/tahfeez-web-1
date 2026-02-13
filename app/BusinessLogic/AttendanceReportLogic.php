<?php

namespace App\BusinessLogic;

use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AttendanceReportLogic
{
    /**
     * جلب البيانات الأولية لصفحة الفلاتر بناءً على صلاحيات المستخدم
     */
    public function getInitialData($user)
    {
        if ($user->is_admin) {
            return [
                'groups'   => Group::all(),
                'students' => Student::all(),
                'teachers' => User::where('is_admin', 0)->get(),
            ];
        }

        return [
            'groups'   => Group::where('UserId', $user->id)->get(),
            'students' => Student::whereHas('groups', function ($q) use ($user) {
                $q->where('UserId', $user->id);
            })->get(),
            'teachers' => User::where('id', $user->id)->get(),
        ];
    }

    /**
     * معالجة طلب بيانات الحضور مع الفلترة
     */
    public function getFilteredAttendance($user, array $filters)
    {
        $query = StudentAttendance::with('student');

        $query->whereBetween('attendance_date', [$filters['date_from'], $filters['date_to']]);

        if (!$user->is_admin) {
            $query->whereHas('student.groups', function ($q) use ($user) {
                $q->where('UserId', $user->id);
            });
        } elseif (!empty($filters['teacher_id'])) {
            $query->whereHas('student.groups', function ($q) use ($filters) {
                $q->where('UserId', $filters['teacher_id']);
            });
        }

        if (!empty($filters['group_id'])) {
            $query->whereHas('student.groups', function ($q) use ($filters) {
                $q->where('group_id', $filters['group_id']);
            });
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get()->map(function ($record) {
            return [
                'attendance_date'   => $record->attendance_date,
                'student_name'      => $record->student->full_name ?? '-',
                'student_id_number' => $record->student->id_number ?? '-',
                'student_phone'     => $record->student->phone_number ?? '-',
                'status'            => $record->status,
                'notes'             => $record->notes,
            ];
        });
    }

    /**
     * منطق تحديث الفلاتر الديناميكي (AJAX Filters)
     */
    public function getDynamicFilters($requestData)
    {
        $groupId   = $requestData['group_id'] ?? null;
        $studentId = $requestData['student_id'] ?? null;
        $teacherId = $requestData['teacher_id'] ?? null;
        $changed   = $requestData['changed_element'] ?? null;

        $studentsQuery = Student::query();
        $groupsQuery   = Group::query();
        $teachersQuery = User::where('is_admin', 0);

        if ($groupId) {
            $group = Group::find($groupId);
            if ($group) {
                $teachersQuery->where('id', $group->UserId);
                $studentsQuery->whereHas('groups', function ($q) use ($groupId) {
                    $q->where('group_id', $groupId);
                });
            }
        }

        if ($studentId) {
            $teachersQuery->whereHas('groups.students', function ($q) use ($studentId) {
                $q->where('students.id', $studentId);
            });
            $groupsQuery->whereHas('students', function ($q) use ($studentId) {
                $q->where('students.id', $studentId);
            });
        }

        if ($teacherId && !$groupId && !$studentId) {
            $groupsQuery->where('UserId', $teacherId);
            $studentsQuery->whereHas('groups', function ($q) use ($teacherId) {
                $q->where('UserId', $teacherId);
            });
        }

        $selectedTeacherId = $teacherId;
        if ($changed === 'group_id' && $groupId) {
            $selectedTeacherId = Group::find($groupId)->UserId ?? null;
        }

        return [
            'students' => $studentsQuery->get(['id', 'full_name']),
            'groups'   => $groupsQuery->get(['id', 'GroupName']),
            'teachers' => $teachersQuery->get(['id', 'full_name']),
            'selected_teacher_id' => $selectedTeacherId
        ];
    }

    public function getDataTableAttendance($user, array $requestData)
    {
        $query = StudentAttendance::with('student');

        $this->applyFilters($query, $user, $requestData);

        if (isset($requestData['order']) && count($requestData['order'])) {
            $columnIndex = $requestData['order'][0]['column'];
            $columnDir = $requestData['order'][0]['dir'];
            $columnName = $requestData['columns'][$columnIndex]['name'];

            if ($columnName == 'student_name') {
                $query->join('students', 'student_attendances.student_id', '=', 'students.id')
                    ->orderBy('students.full_name', $columnDir)
                    ->select('student_attendances.*');
            } else {
                $query->orderBy($columnName, $columnDir);
            }
        } else {
            $query->orderBy('attendance_date', 'desc');
        }

        $totalRecords = StudentAttendance::count();
        $filteredRecords = $query->count();

        //  الترقيم (Pagination)
        $start = $requestData['start'] ?? 0;
        $length = $requestData['length'] ?? 10;
        $records = $query->skip($start)->take($length)->get();

        $data = $records->map(function ($record) {
            return [
                'attendance_date'   => $record->attendance_date,
                'student_name'      => $record->student->full_name ?? '-',
                'student_id_number' => $record->student->id_number ?? '-',
                'student_phone'     => $record->student->phone_number ?? '-',
                'status'            => $record->status,
            ];
        });

        return [
            "draw"            => intval($requestData['draw'] ?? 0),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data"            => $data
        ];
    }

    private function applyFilters($query, $user, $filters)
    {
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('attendance_date', [$filters['date_from'], $filters['date_to']]);
        }

        if (!$user->is_admin) {
            $query->whereHas('student.groups', fn($q) => $q->where('UserId', $user->id));
        } elseif (!empty($filters['teacher_id'])) {
            $query->whereHas('student.groups', fn($q) => $q->where('UserId', $filters['teacher_id']));
        }

        if (!empty($filters['group_id'])) {
            $query->whereHas('student.groups', fn($q) => $q->where('group_id', $filters['group_id']));
        }
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['search']['value'])) {
            $searchValue = $filters['search']['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->whereHas('student', function ($st) use ($searchValue) {
                    $st->where('full_name', 'LIKE', "%{$searchValue}%")
                        ->orWhere('id_number', 'LIKE', "%{$searchValue}%")
                        ->orWhere('phone_number', 'LIKE', "%{$searchValue}%");
                });
            });
        }
    }
    // ميثود جلب البيانات كاملة للتصدير
    public function getAllAttendanceForExport($user, array $filters)
    {
        $query = \App\Models\StudentAttendance::with('student');
        $this->applyFilters($query, $user, $filters);

        return $query->get()->map(function ($record) {
            return [
                'date'   => $record->attendance_date,
                'name'   => $record->student->full_name ?? '-',
                'id'     => $record->student->id_number ?? '-',
                'phone'  => $record->student->phone_number ?? '-',
                'status' => $record->status,
            ];
        })->toArray();
    }
}
