<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentGroup;
use App\Models\StudentDailyMemorization;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use Illuminate\Support\Facades\Log;

class MemorizationApiController extends Controller
{
    /**
     * جلب المجموعات والطلاب التابعين للمحفظ الحالي
     */

    public function syncTeacherData(Request $request)
    {
        $data = $request->user()->groups()
            ->with('students:id,full_name,id_number')
            ->get()
            ->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->GroupName,
                    'students' => $group->students
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
    /**
     * استقبال سجلات التسميع من الجوال ورفعها للسيرفر
     */
    public function syncUp(Request $request)
    {
        Log::info('بيانات المزامنة القادمة:', $request->all());

        $request->validate([
            'records' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->records as $record) {
                StudentDailyMemorization::create([
                    'student_id'  => $record['student_id'],
                    'sura_name'   => $record['sura_name'],
                    'verses_from' => $record['verses_from'],
                    'verses_to'   => $record['verses_to'],
                    'date'        => $record['date'],
                    'note'        => $record['note'] ?? null,
                ]);

                DB::table('student_attendances')->updateOrInsert(
                    [
                        'student_id'      => $record['student_id'],
                        'attendance_date' => $record['date'],
                    ],
                    [
                        'status'      => 'حاضر',
                        'recorded_by' => 'نظام التسميع',
                        'notes'       => 'تسجيل تلقائي عند التسميع',
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]
                );
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'تم الحفظ بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ في المزامنة: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
