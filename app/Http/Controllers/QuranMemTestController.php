<?php

namespace App\Http\Controllers;

use App\Models\QuranMemTest;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\BusinessLogic\QuranMemTestLogic;
use Exception;
use App\Models\User;

class QuranMemTestController extends Controller
{
    protected $testLogic;

    // استخدام الـ Constructor لحقن المنطق البرمجي
    public function __construct(QuranMemTestLogic $testLogic)
    {
        $this->testLogic = $testLogic;
    }

    public function index()
    {
        // جلب معرف المركز المحدد من Session
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;

        // جلب اختيار الجنس من Session
        $selectedGender = session('selected_gender');

        // 1. جلب الاختبارات المفلترة حسب المركز
        $testsQuery = QuranMemTest::with(['student.categorie'])
            ->whereNull('deleted_at')
            ->orderBy('date', 'desc');

        if ($selectedCenterId) {
            $testsQuery->whereHas('student', function ($studentQuery) use ($selectedCenterId) {
                $studentQuery->where('center_id', $selectedCenterId)
                    ->orWhereHas('groups', function ($groupQuery) use ($selectedCenterId) {
                        $groupQuery->where('center_id', $selectedCenterId);
                    });
            });
        }

        // تطبيق فلتر الجنس على الاختبارات (بناءً على جنس الطالب)
        if ($selectedGender) {
            $testsQuery->whereHas('student', function ($q) use ($selectedGender) {
                $q->where('gender', $selectedGender);
            });
        }

        $tests = $testsQuery->get();

        // 2. جلب الطلاب المفلترين حسب المركز والجنس (للاختيار في مودال الإضافة/التعديل)
        $studentsQuery = Student::whereNull('deleted_at');
        if ($selectedCenterId) {
            $studentsQuery->where(function ($q) use ($selectedCenterId) {
                $q->where('center_id', $selectedCenterId)
                    ->orWhereHas('groups', function ($g) use ($selectedCenterId) {
                        $g->where('center_id', $selectedCenterId);
                    });
            });
        }
        if ($selectedGender) {
            $studentsQuery->where('gender', $selectedGender);
        }
        $students = $studentsQuery->get();

        // 3. جلب المعلمين المفلترين حسب المركز والجنس
        $teachersQuery = User::where('is_admin', 0)->whereNull('deleted_at');
        if ($selectedCenterId) {
            $teachersQuery->where('center_id', $selectedCenterId);
        }
        if ($selectedGender) {
            $teachersQuery->where('gender', $selectedGender);
        }
        $teachers = $teachersQuery->get();

        // 4. جلب إعدادات التقديرات
        $settings = DB::table('exam_settings')->get();

        return view('quran_tests.index', compact('tests', 'students', 'teachers', 'settings'));
    }

    public function create()
    {
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;

        $selectedGender = session('selected_gender');

        $studentsQuery = Student::whereNull('deleted_at');
        if ($selectedCenterId) {
            $studentsQuery->where(function ($q) use ($selectedCenterId) {
                $q->where('center_id', $selectedCenterId)
                    ->orWhereHas('groups', function ($g) use ($selectedCenterId) {
                        $g->where('center_id', $selectedCenterId);
                    });
            });
        }
        if ($selectedGender) {
            $studentsQuery->where('gender', $selectedGender);
        }
        $students = $studentsQuery->get();

        $teachersQuery = User::where('is_admin', 0)->whereNull('deleted_at');
        if ($selectedCenterId) {
            $teachersQuery->where('center_id', $selectedCenterId);
        }
        if ($selectedGender) {
            $teachersQuery->where('gender', $selectedGender);
        }
        $teachers = $teachersQuery->get();

        $settings = DB::table('exam_settings')->get();

        return view('quran_tests.create', compact('students', 'settings', 'teachers'));
    }

    /**
     * جلب معلم الطالب بناءً على ID الطالب (AJAX)
     */
    public function getStudentTeacher(Request $request)
    {
        $student = Student::with('groups.teacher')->find($request->student_id);

        if (!$student) {
            return response()->json(['teacher_id' => null, 'teacher_name' => null]);
        }

        $latestGroup = $student->groups()->whereNull('group.deleted_at')->first();
        $teacher     = $latestGroup?->teacher;

        return response()->json([
            'teacher_id'   => $teacher?->id,
            'teacher_name' => $teacher?->full_name,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'studentId'        => 'required|exists:student,id',
            'date'             => 'required|date',
            'from_juz'         => 'required|integer|min:1|max:30',
            'to_juz'           => 'required|integer|min:1|max:30',
            'juz_count'        => 'required|integer|min:1|max:30',
            'score_percentage' => 'required|integer|min:0|max:100',
            'teacher_name'     => 'required|string|max:255',
            'tester_name'      => 'required|string|max:255',
            'examType'         => 'required|in:سرد,اجزاء مجتمعه',
            'note'             => 'nullable|string',
        ]);

        $score = $data['score_percentage'];
        $setting = DB::table('exam_settings')
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();

        $data['result_status'] = $setting ? $setting->grade_name : 'راسب';

        $test = $this->testLogic->storeTest($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل اختبار الطالب ' . ($test->student->full_name ?? '') . ' بنجاح!'
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
            'studentId'        => 'required|exists:student,id',
            'date'             => 'required|date',
            'from_juz'         => 'required|integer|min:1|max:30',
            'to_juz'           => 'required|integer|min:1|max:30',
            'juz_count'        => 'required|integer|min:1|max:30',
            'score_percentage' => 'required|integer|min:0|max:100',
            'teacher_name'     => 'required|string|max:255',
            'tester_name'      => 'required|string|max:255',
            'examType'         => 'required|in:سرد,اجزاء مجتمعه',
            'note'             => 'nullable|string',
        ]);

        $score = $data['score_percentage'];
        $setting = DB::table('exam_settings')
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();

        $data['result_status'] = $setting ? $setting->grade_name : 'راسب';

        $test = $this->testLogic->updateTest($id, $data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث سجل الطالب ' . ($test->student->full_name ?? '') . ' بنجاح.'
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
    /**
     * عرض صفحة إعدادات التقديرات والنسب المئوية
     */
    public function editGradeSettings()
    {
        $settings = DB::table('exam_settings')->get();

        return view('settings.grades', compact('settings'));
    }


    public function updateGradeSettings(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'grades' => 'required|array',
            'grades.*.min_score' => 'required|integer|min:0|max:100',
            'grades.*.max_score' => 'required|integer|min:0|max:100',
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->grades as $id => $scores) {
                    DB::table('exam_settings')
                        ->where('id', $id)
                        ->update([
                            'min_score' => $scores['min_score'],
                            'max_score' => $scores['max_score'],
                        ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث مديات النسب المئوية للنظام الذكي بنجاح!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ غير متوقع: ' . $e->getMessage()
            ], 500);
        }
    }
}
