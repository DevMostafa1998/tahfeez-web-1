<?php

namespace App\Http\Controllers;

use App\Models\QuranMemTest;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use App\BusinessLogic\QuranMemTestLogic; // استيراد الكلاس الجديد
class QuranMemTestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $testLogic;

    // استخدام الـ Constructor لحقن المنطق البرمجي
    public function __construct(QuranMemTestLogic $testLogic)
    {
        $this->testLogic = $testLogic;
    }
    public function index()
    {
        $tests = $this->testLogic->getAllTests();// أو $this->testLogic->getAllTests()
        $students = Student::all();
        return view('quran_tests.index', compact('tests', 'students'));
    }
    /**
     * Show the form for creating a new resource.
     */
   public function create()
    {
        $students = Student::all();
        return view('quran_tests.create', compact('students'));
    }

    /**
     * Store a newly created resource in storage.
     */
 public function store(Request $request)
    {
        $data = $request->validate([
            'studentId'     => 'required|exists:student,id',
            'date'          => 'required|date',
            'juz_count'     => 'required|integer|min:1|max:30',
            'examType'      => 'required|in:سرد,اجزاء مجتمعه',
            'result_status' => 'required|in:ناجح,راسب',
            'note'          => 'nullable|string',
        ]);

        $test = $this->testLogic->storeTest($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل اختبار الطالب ' . $test->student->full_name . ' بنجاح!'
            ]);
        }

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
        $data = $request->validate([
            'studentId'     => 'required|exists:student,id',
            'date'          => 'required|date',
            'juz_count'     => 'required|integer|min:1|max:30',
            'examType'      => 'required|in:سرد,اجزاء مجتمعه',
            'result_status' => 'required|in:ناجح,راسب',
            'note'          => 'nullable|string',
        ]);

        $test = $this->testLogic->updateTest($id, $data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث سجل الطالب ' . $test->student->full_name . ' بنجاح.'
            ]);
        }

        return redirect()->route('quran_tests.index')->with('success', 'تم تحديث بيانات الاختبار بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy($id)
    {
        $this->testLogic->deleteTest($id);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف سجل الاختبار بنجاح.'
        ]);
    }
}
