<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BusinessLogic\MemorizationLogic;
use Illuminate\Support\Facades\DB;

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

        if (!is_array($data)) {
            return response()->json(['error' => 'بيانات غير صالحة'], 400);
        }

        // جلب معرف المحفظ من التوكن (Sanctum)
        $teacherId = $request->user() ? $request->user()->id : null;

        try {
            DB::beginTransaction();

            foreach ($data as $item) {
                // 1. تسجيل أو تحديث بيانات التسميع اليومي
                DB::table('student_daily_memorizations')->updateOrInsert(
                    [
                        'student_id'  => $item['student_id'],
                        'date'        => $item['recitation_date'],
                        'sura_name'   => $item['surah_name'],
                    ],
                    [
                        'verses_from' => $item['from_verse'],
                        'verses_to'   => $item['to_verse'],
                        'note'        => $item['notes'] ?? null,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]
                );

                // 2. تسجيل الحضور تلقائياً (جعل الحالة "حاضر")
                DB::table('student_attendances')->updateOrInsert(
                    [
                        'student_id'      => $item['student_id'],
                        'attendance_date' => $item['recitation_date'],
                    ],
                    [
                        'status'      => 'حاضر',
                        'recorded_by' => $teacherId, 
                        'notes'       => 'تسجيل تلقائي (نظام التسميع)',
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'تمت المزامنة وتسجيل الحضور بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'فشلت المزامنة: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getSurahs()
    {
        $surahs = DB::table('surahs')
            ->select('id', 'name_ar', 'verses_count', 'pages_count', 'juz_number')
            ->get();

        return response()->json(['data' => $surahs]);
    }
}
