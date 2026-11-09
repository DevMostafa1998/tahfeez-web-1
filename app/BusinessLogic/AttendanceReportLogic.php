<?php

namespace App\BusinessLogic;

use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AttendanceReportLogic
{
    /**
     * جلب البيانات الأولية لصفحة الفلاتر عند تحميل الصفحة
     */
    public function getInitialData($user)
    {
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;
        $selectedGender = session('selected_gender');

        if ($user->is_admin) {
            $teachersQuery = User::where('is_admin', 0);
            $groupsQuery   = Group::query();
            $studentsQuery = Student::query();

            if ($selectedCenterId) {
                $teachersQuery->where('center_id', $selectedCenterId);

                $groupsQuery->whereHas('teacher', function ($q) use ($selectedCenterId) {
                    $q->where('center_id', $selectedCenterId);
                });

                $studentsQuery->where(function ($q) use ($selectedCenterId) {
                    $q->where('center_id', $selectedCenterId)
                      ->orWhereHas('groups.teacher', function ($gq) use ($selectedCenterId) {
                          $gq->where('center_id', $selectedCenterId);
                      });
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
                'groups'   => $groupsQuery->orderBy('GroupName')->get(),
                'students' => $studentsQuery->orderBy('full_name')->get(),
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

    /**
     * جلب وقراءة بيانات جدول DataTables مع الترقيم والترتيب
     */
    public function getDataTableAttendance($user, array $requestData)
    {
        $query = StudentAttendance::with(['student']);

        // 1. تطبيق الفلاتر والبحث
        $this->applyFilters($query, $user, $requestData);

        // 2. حساب أعداد السجلات
        $totalRecords = StudentAttendance::count();
        $filteredRecords = (clone $query)->count();

        // 3. الترتيب
        if (isset($requestData['order']) && is_array($requestData['order']) && count($requestData['order'])) {
            $columnIndex = $requestData['order'][0]['column'] ?? null;
            $columnDir   = $requestData['order'][0]['dir'] ?? 'desc';
            $columnName  = $requestData['columns'][$columnIndex]['data'] ?? 'attendance_date';

            if ($columnName === 'student_name') {
                $studentIds = Student::orderBy('full_name', $columnDir)->pluck('id')->toArray();
                if (!empty($studentIds)) {
                    $query->orderByRaw('FIELD(student_id, ' . implode(',', $studentIds) . ')');
                }
            } elseif (in_array($columnName, ['attendance_date', 'status'])) {
                $query->orderBy($columnName, $columnDir);
            } else {
                $query->orderBy('attendance_date', 'desc');
            }
        } else {
            $query->orderBy('attendance_date', 'desc');
        }

        // 4. الترقيم
        $start  = $requestData['start'] ?? 0;
        $length = $requestData['length'] ?? 10;
        $records = $query->skip($start)->take($length)->get();

        $data = $records->map(function ($record) {
            return [
                'attendance_date'   => $record->attendance_date,
                'student_name'      => $record->student->full_name ?? '-',
                'student_id_number' => $record->student->id_number ?? '-',
                'student_phone'     => $record->student->phone_number ?? '-',
                'status'            => $record->status,
                'notes'             => $record->notes ?? '',
            ];
        });

        return [
            "draw"            => intval($requestData['draw'] ?? 0),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data"            => $data
        ];
    }

    /**
     */
    private function applyFilters($query, $user, $filters)
    {
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : ($filters['center_id'] ?? null);

        if (!empty($selectedCenterId)) {
            $query->where(function ($q) use ($selectedCenterId) {
                $q->whereHas('student', function ($sq) use ($selectedCenterId) {
                    $sq->where('center_id', $selectedCenterId);
                })
                ->orWhereHas('student.groups.teacher', function ($t) use ($selectedCenterId) {
                    $t->where('center_id', $selectedCenterId);
                })
                ->orWhereHas('recorder', function ($r) use ($selectedCenterId) {
                    $r->where('center_id', $selectedCenterId);
                });
            });
        }

        // فلترة بالتواريخ
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('attendance_date', [$filters['date_from'], $filters['date_to']]);
        }

        // فلترة بالمعلم
        if (!$user->is_admin) {
            $query->whereHas('student.groups', fn($q) => $q->where('UserId', $user->id));
        } elseif (!empty($filters['teacher_id'])) {
            $query->whereHas('student.groups', fn($q) => $q->where('UserId', $filters['teacher_id']));
        }

        // فلترة بالمجموعة
        if (!empty($filters['group_id'])) {
            $query->whereHas('student.groups', fn($g) => $g->where('groups.id', $filters['group_id']));
        }

        // فلترة بالطالب
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        // فلترة بالحالة
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // فلترة بالجنس (فلتر الصفحة له الأولوية، وإلا فاختيار الجلسة طلاب/طالبات)
        $gender = !empty($filters['gender']) ? $filters['gender'] : session('selected_gender');
        if (!empty($gender)) {
            $query->whereHas('student', fn($st) => $st->where('gender', $gender));
        }

        // البحث بالنص
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

    /**
     * تحديث الفلاتر الديناميكية (Dynamic Ajax)
     */
    public function getDynamicFilters($requestData)
    {
        $groupId   = $requestData['group_id'] ?? null;
        $studentId = $requestData['student_id'] ?? null;
        $teacherId = $requestData['teacher_id'] ?? null;
        $gender    = !empty($requestData['gender']) ? $requestData['gender'] : session('selected_gender');
        $changed   = $requestData['changed_element'] ?? null;

        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : ($requestData['center_id'] ?? null);

        $teachers = User::query()
            ->where('is_admin', false)
            ->when($selectedCenterId, fn($q) => $q->where('center_id', $selectedCenterId))
            ->when($gender, fn($q) => $q->where('gender', $gender))
            ->when($groupId, fn($q) => $q->whereHas('groups', fn($sq) => $sq->where('groups.id', $groupId)))
            ->when($studentId, fn($q) => $q->whereHas('groups.students', fn($ssq) => $ssq->where('students.id', $studentId)))
            ->get(['id', 'full_name']);

        $groups = Group::query()
            ->when($selectedCenterId, fn($q) => $q->whereHas('teacher', fn($sq) => $sq->where('center_id', $selectedCenterId)))
            ->when($gender, fn($q) => $q->whereHas('teacher', fn($sq) => $sq->where('gender', $gender)))
            ->when($teacherId, fn($q) => $q->where('UserId', $teacherId))
            ->when($studentId, fn($q) => $q->whereHas('students', fn($sq) => $sq->where('students.id', $studentId)))
            ->get(['id', 'GroupName']);

        $students = Student::query()
            ->when($selectedCenterId, function($q) use ($selectedCenterId) {
                $q->where('center_id', $selectedCenterId)
                  ->orWhereHas('groups.teacher', fn($sq) => $sq->where('center_id', $selectedCenterId));
            })
            ->when($teacherId, fn($q) => $q->whereHas('groups', fn($sq) => $sq->where('UserId', $teacherId)))
            ->when($groupId, fn($q) => $q->whereHas('groups', fn($sq) => $sq->where('groups.id', $groupId)))
            ->when($gender, fn($q) => $q->where('gender', $gender))
            ->get(['id', 'full_name']);

        $selectedTeacherId = $teacherId;
        if ($changed === 'group_id' && $groupId) {
            $selectedTeacherId = Group::find($groupId)->UserId ?? null;
        }

        return [
            'students'            => $students,
            'groups'              => $groups,
            'teachers'            => $teachers,
            'selected_teacher_id' => $selectedTeacherId
        ];
    }

    /**
     * تصدير البيانات إلى إكسل
     */
    public function getAllAttendanceForExport($user, array $filters)
    {
        $query = StudentAttendance::with(['student']);
        $this->applyFilters($query, $user, $filters);

        return $query->get()->map(function ($record) {
            return [
                'date'   => $record->attendance_date,
                'name'   => $record->student->full_name ?? '-',
                'id'     => $record->student->id_number ?? '-',
                'phone'  => $record->student->phone_number ?? '-',
                'status' => $record->status,
                'notes'  => $record->notes ?? '-',
            ];
        })->toArray();
    }
}
