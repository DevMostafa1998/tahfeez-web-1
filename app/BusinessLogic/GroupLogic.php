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
        $user = Auth::user();

        //  معرف المركز المحدد من الـ Session
        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;
        $selectedGender = session('selected_gender');
        // 1. استعلام المجموعات
        $groupsQuery = Group::withCount('students')
            ->whereNull('deleted_at')
            ->orderBy('creation_at', 'desc');

        //  فلترة المجموعات حسب المركز المختار
        if ($selectedCenterId) {
            $groupsQuery->where('center_id', $selectedCenterId);
        }

        if ($user->is_admin == 0) {
            $groupsQuery->where('UserId', $user->id);
        }
        if ($selectedGender) {
            $groupsQuery->whereHas('teacher', function ($q) use ($selectedGender) {
                $q->where('gender', $selectedGender);
            });
        }
        // 2. استعلام المعلمين/المحفظين
        $teachersQuery = DB::table('user')
            ->select('id', 'full_name')
            ->where('is_admin', 0)
            ->whereNull('deleted_at');

        //  فلترة المعلمين حسب المركز المختار
        if ($selectedCenterId) {
            $teachersQuery->where('center_id', $selectedCenterId);
        }
        if ($selectedGender) {
            $teachersQuery->where('gender', $selectedGender);
        }
        // 3. استعلام الطلاب المتاحين
        $availableStudentsQuery = Student::whereDoesntHave('groups');

        //  فلترة الطلاب المتاحين حسب المركز المختار
        if ($selectedCenterId) {
            $availableStudentsQuery->where('center_id', $selectedCenterId);
        }
        if ($selectedGender) {
            $availableStudentsQuery->where('gender', $selectedGender);
        }
        return [
            'groups'            => $groupsQuery->get(),
            'teachers'          => $teachersQuery->get(),
            'availableStudents' => $availableStudentsQuery->get(),
        ];
    }

    public function storeGroup(array $data)
    {
        $teacher = DB::table('user')->where('id', $data['UserId'])->first();

        $centerId = ($teacher && !empty($teacher->center_id))
            ? $teacher->center_id
            : (session()->has('selected_center_id') && session('selected_center_id') != '' ? session('selected_center_id') : null);

        $groupData = [
            'GroupName'   => $data['GroupName'],
            'UserId'      => $data['UserId'],
            'center_id'   => $centerId,
            'creation_at' => Carbon::now(),
            'creation_by' => Auth::user()->id,
        ];

        return Group::create($groupData);
    }

    public function updateGroup($id, array $data)
    {
        $teacher = DB::table('user')->where('id', $data['UserId'])->first();

        $updateData = [
            'GroupName'  => $data['GroupName'],
            'UserId'     => $data['UserId'],
            'updated_at' => Carbon::now(),
            'updated_by' => Auth::user()->id,
        ];

        if ($teacher && !empty($teacher->center_id)) {
            $updateData['center_id'] = $teacher->center_id;
        }

        return DB::table('group')->where('id', $id)->update($updateData);
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

    /**
     * جلب تفاصيل مجموعة معينة مع طلابها
     */
    public function getGroupDetails($id)
    {
        $group = Group::with('teacher')->findOrFail($id);

        $students = $group->students()
            ->with(['latestMemorization', 'memorizations'])
            ->get();

        $surahs = DB::table('surahs')->orderBy('number')->get();

        $examTypes = DB::table('lov')
            ->select('id', 'description')
            ->where('type', 'testtype')
            ->where('is_valid', 1)
            ->get();

        return [
            'group'     => $group,
            'students'  => $students,
            'surahs'    => $surahs,
            'examTypes' => $examTypes
        ];
    }
}
