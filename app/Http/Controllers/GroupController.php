<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Carbon\Carbon;
// use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\DB;
// use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. جلب المجموعات مع حساب عدد الطلاب لكل مجموعة
        $groups = Group::withCount('students')
            ->whereNull('deleted_at')
            ->orderBy('creation_at', 'desc')
            ->get();

        // 2. جلب المحفظين (المستخدمين الذين ليسوا أدمن)
        $teachers = DB::table('user')
            ->select('id', 'full_name')
            ->where('is_admin', 0)
            ->get();

        // 3. جلب الطلاب غير المسجلين في أي مجموعة لتجنب الخطأ في الـ Blade
        // هذا هو السطر الذي ينقصك في هذا الكنترولر
        $availableStudents = \App\Models\Student::whereDoesntHave('groups')->get();

        // 4. تمرير كافة المتغيرات إلى View
        return view('groups.index', compact('groups', 'teachers', 'availableStudents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'GroupName' => 'required|string|max:255',
            'UserId'    => 'required|exists:user,id,is_admin,0',
        ]);

        $data = array_merge($validated, [
            'GroupName'   => $request->GroupName,
            'UserId'      => $request->UserId, // الربط مع المستخدم الحالي
            'creation_at' => Carbon::now(),
            'creation_by' => Auth::user()->id,
        ]);

        Group::create($data);

        return redirect()->route('group.index')->with('success', 'تم إضافة المجموعة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'GroupName' => 'required|string|max:255',
            'UserId'    => 'required|exists:user,id,is_admin,0',
        ]);

        DB::table('group')->where('id', $id)->update([
            'GroupName' => $request->GroupName,
            'UserId'    => $request->UserId,
            'updated_at' => Carbon::now(),
            'updated_by' => Auth::user()->id,
        ]);

        return redirect()->route('group.index')->with('success', 'تم تحديث المجموعة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $group = Group::findOrFail($id);
        $group->update([
            'deleted_by' => Auth::user()->id,
            'deleted_at' => now(),
        ]);
        $group->delete();
        return redirect()->route('group.index')->with('success', 'تم حذف المجموعة بنجاح');
    }
}
