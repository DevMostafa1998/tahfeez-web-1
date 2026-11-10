<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    const PAGE_SIZE = 100;
    const OLDER_PAGE_SIZE = 50;
    const EDIT_WINDOW_MINUTES = 10;
    const DELETE_WINDOW_MINUTES = 15;

    public function index($teacherId = null)
    {
        $currentUser = auth()->user();
        $currentCenterId = session('selected_center_id');

        $usersQuery = User::where('id', '!=', $currentUser->id);

        if ($currentCenterId) {
            $usersQuery->where(function($query) use ($currentCenterId) {
                $query->where('center_id', $currentCenterId)
                      ->orWhere('is_admin', 1);
            });
        }

        $teachers = $usersQuery->get();

        $unreadSenderIds = Message::where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->pluck('sender_id');

        if ($unreadSenderIds->isNotEmpty()) {
            $extraUsers = User::whereIn('id', $unreadSenderIds)->get();
            $teachers = $teachers->merge($extraUsers)->unique('id');
        }

        $unreadCounts = Message::where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->selectRaw('sender_id, COUNT(*) as total')
            ->groupBy('sender_id')
            ->pluck('total', 'sender_id');

        foreach ($teachers as $teacher) {
            $teacher->unread_count = $unreadCounts[$teacher->id] ?? 0;
        }

        $lastMessageAt = Message::query()
            ->where(function ($q) use ($currentUser) {
                $q->where('sender_id', $currentUser->id)
                  ->orWhere('receiver_id', $currentUser->id);
            })
            ->selectRaw('CASE WHEN sender_id = ' . (int) $currentUser->id . ' THEN receiver_id ELSE sender_id END AS other_id')
            ->selectRaw('MAX(created_at) AS last_at')
            ->groupByRaw('CASE WHEN sender_id = ' . (int) $currentUser->id . ' THEN receiver_id ELSE sender_id END')
            ->pluck('last_at', 'other_id');

        $teachers = $teachers->sort(function ($a, $b) use ($lastMessageAt) {
            $ta = $lastMessageAt[$a->id] ?? null;
            $tb = $lastMessageAt[$b->id] ?? null;
            if ($ta && $tb) {
                return strcmp($tb, $ta);
            }
            if ($ta) {
                return -1;
            }
            if ($tb) {
                return 1;
            }
            if ((bool) $a->is_admin !== (bool) $b->is_admin) {
                return $b->is_admin <=> $a->is_admin;
            }
            return strcmp((string) $a->full_name, (string) $b->full_name);
        })->values();

        $activeTeacher = null;
        $messages = collect();
        $hasMoreMessages = false;

        //  جلب آخر 100 رسالة فقط عند فتح المحادثة
        $firstUnreadId = null;
        if ($teacherId) {
            $activeTeacher = User::findOrFail($teacherId);

            $baseQuery = $this->conversationQuery($currentUser->id, (int) $teacherId);
            $hasMoreMessages = (clone $baseQuery)->count() > self::PAGE_SIZE;

            $messages = (clone $baseQuery)
                ->orderByDesc('id')
                ->limit(self::PAGE_SIZE)
                ->get()
                ->reverse()
                ->values();

            $firstUnreadId = $messages->first(
                fn ($m) => $m->sender_id == $teacherId && !$m->is_read
            )?->id;

            $this->markAsRead($teacherId);

            $teachers->firstWhere('id', (int) $teacherId)?->setAttribute('unread_count', 0);
        }

        return view('messages.index', compact('teachers', 'activeTeacher', 'messages', 'hasMoreMessages', 'firstUnreadId'));
    }

    public function chatData(Request $request, $teacherId)
    {
        $currentUser = auth()->user();
        $teacher = User::findOrFail($teacherId);

        $before = $request->integer('before');
        $after = $request->integer('after');
        $perPage = $before ? self::OLDER_PAGE_SIZE : self::PAGE_SIZE;

        $baseQuery = $this->conversationQuery($currentUser->id, (int) $teacherId);

        if ($before) {
            $baseQuery->where('id', '<', $before);
        }

        if ($after) {
            $baseQuery->where('id', '>', $after);
        }

        $total = (clone $baseQuery)->count();
        $hasMore = $after ? false : ($before ? $total > $perPage : $total > self::PAGE_SIZE);

        $messages = (clone $baseQuery)
            ->orderByDesc('id')
            ->limit($perPage)
            ->get()
            ->reverse()
            ->values();

        $firstUnreadId = null;
        if (!$before && !$after) {
            $firstUnreadId = $messages->first(
                fn ($m) => $m->sender_id == $teacher->id && !$m->is_read
            )?->id;
        }

        if (!$before && (!$after || $messages->isNotEmpty())) {
            $this->markAsRead($teacherId);
            Message::where('receiver_id', $currentUser->id)
                ->where('sender_id', $teacher->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        $prevMessage = null;
        if ($messages->isNotEmpty()) {
            $prevMessage = (clone $this->conversationQuery($currentUser->id, (int) $teacherId))
                ->where('id', '<', $messages->first()->id)
                ->orderByDesc('id')
                ->first();
        }

        return response()->json([
            'success' => true,
            'teacher' => [
                'id' => $teacher->id,
                'full_name' => $teacher->full_name,
                'phone_number' => $teacher->phone_number,
                'is_admin' => (bool) $teacher->is_admin,
                'role_label' => $teacher->is_admin ? 'مدير النظام' : 'مُحفظ معتمد',
            ],
            'html' => view('messages._message_rows', [
                'messages' => $messages,
                'currentUser' => $currentUser,
                'otherUser' => $teacher,
                'prevMessage' => $prevMessage,
                'firstUnreadId' => $firstUnreadId,
            ])->render(),
            'has_more' => $hasMore,
            'oldest_id' => (!$after && $messages->isNotEmpty()) ? $messages->first()->id : null,
        ]);
    }

    // إرسال رسالة جديدة
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:user,id',
            'body' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'body' => $request->body,
            'center_id' => session('selected_center_id') ?? auth()->user()->center_id,
            'is_read' => false,
        ]);

        if ($request->ajax()) {
            $prevMessage = Message::where('id', '<', $message->id)
                ->where(function($q) use ($request) {
                    $q->where(function($q2) {
                        $q2->where('sender_id', Auth::id())->where('receiver_id', $request->receiver_id);
                    })->orWhere(function($q2) {
                        $q2->where('sender_id', $request->receiver_id)->where('receiver_id', Auth::id());
                    });
                })->orderByDesc('id')->first();

            return response()->json([
                'success' => true,
                'message' => $message,
                'id' => $message->id,
                'time' => $message->created_at->format('h:i A'),
                'html' => view('messages._message_rows', [
                    'messages' => collect([$message]),
                    'currentUser' => auth()->user(),
                    'otherUser' => User::find($request->receiver_id),
                    'prevMessage' => $prevMessage,
                ])->render(),
            ]);
        }

        return redirect()->route('messages.chat', $request->receiver_id);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['body' => 'required|string|max:1000']);

        $message = Message::findOrFail($id);

        if ($message->sender_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'غير مسموح بتعديل هذه الرسالة'], 403);
        }

        if (now()->greaterThan($message->created_at->copy()->addMinutes(self::EDIT_WINDOW_MINUTES))) {
            return response()->json(['success' => false, 'message' => 'انتهت مهلة التعديل (10 دقائق)'], 422);
        }

        $message->update([
            'body' => $request->body,
            'edited_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تعديل الرسالة بنجاح',
            'id' => $message->id,
            'body' => $message->body,
            'time' => $message->created_at->format('h:i A'),
        ]);
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);

        if ($message->sender_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'غير مسموح بحذف هذه الرسالة'], 403);
        }

        if (now()->greaterThan($message->created_at->copy()->addMinutes(self::DELETE_WINDOW_MINUTES))) {
            return response()->json(['success' => false, 'message' => 'انتهت مهلة الحذف (15 دقيقة)'], 422);
        }

        $message->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الرسالة']);
    }

    private function conversationQuery($userId, $otherId)
    {
        return Message::where(function($q) use ($userId, $otherId) {
            $q->where('sender_id', $userId)->where('receiver_id', $otherId);
        })->orWhere(function($q) use ($userId, $otherId) {
            $q->where('sender_id', $otherId)->where('receiver_id', $userId);
        });
    }

    private function markAsRead($teacherId)
    {
        Message::where('sender_id', $teacherId)
            ->where('receiver_id', auth()->id())
            ->update(['is_read' => true]);
    }
}
