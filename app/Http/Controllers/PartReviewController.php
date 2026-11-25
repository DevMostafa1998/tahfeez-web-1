<?php

namespace App\Http\Controllers;

use App\BusinessLogic\PartReviewLogic;
use App\Models\PartReviewRequest;
use Illuminate\Support\Facades\Auth;

class PartReviewController extends Controller
{
    protected $logic;

    public function __construct(PartReviewLogic $logic)
    {
        $this->logic = $logic;
    }

    // تأكيد المحفظ بانتهاء المراجعة → إشعار مسؤول المركز
    public function complete($id)
    {
        $review = PartReviewRequest::with('student')->findOrFail($id);

        if ($review->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'تمت معالجة هذا الطلب مسبقاً'], 422);
        }

        $user = Auth::user();
        $isAdmin = $user->is_admin == 1 || $user->is_admin_rouls == 1;
        $isOwnerKeeper = $review->keeper_id && (int) $review->keeper_id === (int) $user->id;

        if (!$isAdmin && !$isOwnerKeeper) {
            return response()->json(['success' => false, 'message' => 'غير مصرح لك بتأكيد هذه المراجعة'], 403);
        }

        $this->logic->completeReview($review);

        $next = PartReviewRequest::where('student_id', $review->student_id)
            ->where('status', 'pending')
            ->orderBy('milestone')
            ->first(['id', 'milestone']);

        $nextPayload = $next ? [
            'id'        => $next->id,
            'milestone' => $next->milestone,
            'range'     => $next->range_label,
        ] : null;

        $remaining = PartReviewRequest::where('student_id', $review->student_id)
            ->where('status', 'pending')
            ->count();

        return response()->json([
            'success'      => true,
            'message'      => 'تم تأكيد انتهاء المراجعة وإشعار مسؤول المركز',
            'next_review'  => $nextPayload,
            'remaining'    => $remaining,
        ]);
    }
}
