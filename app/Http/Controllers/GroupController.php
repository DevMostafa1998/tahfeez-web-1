<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Carbon\Carbon;
// use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\DB;
// use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\BusinessLogic\GroupLogic;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $groupLogic;

    public function __construct(GroupLogic $groupLogic)
    {
        $this->groupLogic = $groupLogic;
    }
    public function index()
    {
        $userId = Auth::id();
        // تمرير المعرف إلى دالة getIndexData
        $data = $this->groupLogic->getIndexData($userId);
        return view('groups.index', $data);
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
        $validated = $request->validate([
            'GroupName' => 'required|string|max:255',
            'UserId'    => 'required|exists:user,id,is_admin,0',
        ]);

        $group = $this->groupLogic->storeGroup($validated);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المجموعة بنجاح',
                'group' => [
                    'id' => $group->id,
                    'GroupName' => $group->GroupName,
                    'students_count' => 0, // المجموعة الجديدة تكون فارغة
                    'created_at' => $group->creation_at->format('Y-m-d')
                ]
            ]);
        }
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
        $validated = $request->validate([
            'GroupName' => 'required|string|max:255',
            'UserId'    => 'required|exists:user,id,is_admin,0',
        ]);

        $this->groupLogic->updateGroup($id, $validated);

        return redirect()->route('group.index')->with('success', 'تم تحديث المجموعة بنجاح');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->groupLogic->deleteGroup($id);
        return redirect()->route('group.index')->with('success', 'تم حذف المجموعة بنجاح');
    }
}
