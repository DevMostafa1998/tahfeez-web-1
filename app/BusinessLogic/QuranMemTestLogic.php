<?php

namespace App\BusinessLogic;

use App\Models\QuranMemTest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuranMemTestLogic
{
    /**
     * جلب جميع الاختبارات مع بيانات الطلاب
     */
    public function getAllTests()
    {
        return QuranMemTest::with('student')->get();
    }

    /**
     * تخزين اختبار جديد في قاعدة البيانات
     */
    public function storeTest(array $data)
    {
        return QuranMemTest::create($data);
    }

    /**
     * تحديث بيانات اختبار موجود
     */
    public function updateTest($id, array $data)
    {
        $test = QuranMemTest::findOrFail($id);
        $test->update($data);
        $test->update([
            'updated_by' => Auth::user()->id,
            'updated_at' => now(),
        ]);
        return $test;
    }

    /**
     * حذف سجل اختبار
     */
    public function deleteTest($id)
    {
        $test = QuranMemTest::findOrFail($id);
        $test->update([
            'deleted_by' => Auth::user()->id,
            'deleted_at' => now(),
        ]);
        return $test->delete();
    }
}
