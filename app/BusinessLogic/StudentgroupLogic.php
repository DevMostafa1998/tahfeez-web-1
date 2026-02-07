<?php

namespace App\BusinessLogic;

use App\Models\Group;
use App\Models\Student;
// use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentgroupLogic
{
    /**
     * جلب البيانات اللازمة لعرض مجموعات الطلاب
     */
    // داخل ملف StudentgroupLogic.php

    public function getIndexData()
    {
        return [
            'groups' => Group::with(['students', 'teacher']) // تأكد من وجود علاقة teacher في مودل Group
                ->withCount('students')
                ->whereNull('deleted_at')
                ->orderBy('creation_at', 'desc')
                ->get(),

            'teachers' => DB::table('user')
                ->select('id', 'full_name', 'gender') // أضفنا الجنس هنا
                ->where('is_admin', 0)
                ->get(),

            // جلب جميع الطلاب غير المسجلين حالياً (سنفلترهم في الـ Blade حسب الجنس)
            'availableStudents' => Student::whereDoesntHave('groups')->get(),
        ];
    }

    /**
     * مزامنة الطلاب مع مجموعة معينة
     */
    public function syncStudentsToGroup($groupId, array $studentIds)
    {
        $group = Group::findOrFail($groupId);

        // استخدام sync لمسح القديم وإضافة الجديد أو الإبقاء على الموجود
        return $group->students()->sync($studentIds);
    }
}
