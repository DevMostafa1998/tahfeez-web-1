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
        $maxVerses = $surah ? $surah->verses_count : 6236; // افتراضي كحد أقصى عام إذا لم توجد السورة

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
}
