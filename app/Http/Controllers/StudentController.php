<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::whereNull('deleted_at')->latest()->get();
        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'     => 'required|string|max:255',
            'id_number'     => 'required|string|unique:student,id_number|digits:9',
            'date_of_birth' => 'required|date',
            'phone_number'  => 'required|string|max:15',
            'address'       => 'required|string',
            'is_displaced'  => 'required|boolean',
        ]);
        $data = array_merge($validated, [
            'user_id'     => Auth::id(),
            'creation_by' => Auth::user()->id,
            'created_at'  => now(),
        ]);

        Student::create($data);
        return redirect()->route('student.index')->with('success', 'تم إضافة الطالب بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $student = Student::findOrFail($id);
        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'id_number' => 'required|numeric|digits:9',
            'phone_number' => 'required|numeric|digits:10',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'is_displaced' => 'required|boolean',
        ]);
        $student->update(array_merge($validatedData, [
            'updated_by' => Auth::user()->id,
            'updated_at' => now(),
        ]));
        $student->update($request->all());

        return redirect()->route('student.index')->with('success', 'تم تحديث بيانات الطالب بنجاح');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->update([
            'deleted_by' => Auth::user()->id,
            'deleted_at' => now(),
        ]);
        $student->delete();
        return redirect()->route('student.index')->with('success', 'تم حذف الطالب بنجاح');
    }
}
