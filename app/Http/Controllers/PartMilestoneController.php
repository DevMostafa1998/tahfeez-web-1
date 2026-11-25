<?php

namespace App\Http\Controllers;

use App\BusinessLogic\PartReviewLogic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartMilestoneController extends Controller
{
    public function edit()
    {
        $milestones = PartReviewLogic::milestoneList();
        return view('settings.part_milestones', compact('milestones'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'milestones'   => 'required|array|min:1',
            'milestones.*' => 'integer|min:1|max:30',
        ]);

        $list = array_values(array_unique(array_map('intval', $request->input('milestones'))));
        sort($list);

        DB::table('settings')->updateOrInsert(
            ['key' => 'part_review_milestones'],
            ['value' => json_encode($list), 'updated_by' => auth()->id(), 'updated_at' => now(), 'created_at' => now()]
        );

        return back()->with('success', 'تم حفظ مراحل مراجعة الأجزاء بنجاح');
    }
}
