<?php

namespace App\Http\Controllers;

use App\Models\QuranMemTest;
use Illuminate\Http\Request;
use App\Models\Student;

class QuranMemTestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tests = QuranMemTest::with('student')->get();
        $students = Student::all();
        return view('quran_tests.index', compact('tests', 'students'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // جلب قائمة الطلاب لتظهر في قائمة الاختيار (Select Menu)
        $students = Student::all();

        // إرسال البيانات إلى ملف الواجهة
        return view('quran_tests.create', compact('students'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'studentId' => 'required|exists:student,id',
            'date' => 'required|date',
            'juz_count' => 'required|integer|min:1|max:30',
            'examType' => 'required|in:سرد,اجزاء مجتمعه',
            'result_status' => 'required|in:ناجح,راسب',
            'note' => 'nullable|string',
        ]);

        QuranMemTest::create($data);
        return redirect()->back()->with('success', 'تم تسجيل الاختبار بنجاح');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $test = QuranMemTest::findOrFail($id);

        $data = $request->validate([
            'studentId' => 'required|exists:student,id',
            'date' => 'required|date',
            'juz_count' => 'required|integer|min:1|max:30',
            'examType' => 'required|in:سرد,اجزاء مجتمعه',
            'result_status' => 'required|in:ناجح,راسب',
            'note' => 'nullable|string',
        ]);

        $test->update($data);

        return redirect()->route('quran_tests.index')->with('success', 'تم تحديث بيانات الاختبار بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
