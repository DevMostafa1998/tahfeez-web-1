<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class ParentController extends Controller
{
    public function showStudentReport($id_number = null)
    {
        if (!$id_number) return redirect()->route('parent.login');

        $student = Student::where('id_number', $id_number)->first();
        if($student == null) return redirect()->route('parent.login')->withErrors(['message' => 'رقم الهوية غير موجود']);
        //  جلب أحدث عملية تسميع (منطقة التوقف الحالية)
        // رتبنا حسب الـ id لضمان الحصول على آخر إدخال فعلي حتى لو تساوت التواريخ
        $lastMemo = DB::table('student_daily_memorizations')
            ->where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        //  جلب تفاصيل حفظ السور (القائمة السفلية) مع حساب الأوزان
        $memorizedSurahs = DB::table('student_daily_memorizations')
            ->select('sura_name', DB::raw('MIN(verses_from) as min_v'), DB::raw('MAX(verses_to) as max_v'))
            ->where('student_id', $student->id)
            ->groupBy('sura_name')
            ->get()
            ->map(function($item) {
                $surahInfo = DB::table('surahs')->where('name_ar', $item->sura_name)->first();

                $totalVerses = $surahInfo->verses_count ?? 1;
                $pagesInSurah = $surahInfo->pages_count ?? ($totalVerses / 12);

                // حساب كم حفظ من آيات هذه السورة
                $actualMemorizedVerses = ($item->max_v - $item->min_v) + 1;

                // حساب الصفحات المكافئة لهذه السورة
                $item->equivalent_pages = ($actualMemorizedVerses / $totalVerses) * $pagesInSurah;
                $item->total_verses = $totalVerses;
                $item->is_completed = ($item->max_v >= $totalVerses && $item->min_v <= 1);

                return $item;
            });

            //  الحسابات الإجمالية بناءً على الصفحات
            $totalSavedPages = $memorizedSurahs->sum('equivalent_pages');

            // عدد الأجزاء (كل 20.13 صفحة تعادل جزءاً في مصحف المدينة)
            $completedPartsCount = round($totalSavedPages / 20.13, 2);

            // النسبة المئوية من كامل المصحف (604 صفحة)
            $percentage = ($totalSavedPages / 604) * 100;

            // قيود الحدود القصوى
            $completedPartsCount = min($completedPartsCount, 30);
            $percentage = min($percentage, 100);

            $teacher = $student->groups->first() ? $student->groups->first()->teacher : null;
            $age = $student->date_of_birth ? Carbon::parse($student->date_of_birth)->age : 0;

            //  تحديد مسار الحفظ (من الفاتحة أم من الناس)
            // نجلب أول عملية حفظ سجلت للطالب تاريخياً
            $firstMemo = DB::table('student_daily_memorizations')
                ->where('student_id', $student->id)
                ->orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->first();

            // إذا كانت أول سورة هي الناس أو رقمها 114 (أو ضمن جزء عم)، نعتبره بدأ من الناس
            $startFromEnd = false;
            if ($firstMemo) {
                $firstSurah = DB::table('surahs')->where('name_ar', $firstMemo->sura_name)->first();
                // إذا بدأ من سورة رقمها أكبر من 78 (جزء عم) نعتبر المسار من الناس
                if ($firstSurah && $firstSurah->number >= 78) {
                    $startFromEnd = true;
                }
            }

            return view('parents.index', compact(
                'student', 'age', 'completedPartsCount', 'percentage',
                'lastMemo', 'teacher', 'memorizedSurahs', 'startFromEnd'
            ));
        }
}
