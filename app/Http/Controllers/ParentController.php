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

        $student = Student::where('id_number', $id_number)->firstOrFail();

        // 1. تحديد اتجاه الحفظ (أول تسميع للطالب)
        $firstMemo = DB::table('student_daily_memorizations')
            ->where('student_id', $student->id)
            ->orderBy('date', 'asc')
            ->first();

        $startFromEnd = false;
        if ($firstMemo) {
            $firstSurah = DB::table('surahs')->where('name_ar', $firstMemo->sura_name)->first();
            if ($firstSurah && $firstSurah->number > 100) {
                $startFromEnd = true;
            }
        }

        // 2. جلب قائمة أرقام الأجزاء المنجزة وترتيبها
        $passedJuzs = DB::table('student_daily_memorizations')
            ->join('surahs', 'student_daily_memorizations.sura_name', '=', 'surahs.name_ar')
            ->where('student_daily_memorizations.student_id', $student->id)
            ->distinct()
            ->orderBy('surahs.juz_number', 'asc')
            ->pluck('surahs.juz_number')
            ->toArray();

        // حساب العدد والنسبة المئوية
        $completedPartsCount = count($passedJuzs);
        $percentage = ($completedPartsCount / 30) * 100;

        // 3. كويري السور الحالية وتفاصيل الإنجاز لكل سورة
        $memorizedSurahs = DB::table('student_daily_memorizations')
            ->select('sura_name', DB::raw('MIN(verses_from) as min_v'), DB::raw('MAX(verses_to) as max_v'))
            ->where('student_id', $student->id)
            ->groupBy('sura_name')
            ->get()
            ->map(function($item) {
                $surahInfo = DB::table('surahs')->where('name_ar', $item->sura_name)->first();
                $item->total_verses = $surahInfo->verses_count ?? 0;
                $item->is_completed = ($item->max_v >= $item->total_verses && $item->min_v <= 1);
                return $item;
            });

        // 4. جلب باقي البيانات (المحفظ، العمر، آخر تسميع)
        $lastMemo = DB::table('student_daily_memorizations')
            ->where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->first();

        $teacher = $student->groups->first() ? $student->groups->first()->teacher : null;
        $age = $student->date_of_birth ? Carbon::parse($student->date_of_birth)->age : 0;

        // تمرير المتغيرات إلى الـ View
        return view('parents.index', compact(
            'student',
            'age',
            'completedPartsCount',
            'percentage',
            'passedJuzs', // مصفوفة الأجزاء لاستخدامها في الخريطة
            'lastMemo',
            'teacher',
            'memorizedSurahs',
            'startFromEnd'
        ));
    }
}
