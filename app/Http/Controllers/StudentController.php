<?php

namespace App\Http\Controllers;

use App\BusinessLogic\StudentLogic;
use App\Models\Student;
use App\Models\Course;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    protected $studentLogic;

    public function __construct(StudentLogic $studentLogic)
    {
        $this->studentLogic = $studentLogic;
    }

    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
    {
        if ($request->has('export_all')) {
            return $this->studentLogic->exportStudentsData();
        }

        if ($request->ajax()) {
            $data = $this->studentLogic->getDatatableStudents($request);
            return response()->json($data);
        }

        $mosques = \App\Models\Lov::getOptions('mosque_list');
        $categories = DB::table('categorie')->get();
        $centers = DB::table('centers')->get();

        //  جلب رقم المركز المحدد حالياً من الـ Session
        $selectedCenterId = session('selected_center_id');

        //  جلب دورات الطلاب المخصصة للمركز الحالي + الدورات العامة (NULL)
        $student_courses = \App\Models\Course::where(function ($q) {
            $q->where('type', 'students')->orWhereNull('type');
        })
       ->when($selectedCenterId, function ($q) use ($selectedCenterId) {
        // عند تحديد مركز: يجلب دورات هذا المركز فقط
        return $q->where('center_id', $selectedCenterId);
    })
        ->get();

        return view('students.index', compact('mosques', 'categories', 'centers', 'student_courses'));
    }

    public function exportExcel()
    {
        return $this->studentLogic->exportStudentsData();
    }

    public function getStudentData($id)
    {
        $student = Student::findOrFail($id);
        return response()->json($student);
    }

    public function getStudentCourses($id)
    {
        $student = Student::findOrFail($id);
        $courseIds = $student->courses()->pluck('course_id')->toArray();
        return response()->json($courseIds);
    }

    /**
     * Show the form for creating a new resource.
     */
   public function create()
    {
        $groups = Group::all();
        $categories = DB::table('categorie')->get();
        $centers = DB::table('centers')->get();
        $mosques = \App\Models\Lov::getOptions('mosque_list');

        // جلب المركز المحدد من Session
        $selectedCenterId = session('selected_center_id');

        $student_courses = Course::where(function ($q) {
            $q->where('type', 'students')
              ->orWhereNull('type');
        })
        ->when($selectedCenterId, function ($q) use ($selectedCenterId) {
        return $q->where('center_id', $selectedCenterId);
    })
        ->get();

        return view('students.create', compact('mosques', 'student_courses', 'groups', 'categories', 'centers'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('full_name') && $request->input('full_name')) {
            $encodedStr = $request->input('full_name');
            $decoded = base64_decode($encodedStr, true);
            if ($decoded !== false) {
                $utf8Str = preg_replace_callback('/./s', function($match) {
                    return '%' . sprintf('%02X', ord($match[0]));
                }, $decoded);
                $rawName = urldecode($utf8Str);
                $request->merge(['full_name' => trim($rawName)]);
            }
        }

        $validated = $request->validate([
            'full_name'       => 'required|string|max:255',
            'id_number'       => 'required|string|unique:student,id_number|digits:9',
            'father_id'       => 'nullable|string|digits:9',
            'date_of_birth'   => 'required|date',
            'phone_number'    => 'required|string|max:15',
            'address'         => 'required|string',
            'group_id'        => 'nullable|exists:group,id',
            'categorie_id'    => 'required|exists:categorie,id',
            'is_displaced'    => 'required|in:0,1',
            'courses'         => 'nullable|array',
            'birth_place'     => 'required|string|max:255',
            'center_id'     => 'nullable|string|max:255',
            'mosque_name'     => 'required|string|max:255',
            'mosque_address'  => 'required|string|max:255',
            'whatsapp_number' => 'nullable|string|max:15',
            'gender'          => 'required|in:male,female',
        ]);

        $this->studentLogic->storeStudent($validated);

        return $request->ajax()
            ? response()->json(['success' => true, 'message' => 'تم إضافة الطالب بنجاح'])
            : redirect()->route('student.index')->with('success', 'تم إضافة الطالب بنجاح');
    }

    /**
     * Show the form for editing the specified resource.
     */
public function edit($id)
    {
        $student = $this->studentLogic->getStudentById($id);

        // جلب المراكز لصفحة الـ edit العادية
        $centers = DB::table('centers')->get();

        return view('students.edit', compact('student', 'centers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if ($request->has('update_courses_only')) {
            $student = Student::findOrFail($id);
            $student->courses()->sync($request->input('courses', []));

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم تحديث الدورات بنجاح']);
            }
            return redirect()->back()->with('success', 'تم تحديث الدورات بنجاح');
        }

        if ($request->has('full_name') && $request->input('full_name')) {
    $encodedStr = $request->input('full_name');

    if (base64_encode(base64_decode($encodedStr, true)) === $encodedStr) {
        $decoded = base64_decode($encodedStr, true);
        if ($decoded !== false) {
            $utf8Str = preg_replace_callback('/./s', function($match) {
                return '%' . sprintf('%02X', ord($match[0]));
            }, $decoded);
            $rawName = urldecode($utf8Str);
            $request->merge(['full_name' => trim($rawName)]);
        }
    }
}

        $validatedData = $request->validate([
            'full_name'      => 'required|string|max:255',
            'id_number'      => ['required', 'string', 'digits:9', Rule::unique('student')->ignore($id)],
            'father_id'       => ['nullable', 'string', 'digits:9'],
            'date_of_birth'  => 'required|date',
            'phone_number'   => 'required|string|max:15',
            'address'        => 'required|string',
            'is_displaced'   => 'required|in:0,1',
            'categorie_id'   => 'required|exists:categorie,id',
            'birth_place'    => 'nullable|string|max:255',
            'center_id'    => 'nullable|string|max:255',
            'mosque_name'    => 'nullable|string|max:255',
            'mosque_address' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:15',
            'gender'         => 'sometimes|in:male,female',
        ], [
            'id_number.unique' => 'عذراً.. رقم هوية الطالب هذا مسجل مسبقاً في النظام لمستخدم آخر!',
            'father_id.unique' => 'عذراً.. رقم هوية الأب هذا مسجل مسبقاً في النظام لطالب آخر!',
            'id_number.digits' => 'رقم هوية الطالب يجب أن يتكون من 9 أرقام.',
            'father_id.digits' => 'رقم هوية الأب يجب أن يتكون من 9 أرقام.',
        ]);

        $this->studentLogic->updateStudent($id, $validatedData);

        if ($request->wantsJson() || $request->ajax()) {
    return response()->json([
        'success' => true,
        'message' => 'تم تحديث البيانات بنجاح.'
    ], 200, [], JSON_UNESCAPED_UNICODE);
}

        return redirect()->route('student.index')->with('success', 'تم تحديث بيانات الطالب بنجاح');
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy($id)
    {
        $this->studentLogic->deleteStudent($id);
        return redirect()->route('student.index')->with('success', 'تم حذف الطالب بنجاح');
    }

    public function restore($id)
    {
        if ($this->studentLogic->restoreStudent($id)) {
            return redirect()->back()->with('success', 'تم استعادة سجل الطالب بنجاح');
        }
        return redirect()->back()->with('error', 'الطالب غير موجود');
    }

  public function editPublic($id)
    {
        $student = $this->studentLogic->getStudentById($id);

        $groups = \App\Models\Group::all();
        $categories = DB::table('categorie')->get();
        $centers = DB::table('centers')->get();

        $selectedCenterId = session('selected_center_id');

        $student_courses = \App\Models\Course::where(function ($q) {
            $q->where('type', 'students')->orWhereNull('type');
        })
       ->when($selectedCenterId, function ($q) use ($selectedCenterId) {
        return $q->where('center_id', $selectedCenterId);
    })
        ->get();

        return view('students.edit_modal_unified', compact('student', 'groups', 'categories', 'student_courses', 'centers'));
    }

    /**
     * تحديث بيانات الطالب من الواجهة العامة.
     */
    public function updatePublic(Request $request, $id)
    {
       if ($request->has('full_name') && $request->input('full_name')) {
    $encodedStr = $request->input('full_name');

    if (base64_encode(base64_decode($encodedStr, true)) === $encodedStr) {
        $decoded = base64_decode($encodedStr, true);
        if ($decoded !== false) {
            $utf8Str = preg_replace_callback('/./s', function($match) {
                return '%' . sprintf('%02X', ord($match[0]));
            }, $decoded);
            $rawName = urldecode($utf8Str);
            $request->merge(['full_name' => trim($rawName)]);
        }
    }
}

        $validatedData = $request->validate([
            'full_name'       => 'required|string|max:255',
            'id_number'       => ['required', 'string', 'digits:9', \Illuminate\Validation\Rule::unique('student')->ignore($id)],
            'father_id'       => ['nullable', 'string', 'digits:9'],
            'date_of_birth'   => 'required|date',
            'phone_number'    => 'required|string|max:15',
            'address'         => 'required|string',
            'is_displaced'    => 'required|in:0,1',
            'categorie_id'    => 'required|exists:categorie,id',
            'birth_place'     => 'nullable|string|max:255',
            'center_id'     => 'nullable|string|max:255',
            'mosque_name'     => 'nullable|string|max:255',
            'mosque_address'  => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:15',
            'gender'          => 'sometimes|in:male,female',
        ], [
            'id_number.unique' => 'عذراً.. رقم هوية الطالب هذا مسجل مسبقاً في النظام لمستخدم آخر!',
            'father_id.unique' => 'عذراً.. رقم هوية الأب هذا مسجل مسبقاً في النظام لطالب آخر!',
            'id_number.digits' => 'رقم هوية الطالب يجب أن يتكون من 9 أرقام.',
            'father_id.digits' => 'رقم هوية الأب يجب أن يتكون من 9 أرقام.',
        ]);

        $this->studentLogic->updateStudent($id, $validatedData);

        if ($request->wantsJson() || $request->ajax()) {
    return response()->json([
        'success' => true,
        'message' => 'تم تحديث البيانات بنجاح.'
    ], 200, [], JSON_UNESCAPED_UNICODE);
}

        return redirect()->route('parent.login')->with('success', 'تم تحديث بيانات ابنكم بنجاح.');
    }
}
