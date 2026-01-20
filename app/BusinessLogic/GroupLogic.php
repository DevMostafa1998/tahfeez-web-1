<?php

namespace App\BusinessLogic;

use App\Models\Group;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GroupLogic
{
    /**
     * جلب البيانات اللازمة لصفحة الفهرس
     */
    public function getIndexData()
    {
        return [
            'groups' => Group::withCount('students')
                ->whereNull('deleted_at')
                ->orderBy('creation_at', 'desc')
                ->get(),

            'teachers' => DB::table('user')
                ->select('id', 'full_name')
                ->where('is_admin', 0)
                ->get(),

            'availableStudents' => Student::whereDoesntHave('groups')->get(),
        ];
    }

    /**
     * تخزين مجموعة جديدة
     */
    public function storeGroup(array $data)
    {
        $groupData = [
            'GroupName'   => $data['GroupName'],
            'UserId'      => $data['UserId'],
            'creation_at' => Carbon::now(),
            'creation_by' => Auth::user()->id,
        ];

        return Group::create($groupData);
    }

    /**
     * تحديث بيانات المجموعة
     */
    public function updateGroup($id, array $data)
    {
        return DB::table('group')->where('id', $id)->update([
            'GroupName'  => $data['GroupName'],
            'UserId'     => $data['UserId'],
            'updated_at' => Carbon::now(),
            'updated_by' => Auth::user()->id,
        ]);
    }

    /**
     * حذف مجموعة (Soft Delete)
     */
    public function deleteGroup($id)
    {
        $group = Group::findOrFail($id);

        $group->update([
            'deleted_by' => Auth::user()->id,
            'deleted_at' => now(),
        ]);

        return $group->delete();
    }
}
