<?php

namespace App\BusinessLogic;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class StudentLogic
{
    public function storeStudent(array $data)
    {
        //  تجهيز بيانات الطالب فقطد)
        $studentData = [
            'full_name'     => $data['full_name'],
            'id_number'     => $data['id_number'],
            'date_of_birth' => $data['date_of_birth'],
            'phone_number'  => $data['phone_number'],
            'address'       => $data['address'],
            'group_id'      => $data['group_id'] ?? null,
            'is_displaced'  => isset($data['is_displaced']) ? 1 : 0,
            'user_id'       => Auth::id(), // المحفظ الذي أضاف الطالب
            'creation_by'   => Auth::user()->id,
        ];

        //  إنشاء الطالب
        $student = Student::create($studentData);


        if (isset($data['courses']) && is_array($data['courses'])) {
            $student->courses()->attach($data['courses']);
        }

        return $student;
    }

    public function getStudentById($id)
    {
        return Student::with('courses')->findOrFail($id);
    }

    public function updateStudent($id, array $data)
    {
        $student = $this->getStudentById($id);

        $updateData = [
            'full_name'     => $data['full_name'],
            'id_number'     => $data['id_number'],
            'phone_number'  => $data['phone_number'],
            'address'       => $data['address'] ?? $student->address,
            'is_displaced'  => isset($data['is_displaced']) ? 1 : 0,
            'updated_by'    => Auth::user()->id,
        ];

    if (isset($data['group_id'])) {
        $updateData['group_id'] = $data['group_id'];
    }

    if (isset($data['date_of_birth'])) {
        $updateData['date_of_birth'] = $data['date_of_birth'];
    }
        $student->update($updateData);

        //  تحديث الدورات
        if (isset($data['courses']) && is_array($data['courses'])) {
            $student->courses()->sync($data['courses']);
        } else {
            $student->courses()->sync([]); // إزالة جميع الدورات إذا لم يتم إرسال أي دورة
        }

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
