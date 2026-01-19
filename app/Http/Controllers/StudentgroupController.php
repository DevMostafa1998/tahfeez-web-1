<?php

namespace App\Http\Controllers;

// use Illuminate\Container\Attributes\DB;

use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentgroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. جلب المجموعات
        $groups = Group::with('students')->withCount('students')
            ->whereNull('deleted_at')
            ->orderBy('creation_at', 'desc')
            ->get();

        // 2. جلب المحفظين (كما كان في كودك السابق)
        $teachers = DB::table('user')
            ->select('id', 'full_name')
            ->where('is_admin', 0)
            ->get();

        // 3. جلب الطلاب غير المسجلين في أي مجموعة (هذا هو الجزء الناقص)
        // ملاحظة: تأكد أن لديك مودل Student، وإذا كان الطلاب في جدول User قم بتغيير Student:: إلى User:: مع شرط الصلاحية
        $availableStudents = Student::whereDoesntHave('groups')->get();

        // 4. تمرير المتغير availableStudents للواجهة
        return view('groups.index', compact('groups', 'teachers', 'availableStudents'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $group = Group::findOrFail($request->group_id);

        // التحقق من صحة البيانات (مصفوفة من أرقام الطلاب)
        $request->validate([
            'student_ids' => 'array',
            'student_ids.*' => 'exists:student,id', // تأكد أن الجدول students واسم العمود id صحيح
        ]);

        // دالة sync تقوم بعمل:
        // 1. إضافة الطلاب الجدد المحددين.
        // 2. حذف الطلاب الذين كانوا في المجموعة وتم إزالة التحديد عنهم.
        // 3. الإبقاء على الطلاب المحددين مسبقاً.
        $group->students()->sync($request->student_ids);

        return redirect()->back()->with('success', 'تم تحديث قائمة الطلاب بنجاح');
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
    // في ملف StudentgroupController.php

    public function update(Request $request, string $id) // غيرنا الاسم من store إلى update لتناسب الـ Route
    {
        $group = Group::findOrFail($id); // الـ ID يأتي من الرابط

        $request->validate([
            'student_ids' => 'nullable|array', // nullable في حال أراد حذف كل الطلاب
            'student_ids.*' => 'exists:students,id',
        ]);

        // إذا كانت المصفوفة فارغة، نقوم بإرسال مصفوفة فارغة لـ sync لمسح جميع الارتباطات
        $studentIds = $request->input('student_ids', []);

        $group->students()->sync($studentIds);

        return redirect()->back()->with('success', 'تم تحديث قائمة طلاب مجموعة ' . $group->GroupName . ' بنجاح');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
