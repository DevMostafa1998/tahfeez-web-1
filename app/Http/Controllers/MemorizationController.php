<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BusinessLogic\MemorizationLogic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemorizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $memorizationLogic;

    public function __construct(MemorizationLogic $logic)
    {
        $this->memorizationLogic = $logic;
    }
    public function index()
    {
        //
    }
    public function getAttendanceHistory(Request $request)
    {
        $request->validate([
            'group_id' => 'required|integer',
            'date' => 'required|date',
        ]);

        $user = $request->user();
        $groupId = $request->group_id;
        $date = $request->date;

        if (!$user->is_admin) {
            $hasAccess = DB::table('group')
                ->where('id', $groupId)
                ->where('UserId', $user->id)
                ->exists();

            if (!$hasAccess) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ليس لديك صلاحية لعرض هذه المجموعة'
                ], 403);
            }
        }

        // جلب الطلاب مع حالة الحضور
        $students = DB::table('student')
            ->join('student_group', 'student.id', '=', 'student_group.student_id')
            ->leftJoin('student_attendances', function ($join) use ($date) {
                $join->on('student.id', '=', 'student_attendances.student_id')
                    ->where('student_attendances.attendance_date', '=', $date);
            })
            ->where('student_group.group_id', $groupId)
            ->select(
                'student.id',
                'student.full_name',
                'student_attendances.status',
                'student_attendances.notes',
                DB::raw("'synced' as sync_status")
            )
            ->orderBy('student.full_name', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $students,
            'date' => $date,
            'group_id' => $groupId
        ]);
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
        // 1. جلب بيانات السورة للتأكد من عدد الآيات
        $surah = DB::table('surahs')->where('name_ar', $request->sura_name)->first();
        $maxVerses = $surah ? $surah->verses_count : 6236;

        $validated = $request->validate([
            'student_id'  => 'required|exists:student,id',
            'date'        => 'required|date',
            'sura_name'   => 'required|string|max:255',
            'verses_from' => "required|integer|min:1|max:{$maxVerses}",
            'verses_to'   => "required|integer|gte:verses_from|max:{$maxVerses}",
            'note'        => 'nullable|string',
        ], [
            'verses_to.max' => "خطأ: سورة {$request->sura_name} تحتوي على {$maxVerses} آية فقط.",
            'verses_from.max' => "خطأ: بداية الآيات لا يمكن أن تتجاوز عدد آيات السورة ({$maxVerses}).",
        ]);

        $this->memorizationLogic->storeDailyMemorization($validated);

        return response()->json(['success' => true, 'message' => 'تم تسجيل الحفظ بنجاح']);
    }
    public function syncBulk(Request $request)
    {
        $data = $request->input('memorizations');

        if (empty($data)) {
            return response()->json(['error' => 'لا توجد بيانات'], 400);
        }

        $teacherId = $request->user() ? $request->user()->id : null;

        try {
            DB::beginTransaction();

            foreach ($data as $item) {
                // للسماح بالتسميع أكثر من مرة للطالب
                DB::table('student_daily_memorizations')->insert([
                    'student_id'  => $item['student_id'],
                    'date'        => $item['recitation_date'],
                    'sura_name'   => $item['surah_name'],
                    'verses_from' => $item['from_verse'],
                    'verses_to'   => $item['to_verse'],
                    'note'        => $item['notes'] ?? null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                // تسجيل الحضور أو تحديثه بناءً على تاريخ التسميع
                DB::table('student_attendances')->updateOrInsert(
                    [
                        'student_id'      => $item['student_id'],
                        'attendance_date' => $item['recitation_date'],
                    ],
                    [
                        'status'      => 'حاضر',
                        'recorded_by' => $teacherId,
                        'updated_at'  => now(),
                    ]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'تم الحفظ بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('فشل المزامنة النهائي: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ في قاعدة البيانات'], 500);
        }
    }
    public function getSurahs()
    {
        $surahs = DB::table('surahs')
            ->select('id', 'name_ar', 'verses_count', 'pages_count', 'juz_number')
            ->get();

        return response()->json(['data' => $surahs]);
    }
    // داخل كلاس MemorizationController في ملف MemorizationController.php

    // Laravel: MemorizationController.php
    public function syncAttendance(Request $request)
    {
        $data = $request->input('attendance');
        $teacherId = $request->user()->id;

        try {
            DB::beginTransaction();
            foreach ($data as $item) {
                DB::table('student_attendances')->updateOrInsert(
                    [
                        'student_id'      => $item['student_id'],
                        'attendance_date' => $item['attendance_date'],
                    ],
                    [
                        'status'      => $item['status'],
                        'notes'       => $item['notes'] ?? null,
                        'recorded_by' => 'تطبيق الجوال يدوي',
                        'updated_at'  => now(),
                    ]
                );
            }
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
