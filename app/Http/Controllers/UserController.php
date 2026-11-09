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
            return $this->userLogic->getUsersForDataTable($request);
        }

        $categories = DB::table('categorie')->get();
        $centers = DB::table('centers')->get();
        $mosques = \App\Models\Lov::getOptions('mosque_list');

        // جلب المركز المحدد من Session
        $selectedCenterId = session('selected_center_id');

        $all_courses = Course::where(function ($query) {
            $query->where('type', 'teachers')->orWhereNull('type');
        })
            ->when($selectedCenterId, function ($q) use ($selectedCenterId) {
                // يجلب دورات المركز المحدد فقط ويستثني العامة والملغاة
                return $q->where('center_id', $selectedCenterId);
            })
            ->get();

        return view('users.index', compact('categories', 'all_courses', 'centers', 'mosques'));
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
            'full_name'      => 'required|string|max:255',
            'id_number'      => 'required|unique:user,id_number',
            'password'       => 'required|string|min:6|confirmed',
            'date_of_birth'  => 'required|date',
            'phone_number'   => 'required',
            'address'        => 'required',
            'center_id'      => 'nullable|string|max:255',
            'category_id'    => 'required',
            'courses'        => 'nullable|array',
            'is_displaced'   => 'required|boolean',
            'gender'         => 'sometimes|in:male,female',
            'is_admin_rouls' => 'nullable|boolean',
        ]);

        $this->userLogic->storeUser($request->all());
        return redirect()->route('user.index')->with('success', 'تم إضافة المستخدم والدورات بنجاح!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $categories = DB::table('categorie')->get();
        $centers = DB::table('centers')->get();

        $selectedCenterId = session('selected_center_id');

        $all_courses = Course::where(function ($query) {
            $query->where('type', 'teachers')->orWhereNull('type');
        })
            ->when($selectedCenterId, function ($q) use ($selectedCenterId) {
                return $q->where('center_id', $selectedCenterId);
            })
            ->get();

        return view('profile.edit', compact('user', 'categories', 'all_courses', 'centers'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->has('update_courses_only')) {
            $this->userLogic->updateUser($user, $request->all());
            return redirect()->route('user.index')->with('success', 'تم تحديث الدورات بنجاح');
        }

        $request->validate([
            'full_name'    => 'required|string|max:255',
            'id_number'    => 'required|unique:user,id_number,' . $id,
            'phone_number' => 'required',
            'address'      => 'required',
            'category_id'  => 'required',
            'center_id'    => 'nullable|string|max:255',
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

    public function restore($id)
    {
        $restored = $this->userLogic->restoreUser($id);

        if ($restored) {
            return redirect()->back()->with('success', 'تم استعادة المحفظ بنجاح');
        }

        return redirect()->back()->with('error', 'حدث خطأ أثناء محاولة الاستعادة');
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
        $centers = DB::table('centers')->get();
        $mosques = \App\Models\Lov::getOptions('mosque_list');

        $selectedCenterId = session('selected_center_id');

        $all_courses = Course::where(function ($query) {
            $query->where('type', 'teachers')->orWhereNull('type');
        })
            ->when($selectedCenterId, function ($q) use ($selectedCenterId) {
                return $q->where('center_id', $selectedCenterId);
            })
            ->get();

        return view('users.create', compact('categories', 'all_courses', 'centers', 'mosques'));
    }

    public function getEditData($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function exportFullExcel()
    {
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;

        // جلب اختيار الجنس من الـ Session
        $selectedGender = session('selected_gender');

        $usersQuery = User::with(['category', 'courses']);

        if ($selectedCenterId) {
            $usersQuery->where(function ($q) use ($selectedCenterId) {
                $q->where('center_id', $selectedCenterId)
                    ->orWhereNull('center_id');
            });
        }

        // تطبيق فلتر الجنس إذا كان موجوداً
        if ($selectedGender) {
            $usersQuery->where('gender', $selectedGender);
        }

        $users = $usersQuery->get();
        $exporter = new ExportExcel();

        $headers = [
            'الاسم الكامل',
            'رقم الهوية',
            'رقم الجوال',
            'الجنس',
            'التصنيف',
            'الصلاحية',
            'عدد الدورات'
        ];

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
            'users_report_' . date('Y-m-d'),
            'تقرير جميع المستخدمين في النظام',
            'تقرير المستخدمين',
            $headers,
            $data,
            $columnsMapping
        );
    }

    public function getAllTeachers()
    {
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;

        // جلب اختيار الجنس
        $selectedGender = session('selected_gender');

        $query = User::where('is_admin', false);

        if ($selectedCenterId) {
            $query->where('center_id', $selectedCenterId);
        }

        // تطبيق الفلتر
        if ($selectedGender) {
            $query->where('gender', $selectedGender);
        }

        return $query->get();
    }
}
