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
        'is_displaced'    => isset($data['is_displaced']) ? 1 : 0,
        'user_id'         => Auth::id(),
        'creation_by'     => Auth::user()->id,
        'birth_place'     => $data['birth_place'] ?? null,
        'center_name'     => $data['center_name'] ?? null,
        'mosque_name'     => $data['mosque_name'] ?? null,
        'mosque_address'  => $data['mosque_address'] ?? null,
        'whatsapp_number' => $data['whatsapp_number'] ?? null,
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
        return Student::whereNull('deleted_at')->latest()->paginate(10);
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
