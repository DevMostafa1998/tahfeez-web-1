<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BusinessLogic\MemorizationLogic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class MemorizationController extends Controller
{
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
            'date'     => 'required|date',
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
                    'status'  => 'error',
                    'message' => 'ليس لديك صلاحية لعرض هذه المجموعة'
                ], 403);
            }
        }

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
            'status'   => 'success',
            'data'     => $students,
            'date'     => $date,
            'group_id' => $groupId
        ]);
    }

    public function store(Request $request)
    {
        $isMulti = ($request->input('is_multi_surah') == '1' || $request->input('is_multi_surah') == 'on');
        $isMemorized = $request->is_memorized == 1;
        $isPartial = ($request->input('is_last_surah_partial') == '1' || $request->input('is_last_surah_partial') == 'on');

        if ($isMulti && $request->has('surah_ids')) {
            $rawSurahs = is_array($request->surah_ids) ? $request->surah_ids : array_filter(explode(',', $request->surah_ids));

            $cleanSurahs = array_map(function($rawName) {
                return trim(preg_replace('/^\s*\d+\s*\.\s*/', '', $rawName));
            }, $rawSurahs);

            $request->merge(['surah_ids' => $cleanSurahs]);
        }

        $maxVerses = 6236;
        if ($isMemorized) {
            if (!$isMulti && $request->filled('sura_name')) {
                $surah = DB::table('surahs')->where('name_ar', $request->sura_name)->first();
                if ($surah) {
                    $maxVerses = $surah->verses_count;
                }
            } elseif ($isMulti && !empty($request->input('surah_ids'))) {
                $surahNames = $request->input('surah_ids');
                $lastSurahName = end($surahNames);
                $surah = DB::table('surahs')->where('name_ar', $lastSurahName)->first();
                if ($surah) {
                    $maxVerses = $surah->verses_count;
                }
            }
        }

        $rules = [
            'student_id'   => 'required|exists:student,id',
            'date'         => 'required|date',
            'is_memorized' => 'required|in:0,1',
            'note'         => 'nullable|string',
            'examType_id'  => 'required_if:is_memorized,1|nullable|integer',
        ];

        if ($isMemorized) {
            if ($isMulti) {
                $rules['surah_ids'] = 'required|array|min:1';
                if ($isPartial) {
                    $rules['last_sura_from'] = "required|integer|min:1|max:{$maxVerses}";
                    $rules['last_sura_to']   = "required|integer|max:{$maxVerses}|gte:last_sura_from";
                }
            } else {
                $rules['sura_name']   = 'required|string|max:255';
                $rules['verses_from'] = "required|integer|min:0|max:{$maxVerses}";
                $rules['verses_to']   = "required|integer|max:{$maxVerses}|gte:verses_from";
            }
        }

        $validated = $request->validate($rules, [
            'sura_name.required'       => 'حقل اسم السورة مطلوب عند اختيار سَمَّعَ.',
            'surah_ids.required'       => 'يجب اختيار سورة واحدة على الأقل في التسميع المتعدد.',
            'verses_from.required'     => 'بداية الآيات مطلوبة عند اختيار سَمَّعَ.',
            'verses_to.required'       => 'نهاية الآيات مطلوبة عند اختيار سَمَّعَ.',
            'examType_id.required_if'  => 'يجب تحديد نوع التسميع أولاً.',
            'verses_to.max'            => "خطأ: سورة {$request->sura_name} تحتوي على {$maxVerses} آية فقط.",
            'verses_to.gte'            => 'نهاية الآيات لا يمكن أن تكون أقل من بدايتها.',
            'last_sura_from.required'  => 'بداية آيات السورة الأخيرة مطلوبة.',
            'last_sura_to.required'    => 'نهاية آيات السورة الأخيرة مطلوبة.',
            'last_sura_to.max'         => "خطأ: السورة الأخيرة تحتوي على {$maxVerses} آية فقط.",
            'last_sura_to.gte'         => 'نهاية آيات السورة الأخيرة لا يمكن أن تكون أقل من بدايتها.',
        ]);

        try {
            DB::beginTransaction();

            // الحالة الأولى: الطالب لم يسمع
            if ($validated['is_memorized'] == '0') {

                $lastMemorization = DB::table('student_daily_memorizations')
                    ->where('student_id', $validated['student_id'])
                    ->whereNotNull('examType_id')
                    ->where('sura_name', '!=', '---')
                    ->where('sura_name', '!=', 'لم يسمع')
                    ->orderBy('date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastMemorization) {
                    $oldSurah = $lastMemorization->sura_name;
                    $oldVerseFrom = $lastMemorization->verses_from;
                    $oldVerseTo = $lastMemorization->verses_to;
                } else {
                    $oldSurah = '---';
                    $oldVerseFrom = 0;
                    $oldVerseTo = 0;
                }

                $this->memorizationLogic->storeDailyMemorization([
                    'student_id'   => $validated['student_id'],
                    'date'         => $validated['date'],
                    'sura_name'    => $oldSurah,
                    'verses_from'  => (int)$oldVerseFrom,
                    'verses_to'    => (int)$oldVerseTo,
                    'examType_id'  => null,
                    'note'         => $validated['note'] ?? 'الطالب لم يقم بالتسميع اليوم',
                ]);

            // الحالة الثانية: الطالب سمع (أكثر من سورة)
            } elseif ($isMulti) {
                $surahNames = $validated['surah_ids'];
                $totalSurahs = count($surahNames);

                foreach ($surahNames as $index => $cleanName) {
                    $isLastSurah = ($index === $totalSurahs - 1);
                    $vFrom = 1;
                    $vTo = 1;

                    $surahData = DB::table('surahs')->where('name_ar', $cleanName)->first();
                    if ($surahData) {
                        $vTo = $surahData->verses_count;
                    }

                    if ($isLastSurah && $isPartial) {
                        $vFrom = (int)$request->input('last_sura_from', 1);
                        $vTo = (int)$request->input('last_sura_to', $vTo);
                    }

                    $existing = DB::table('student_daily_memorizations')
                        ->where('student_id', $validated['student_id'])
                        ->where('sura_name', $cleanName)
                        ->first();

                    if ($existing) {
                        DB::table('student_daily_memorizations')
                            ->where('id', $existing->id)
                            ->update([
                                'verses_to'   => max((int)$existing->verses_to, $vTo),
                                'examType_id' => $validated['examType_id'], // حفظ النوع الفعلي المختار (تسميع، مراجعة، سرد)
                                'note'        => $validated['note'],
                                'updated_at'  => now()
                            ]);
                    } else {
                        $this->memorizationLogic->storeDailyMemorization([
                            'student_id'   => $validated['student_id'],
                            'date'         => $validated['date'],
                            'sura_name'    => $cleanName,
                            'verses_from'  => $vFrom,
                            'verses_to'    => $vTo,
                            'examType_id'  => $validated['examType_id'], // حفظ النوع الفعلي المختار
                            'note'         => $validated['note'],
                        ]);
                    }
                }

            // الحالة الثالثة: الطالب سمع (سورة واحدة)
            } else {
                $existing = DB::table('student_daily_memorizations')
                    ->where('student_id', $validated['student_id'])
                    ->where('sura_name', $validated['sura_name'])
                    ->first();

                if ($existing) {
                    DB::table('student_daily_memorizations')
                        ->where('id', $existing->id)
                        ->update([
                            'verses_to'   => (int)$validated['verses_to'], // تحديث آية النهاية فقط
                            'examType_id' => $validated['examType_id'],   // تخزين نوع التسميع الصحيح المختار
                            'note'        => $validated['note'],
                            'updated_at'  => now()
                        ]);
                } else {
                    $this->memorizationLogic->storeDailyMemorization([
                        'student_id'   => $validated['student_id'],
                        'date'         => $validated['date'],
                        'sura_name'    => $validated['sura_name'],
                        'verses_from'  => $validated['verses_from'],
                        'verses_to'    => $validated['verses_to'],
                        'examType_id'  => $validated['examType_id'],
                        'note'         => $validated['note'],
                    ]);
                }
            }

            DB::commit();

            $responseSuraText = '';
            $responseVerseText = '';

            if ($validated['is_memorized'] == '1') {
                if ($isMulti) {
                    $surahNames = $validated['surah_ids'];
                    $responseSuraText = end($surahNames);

                    if ($isPartial) {
                        $responseVerseText = $request->input('last_sura_to');
                    } else {
                        $lastSurahData = DB::table('surahs')->where('name_ar', $responseSuraText)->first();
                        $responseVerseText = $lastSurahData ? $lastSurahData->verses_count : 'كاملة';
                    }
                } else {
                    $responseSuraText = $validated['sura_name'];
                    $responseVerseText = $validated['verses_to'];
                }
            } else {
                $responseSuraText = '---';
                $responseVerseText = '---';
            }

            return response()->json([
                'success'      => true,
                'message'      => 'تم تسجيل العملية بنجاح منعاً للتكرار',
                'sura_text'    => $responseSuraText,
                'verse_text'   => $responseVerseText,
                'is_memorized' => $validated['is_memorized'],
                'verses_to'    => $request->input('verses_to')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ أثناء حفظ التسميع اليومي: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ داخلي أثناء الحفظ، يرجى مراجعة سجلات النظام.'
            ], 500);
        }
    }

    public function syncBulk(Request $request)
    {
        $data = $request->input('memorizations');

        if (empty($data)) {
            Log::warning('تحذير: مصفوفة البيانات فارغة تماماً!');
            return response()->json(['error' => 'لا توجد بيانات'], 400);
        }

        $teacherId = $request->user() ? $request->user()->id : null;

        try {
            DB::beginTransaction();

            foreach ($data as $item) {
                $examTypeId = $item['examType_id'] ?? $item['test_type_id'] ?? null;
                if ($examTypeId == 0) {
                    $examTypeId = null;
                }

                DB::table('student_daily_memorizations')->insert([
                    'student_id'   => $item['student_id'],
                    'date'         => $item['recitation_date'],
                    'sura_name'    => $item['surah_name'],
                    'verses_from'  => $item['from_verse'],
                    'verses_to'    => $item['to_verse'],
                    'examType_id'  => $examTypeId,
                    'note'         => $item['notes'] ?? null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

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
            return response()->json(['success' => true, 'message' => 'تم الحفظ والمزامنة بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('فشل المزامنة النهائي: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ في قاعدة البيانات أثناء المزامنة'], 500);
        }
    }

    public function getSurahs()
    {
        $surahs = DB::table('surahs')
            ->select('id', 'name_ar', 'verses_count', 'pages_count', 'juz_number')
            ->get();

        return response()->json(['data' => $surahs]);
    }

    public function syncAttendance(Request $request)
    {
        $data = $request->input('attendance');

        if (empty($data)) {
            return response()->json(['error' => 'لا توجد بيانات حضور'], 400);
        }

        $teacherId = $request->user() ? $request->user()->id : null;

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
                        'recorded_by' => $teacherId,
                        'updated_at'  => now(),
                    ]
                );
            }
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('فشل مزامنة الحضور: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ أثناء حفظ الحضور'], 500);
        }
    }

    public function getLov(Request $request)
    {
        $type = $request->query('type');
        $query = DB::table('lov')->where('is_valid', true);

        if ($type) {
            $query->where('type', $type);
        }

        $items = $query->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data'    => $items,
        ]);
    }

    public function editStudentSurahs($student_id)
    {
        $student = DB::table('student')->where('id', $student_id)->first();
        if (!$student) {
            abort(404, 'الطالب غير موجود');
        }

        $latestGroup = DB::table('student_group')
            ->where('student_id', $student_id)
            ->orderBy('creation_at', 'desc')
            ->first();

        $student->group_id = $latestGroup ? $latestGroup->group_id : 1;

        $records = DB::table('student_daily_memorizations')
            ->where('student_id', $student_id)
            ->whereNotNull('examType_id')
            ->orderBy('date', 'desc')
            ->get();

        $surahs = DB::table('surahs')->orderBy('number')->get();

        return view('groups.edit_student_surahs', compact('student', 'records', 'surahs'));
    }

    public function updateRecord(Request $request, $id)
    {
        $suraName   = $request->input('sura_name');
        $versesFrom = (int) $request->input('verses_from');
        $versesTo   = (int) $request->input('verses_to');
        $examTypeId = $request->input('examType_id');

        $currentRecord = DB::table('student_daily_memorizations')->where('id', $id)->first();
        if (!$currentRecord) {
            return response()->json(['success' => false, 'message' => 'السجل غير موجود!']);
        }

        $studentId = $currentRecord->student_id;

        $surrah = DB::table('surahs')->where('name_ar', $suraName)->first();
        if ($surrah) {
            $maxVerses = $surrah->verses_count ?? $surrah->verses ?? 7;

            if ($versesFrom > $versesTo) {
                return response()->json(['success' => false, 'message' => 'خطأ: آية البدء أكبر من آية الانتهاء.']);
            }

            if ($versesTo > $maxVerses || $versesFrom > $maxVerses) {
                return response()->json(['success' => false, 'message' => "خطأ: عدد آيات سورة {$suraName} هو {$maxVerses} آية فقط!"]);
            }
        }

        $allRecords = DB::table('student_daily_memorizations')
            ->where('student_id', $studentId)
            ->where('sura_name', $suraName)
            ->get();

        if ($allRecords->count() > 1) {
            $allFromValues = $allRecords->pluck('verses_from')->push($versesFrom)->toArray();
            $allToValues   = $allRecords->pluck('verses_to')->push($versesTo)->toArray();

            $versesFrom = min($allFromValues);
            $versesTo   = max($allToValues);

            DB::table('student_daily_memorizations')
                ->where('student_id', $studentId)
                ->where('sura_name', $suraName)
                ->where('id', '!=', $id)
                ->delete();
        }

        // تحديث السجل الحالي بالقيم النهائية مع نوع التسميع
        DB::table('student_daily_memorizations')
            ->where('id', $id)
            ->update([
                'sura_name'   => $suraName,
                'verses_from' => $versesFrom,
                'verses_to'   => $versesTo,
                'examType_id' => $examTypeId ?? $currentRecord->examType_id,
                'updated_at'  => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'تم دمج السجلات المكررة للسورة بنجاح وتنظيف الجدول.',
            'final_from' => $versesFrom,
            'final_to' => $versesTo
        ]);
    }
}
