<?php

namespace App\BusinessLogic;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class StudentLogic
{
    /**
     * جلب جميع الطلاب النشطين
     */
    public function getAllStudents()
    {
        return Student::whereNull('deleted_at')->latest()->paginate(10);
    }

    /**
     * حفظ طالب جديد
     */
    public function storeStudent(array $data)
    {
        $data = array_merge($data, [
            'user_id'     => Auth::id(),
            'creation_by' => Auth::user()->id,
            'created_at'  => now(),
        ]);

        return Student::create($data);
    }

    /**
     * جلب طالب معين بواسطة المعرف
     */
    public function getStudentById($id)
    {
        return Student::findOrFail($id);
    }

    /**
     * تحديث بيانات الطالب
     */
    public function updateStudent($id, array $data)
    {
        $student = $this->getStudentById($id);

        $updateData = array_merge($data, [
            'updated_by' => Auth::user()->id,
            'updated_at' => now(),
        ]);

        $student->update($updateData);
        return $student;
    }

    /**
     * حذف الطالب (حذف منطقي Soft Delete مع تحديث المستخدم)
     */
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
