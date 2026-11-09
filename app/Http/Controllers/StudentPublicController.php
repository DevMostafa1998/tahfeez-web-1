<?php

namespace App\Http\Controllers;

use App\BusinessLogic\StudentLogic;
use App\Models\Student;
use App\Models\Course;
use App\Models\Group;
use Illuminate\Http\Request;
use App\BusinessLogic\UserLogic;
use Illuminate\Support\Facades\DB;

class StudentPublicController extends Controller
{
    protected $studentLogic;
    private $userLogic;

    public function __construct(StudentLogic $studentLogic, UserLogic $userLogic)
    {
        $this->studentLogic = $studentLogic;
        $this->userLogic = $userLogic;
    }

    public function index(Request $request)
    {
        $groups = Group::all();
        $teachers = $this->userLogic->getAllTeachers();

        // جلب التصنيفات المتاحة من قاعدة البيانات لتظهر في الصفحة العامة
        $categories = DB::table('categorie')->get();

        $student_courses = Course::where(function ($q) {
            $q->where('type', 'students')
                ->orWhereNull('type');
        })->get();

        return view('students.create_public', compact('student_courses', 'groups', 'teachers', 'categories'));
    }

    public function store(Request $request)
    {
        // 1. فك تشفير الـ payload القادم من الجافاسكريبت وتحويله لمصفوفة بيانات طبيعية
        if ($request->filled('payload')) {
            try {
                $decryptedString = base64_decode($request->input('payload'));

                // تحويل السلسلة النصية (Query String) إلى مصفوفة متغيرات PHP
                parse_str($decryptedString, $decodedData);

                if (is_array($decodedData)) {
                    // دمج البيانات داخل الـ Request لتبدو طبيعية تماماً للنظام
                    $request->merge($decodedData);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء معالجة البيانات المشفرة: ' . $e->getMessage()
                ], 500);
            }
        }

        // 2. التحقق من البيانات كالمعتاد بعد دمجها
        $validated = $request->validate([
            'full_name'       => 'required|string|max:255',
            'id_number'       => 'required|string|unique:student,id_number|digits:9',
            'date_of_birth'   => 'required|date',
            'phone_number'    => 'required|string|max:15',
            'address'         => 'required|string',
            'group_id'        => 'required|exists:group,id',
            'categorie_id'    => 'required|exists:categorie,id',
            'is_displaced'    => 'sometimes|in:0,1',
            'courses'         => 'nullable|array',
            'birth_place'     => 'required|string|max:255',
            'center_name'     => 'required|string|max:255',
            'mosque_name'     => 'required|string|max:255',
            'mosque_address'  => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:15',
            'gender'          => 'sometimes|in:male,female',
            'user_id'         => 'required',
        ]);

        // 3. إرسال البيانات إلى البزنس لوجيك
        $this->studentLogic->storeStudentPublic($validated);

        return $request->ajax()
            ? response()->json(['success' => true, 'message' => 'تم إضافة الطالب بنجاح'])
            : redirect()->route('student_public.index')->with('success', 'تم إضافة الطالب بنجاح');
    }
}
