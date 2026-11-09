<?php

namespace App\BusinessLogic;

use App\Models\QuranMemTest;
use Illuminate\Support\Facades\Auth;

class QuranMemTestLogic
{
    /**
     * جلب جميع الاختبارات مع بيانات الطلاب
     */
    public function getAllTests()
    {
        return QuranMemTest::with('student')->whereNull('deleted_at')->get();
    }

    /**
     * تخزين اختبار جديد في قاعدة البيانات
     */
    public function storeTest(array $data)
    {
        $data['creation_by'] = Auth::id();
        return QuranMemTest::create($data);
    }

    /**
     * تحديث بيانات اختبار موجود
     */
    public function updateTest($id, array $data)
    {
        $test = QuranMemTest::findOrFail($id);

        $data['updated_by'] = Auth::id();
        $data['updated_at'] = now();

        $test->update($data);

        return $test;
    }

    /**
     * حذف سجل اختبار
     */
    public function deleteTest($id)
    {
        $test = QuranMemTest::findOrFail($id);

        if (in_array('deleted_by', $test->getFillable())) {
            $test->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => now(),
            ]);
        }

        return $test->delete();
    }
}
