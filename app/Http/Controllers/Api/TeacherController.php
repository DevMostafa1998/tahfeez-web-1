<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    /**
     * جلب بيانات المعلم والمجموعات والطلاب التابعين له
     */
    public function getTeacherData(Request $request)
    {
        $user = $request->user();

        $data = $user->groups()->with('students:id,full_name,id_number')->get()->map(function ($group) {
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
     * استقبال بيانات التسميع القادمة من التطبيق ومزامنتها
     * تدعم هذه الدالة إرسال مصفوفة من السجلات (Bulk Insert)
     */
    public function syncMemorizations(Request $request)
    {
        $request->validate([
            'memorizations' => 'required|array',
            'memorizations.*.student_id' => 'required|integer',
            'memorizations.*.recitation_date' => 'required',
            'memorizations.*.surah_name' => 'required|string',
            'memorizations.*.from_verse' => 'required|integer',
            'memorizations.*.to_verse' => 'required|integer',
        ]);

        $data = $request->input('memorizations');

        try {
            DB::beginTransaction();

            foreach ($data as $item) {
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
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تمت المزامنة بنجاح'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء المزامنة: ' . $e->getMessage()
            ], 500);
        }
    }
}
