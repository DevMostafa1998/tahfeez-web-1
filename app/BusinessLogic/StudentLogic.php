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
