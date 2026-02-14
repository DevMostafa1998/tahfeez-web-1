<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\BusinessLogic\UserLogic;
use Illuminate\Support\Facades\DB;
use App\BusinessLogic\ExportExcel;

class UserController extends Controller
{
    protected $userLogic;

    public function __construct(UserLogic $userLogic)
    {
        $this->userLogic = $userLogic;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // نمرر الطلب بالكامل للمنطق لمعالجة الترتيب والبحث
            return $this->userLogic->getUsersForDataTable($request);
        }

        $categories = DB::table('categorie')->get();
        $all_courses = Course::where(function ($query) {
            $query->where('type', 'teachers')->orWhereNull('type');
        })->get();

        return view('users.index', compact('categories', 'all_courses'));
    }
    public function show($id)
    {
        $teacher = User::with(['courses', 'groups' => function ($query) {
            $query->withCount('students');
        }])->findOrFail($id);

        return view('profile.index', compact('teacher'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'full_name'     => 'required|string|max:255',
            'id_number'     => 'required|unique:user,id_number',
            'password'      => 'required|string|min:6|confirmed',
            'date_of_birth' => 'required|date',
            'phone_number'  => 'required',
            'address'       => 'required',
            'category_id'   => 'required',
            'courses'       => 'nullable|array',
            'is_displaced'  => 'required|boolean',
            'gender'       => 'sometimes|in:male,female',
            'is_admin_rouls' => 'nullable|boolean',
        ]);

        $this->userLogic->storeUser($request->all());
        return redirect()->route('user.index')->with('success', 'تم إضافة المستخدم والدورات بنجاح!');
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $categories = DB::table('categorie')->get();
        $all_courses = Course::where(function ($query) {
            $query->where('type', 'teachers')->orWhereNull('type');
        })->get();

        return view('profile.edit', compact('user', 'categories', 'all_courses'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->has('update_courses_only')) {
            $this->userLogic->updateUser($user, $request->all());
            return redirect()->route('user.index')->with('success', 'تم تحديث الدورات بنجاح');
        }

        // التحقق  لباقي العمليات
        $request->validate([
            'full_name'    => 'required|string|max:255',
            'id_number'    => 'required|unique:user,id_number,' . $id,
            'phone_number' => 'required',
            'address'      => 'required',
            'category_id'  => 'required',
            'password'     => 'nullable|string|min:6|confirmed',
            'courses'      => 'nullable|array',
            'is_displaced' => 'required|boolean',
            'gender'       => 'sometimes|in:male,female',
        ]);
        $data = $request->all();
        if (!$request->has('is_admin_rouls')) {
            $data['is_admin_rouls'] = 0;
        }
        $this->userLogic->updateUser($user, $data);
        return redirect()->route('user.index')->with('success', 'تم تحديث البيانات بنجاح');
    }

    public function destroy($id)
    {
        if ($id == 1) {
            return redirect()->route('user.index')->with('error', 'لا يمكن حذف المستخدم الرئيسي للمنظومة!');
        }
        $user = User::findOrFail($id);
        $this->userLogic->deleteUser($user);
        return redirect()->route('user.index')->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function create()
    {
        $categories = DB::table('categorie')->get();
        $all_courses = Course::where(function ($query) {
            $query->where('type', 'teachers')->orWhereNull('type');
        })->get();
        return view('users.create', compact('categories', 'all_courses'));
    }
    public function getEditData($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }
    public function exportFullExcel()
    {
        $users = User::with(['category', 'courses'])->get();

        $exporter = new ExportExcel();

        // تعريف العناوين التي ستظهر في رأس ملف الإكسل
        $headers = [
            'الاسم الكامل',
            'رقم الهوية',
            'رقم الجوال',
            'الجنس',
            'التصنيف',
            'الصلاحية',
            'عدد الدورات'
        ];

        // تحويل البيانات لمصفوفة تتناسب مع توقعات الكلاس
        $data = $users->map(function ($user) {
            return [
                'full_name'     => $user->full_name,
                'id_number'     => $user->id_number,
                'phone_number'  => $user->phone_number,
                'gender'        => ($user->gender == 'male' || $user->gender == 'ذكر') ? 'ذكر' : 'أنثى',
                'category_name' => $user->category->name ?? '---',
                'role'          => $user->is_admin ? 'مسؤول' : ($user->is_admin_rouls ? 'محفظ' : 'مستخدم'),
                'courses_count' => $user->courses->count(),
            ];
        })->toArray();

        // ترتيب المفاتيح كما هي في المصفوفة أعلاه
        $columnsMapping = [
            'full_name',
            'id_number',
            'phone_number',
            'gender',
            'category_name',
            'role',
            'courses_count'
        ];

        return $exporter->export(
            'users_report_' . date('Y-m-d'), // اسم الملف
            'تقرير جميع المستخدمين في النظام',  // عنوان الجدول بالداخل
            $headers,
            $data,
            $columnsMapping
        );
    }
}
