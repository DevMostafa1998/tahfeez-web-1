<?php

namespace App\Http\Controllers;

use App\BusinessLogic\StudentLogic;
use App\Models\Student;
use App\Models\Course;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    protected $studentLogic;
    /**
     * Display a listing of the resource.
     */
    public function __construct(StudentLogic $studentLogic)
    {
        $this->studentLogic = $studentLogic;
    }
    public function index(Request $request)
    {
        if ($request->has('export_all')) {
            $students = Student::all();
            return $this->exportExcel($students);
        }
        if ($request->ajax()) {
            $query = Student::query();

            // 1. الإحصائيات الكلية
            $totalData = $query->count();
            $totalFiltered = $totalData;

            // 2. البحث (Search)
            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'LIKE', "%{$search}%")
                        ->orWhere('id_number', 'LIKE', "%{$search}%")
                        ->orWhere('phone_number', 'LIKE', "%{$search}%");
                });
                $totalFiltered = $query->count();
            }

            // 3. الترتيب (Ordering)
            $columns = ['full_name', 'id_number', 'gender', 'is_displaced', 'id', 'id']; // ترتيب الأعمدة في الجدول
            if ($request->filled('order.0.column')) {
                $columnIdx = $request->input('order.0.column');
                $dir = $request->input('order.0.dir');
                $query->orderBy($columns[$columnIdx], $dir);
            }

            // 4. الترقيم (Pagination)
            $start = $request->input('start');
            $length = $request->input('length');
            $students = $query->offset($start)->limit($length)->get();

            // 5. تجهيز البيانات للعرض (Formatting)
            $data = $students->map(function ($student) {
                return [
                    'full_name' => '<span class="fw-bold">' . $student->full_name . '</span>',
                    'id_number' => '<span class="badge bg-light text-dark border px-4 py-2 fw-bold">' . $student->id_number . '</span>',
                    'gender' => $student->gender == 'male'
                        ? '<span class="badge bg-blue-subtle text-primary border px-3"><i class="bi bi-person-fill ms-1"></i> ذكر </span>'
                        : '<span class="badge bg-pink-subtle text-danger border px-3"><i class="bi bi-person ms-1"></i> أنثى </span>',
                    'status' => $student->is_displaced
                        ? '<span class="badge rounded-pill border bg-warning-subtle text-dark">نازح</span>'
                        : '<span class="badge rounded-pill border bg-success-subtle text-success">مقيم</span>',
                    'courses' => '<span class="badge bg-warning text-dark rounded-pill shadow-sm px-3">' . ($student->courses()->count()) . ' دورات</span>',
                    'actions' => '
                    <div class="d-flex justify-content-center gap-2">
                        <a href="' . route('parents.index', ['id_number' => $student->id_number]) . '" class="btn btn-sm btn-outline-secondary rounded-circle action-btn" title="عرض ولي الأمر"><i class="bi bi-person-vcard"></i></a>
                        <button class="btn btn-sm btn-outline-info rounded-circle action-btn course-btn" data-id="' . $student->id . '" data-name="' . $student->full_name . '"><i class="bi bi-journal-plus"></i></button>
                        <button class="btn btn-sm btn-outline-warning rounded-circle action-btn edit-student-btn" data-id="' . $student->id . '"><i class="bi bi-pencil-square"></i></button>
                        <button type="button" onclick="confirmDelete(' . $student->id . ')" class="btn btn-sm btn-outline-danger rounded-circle action-btn"><i class="bi bi-trash3"></i></button>
                    </div>'
                ];
            });

            return response()->json([
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data
            ]);
        }

        return view('students.index');
    }
    public function exportExcel()
    {
        // جلب الطلاب مع علاقة الدورات لتحسين الأداء
        $students = \App\Models\Student::withCount('courses')->get();
        $date = date('Y-m-d');
        $fileName = "تقرير_الطلاب_{$date}.xls";

        // بناء محتوى الجدول
        $output = '
    <html dir="rtl" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <style>
            .name-column { width: 300px; text-align: right; } /* زيادة عرض عمود الاسم */
            .header-title { font-size: 16px; font-weight: bold; height: 40px; text-align: center; background-color: #e7f1ff; }
            .table-header { background-color: #f2f2f2; font-weight: bold; border: 1px solid #000; text-align: center; }
            td { border: 0.5pt solid #ccc; padding: 5px; }
            .text-center { text-align: center; }
        </style>
    </head>
    <body>
        <table>
            <tr>
                <td colspan="5" class="header-title">تقرير الطلاب بتاريخ: ' . $date . '</td>
            </tr>
            <tr>
                <th class="table-header name-column">اسم الطالب/ة</th>
                <th class="table-header" style="width:120px;">رقم الهوية</th>
                <th class="table-header" style="width:80px;">الجنس</th>
                <th class="table-header" style="width:80px;">الحالة</th>
                <th class="table-header" style="width:100px;">عدد الدورات</th>
            </tr>';

        foreach ($students as $student) {
            $gender = ($student->gender == 'male') ? 'ذكر' : 'أنثى';
            $status = ($student->is_displaced) ? 'نازح' : 'مقيم';

            $output .= '
            <tr>
                <td class="name-column">' . htmlspecialchars($student->full_name) . '</td>
                <td class="text-center" style="mso-number-format:\'@\';">' . $student->id_number . '</td>
                <td class="text-center">' . $gender . '</td>
                <td class="text-center">' . $status . '</td>
                <td class="text-center">' . $student->courses_count . '</td>
            </tr>';
        }

        $output .= '</table></body></html>';

        // استخدام ترويسات متوافقة لتقليل رسائل التحذير
        return response($output)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename=' . $fileName)
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
    //  لجلب بيانات طالب واحد للمودال
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

        $student_courses = Course::where(function ($q) {
            $q->where('type', 'students')
                ->orWhereNull('type');
        })->get();

        return view('students.create', compact('student_courses', 'groups'));
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
            'group_id'      => 'nullable|exists:group,id',
            'is_displaced'  => 'nullable',
            'courses'       => 'nullable|array',
            'birth_place'   => 'required|string|max:255',
            'center_name'   => 'required|string|max:255',
            'mosque_name'   => 'required|string|max:255',
            'mosque_address' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:15',
            'gender'       => 'sometimes|in:male,female',

        ]);
        $this->studentLogic->storeStudent($validated);
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم إضافة الطالب']);
        }
        return redirect()->route('student.index')->with('success', 'تم إضافة الطالب والدورات بنجاح');
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
        $student = $this->studentLogic->getStudentById($id);
        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if ($request->has('update_courses_only')) {
            $student = \App\Models\Student::findOrFail($id);
            $student->courses()->sync($request->input('courses', []));
            return redirect()->back()->with('success', 'تم تحديث الدورات بنجاح');
        }

        $validatedData = $request->validate([
            'full_name'      => 'required|string|max:255',
            'id_number'      => 'required|string|digits:9',
            'date_of_birth'  => 'required|date',
            'phone_number'   => 'required|string|max:15',
            'address'        => 'required|string',
            'is_displaced'  => 'required|boolean',
            'birth_place'    => 'nullable|string|max:255',
            'center_name'    => 'nullable|string|max:255',
            'mosque_name'    => 'nullable|string|max:255',
            'mosque_address' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:15',
            'gender'       => 'sometimes|in:male,female',
        ]);

        $this->studentLogic->updateStudent($id, $validatedData);

        return redirect()->route('student.index')->with('success', 'تم تحديث بيانات الطالب بنجاح');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->studentLogic->deleteStudent($id);
        return redirect()->route('student.index')->with('success', 'تم حذف الطالب بنجاح');
    }
}
