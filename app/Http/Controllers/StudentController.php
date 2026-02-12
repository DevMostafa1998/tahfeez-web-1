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
            $students = Student::all();
            return $this->exportExcel($students);
        }

        if ($request->ajax()) {
            // التحقق مما إذا كان الطلب للأرشيف
            $showArchived = $request->input('archived') == 'true';

            // بناء الاستعلام بناءً على الحالة
            $query = $showArchived ? Student::onlyTrashed() : Student::query();

            $totalData = $query->count();
            $totalFiltered = $totalData;

            // البحث
            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'LIKE', "%{$search}%")
                        ->orWhere('id_number', 'LIKE', "%{$search}%");
                });
            }
            if ($request->has('order')) {
                $columnIndex = $request->input('order.0.column'); // رقم العمود المنقور عليه
                $columnName = $request->input("columns.{$columnIndex}.name"); // اسم العمود
                $columnDirection = $request->input('order.0.dir'); // اتجاه الفرز asc أو desc

                // التأكد من أن العمود قابل للفرز وليس عمود "الإجراءات" أو "الدورات"
                if ($columnName && !in_array($columnName, ['actions', 'courses'])) {
                    $query->orderBy($columnName, $columnDirection);
                }
            } else {
                $query->orderBy('full_name', 'asc'); // فرز افتراضي
            }
            $totalFiltered = $query->count();

            $students = $query->offset($request->input('start'))
                ->limit($request->input('length'))
                ->get();

            $data = $students->map(function ($student) use ($showArchived) {
                $actionButtons = '';

                if ($showArchived) {
                    // زر الاستعادة يظهر فقط في الأرشيف
                    $actionButtons = '<button onclick="restoreStudent(' . $student->id . ')" class="btn btn-sm btn-outline-success rounded-pill px-3"><i class="bi bi-arrow-counterclockwise"></i> استعادة</button>';
                } else {
                    // الأزرار للطلاب الحاليين (تم إضافة زر التقرير هنا)
                    $actionButtons = '
                <div class="d-flex justify-content-center gap-2">
                    <a href="' . route('parents.index', ['id_number' => $student->id_number]) . '"
                       class="btn btn-sm btn-outline-secondary rounded-circle action-btn"
                       title="عرض التقرير">
                       <i class="bi bi-file-earmark-person"></i>
                    </a>

                    <button class="btn btn-sm btn-outline-info rounded-circle action-btn course-btn" data-id="' . $student->id . '" data-name="' . $student->full_name . '" title="الدورات"><i class="bi bi-journal-plus"></i></button>
                    <button class="btn btn-sm btn-outline-warning rounded-circle action-btn edit-student-btn" data-id="' . $student->id . '" title="تعديل"><i class="bi bi-pencil-square"></i></button>
                    <button type="button" onclick="confirmDelete(' . $student->id . ')" class="btn btn-sm btn-outline-danger rounded-circle action-btn" title="حذف"><i class="bi bi-trash3"></i></button>
                </div>';
                }

                return [
                    'full_name' => '<span class="fw-bold">' . $student->full_name . '</span>',
                    'id_number' => '<span class="badge bg-light text-dark border px-4 py-2">' . $student->id_number . '</span>',
                    'gender'    => $student->gender == 'male' ? '<span class="badge bg-blue-subtle text-primary border px-3">ذكر</span>' : '<span class="badge bg-pink-subtle text-danger border px-3">أنثى</span>',
                    'status'    => $showArchived ? '<span class="badge rounded-pill bg-danger text-white">محذوف</span>' : ($student->is_displaced ? '<span class="badge rounded-pill bg-warning-subtle text-dark">نازح</span>' : '<span class="badge rounded-pill bg-success-subtle text-success">مقيم</span>'),
                    'courses'   => '<span class="badge bg-warning text-dark rounded-pill px-3">' . ($student->courses()->count()) . ' دورات</span>',
                    'actions'   => $actionButtons
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
        $students = Student::withCount('courses')->get();
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
            'full_name'       => 'required|string|max:255',
            'id_number'       => 'required|string|unique:student,id_number|digits:9',
            'date_of_birth'   => 'required|date',
            'phone_number'    => 'required|string|max:15',
            'address'         => 'required|string',
            'group_id'        => 'nullable|exists:group,id',
            'is_displaced'    => 'required|in:0,1',
            'courses'         => 'nullable|array',
            'birth_place'     => 'required|string|max:255',
            'center_name'     => 'required|string|max:255',
            'mosque_name'     => 'required|string|max:255',
            'mosque_address'  => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:15',
            'gender'          => 'sometimes|in:male,female',
        ]);
        $this->studentLogic->storeStudent($validated);
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم إضافة الطالب']);
        }
        return redirect()->route('student.index')->with('success', 'تم إضافة الطالب والدورات بنجاح');
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
            $student = Student::findOrFail($id);
            $student->courses()->sync($request->input('courses', []));
            return redirect()->back()->with('success', 'تم تحديث الدورات بنجاح');
        }

        $validatedData = $request->validate([
            'full_name'      => 'required|string|max:255',
            'id_number'      => 'required|string|digits:9',
            'date_of_birth'  => 'required|date',
            'phone_number'   => 'required|string|max:15',
            'address'        => 'required|string',
            'is_displaced'  => 'required|in:0,1',
            'birth_place'    => 'nullable|string|max:255',
            'center_name'    => 'nullable|string|max:255',
            'mosque_name'    => 'nullable|string|max:255',
            'mosque_address' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:15',
            'gender'          => 'sometimes|in:male,female',
        ]);

        $this->studentLogic->updateStudent($id, $validatedData);

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
        // البحث عن الطالب حتى لو كان محذوفاً
        $student = Student::withTrashed()->find($id);

        if ($student) {
            $student->restore();
            return redirect()->back()->with('success', 'تم استعادة سجل الطالب بنجاح');
        }

        return redirect()->back()->with('error', 'الطالب غير موجود');
    }
}
