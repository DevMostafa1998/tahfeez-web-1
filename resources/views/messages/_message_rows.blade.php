@php
    $currentUserId = $currentUser->id;
    $prevDay = $prevMessage ? $prevMessage->created_at->toDateString() : null;
    $firstUnreadId = $firstUnreadId ?? null;
@endphp
@foreach($messages as $msg)
    @php
        $isMe = $msg->sender_id == $currentUserId;
        $day = $msg->created_at->toDateString();
        $showSeparator = $day !== $prevDay;
        $prevDay = $day;
        $canEdit = $isMe && now()->lessThanOrEqualTo($msg->created_at->copy()->addMinutes(10));
        $senderName = $isMe ? 'أنت' : $otherUser->full_name;
        $dayLabel = \Carbon\Carbon::parse($day)->isToday()
            ? 'اليوم'
            : (\Carbon\Carbon::parse($day)->isYesterday() ? 'أمث' : '');
        if (!$dayLabel) {
            $dayLabel = \Carbon\Carbon::parse($day)->translatedFormat('l، j F Y');
        }
    @endphp
    @if($showSeparator)
        <div class="chat-date-separator">
            <span>{{ $dayLabel }}</span>
        </div>
    @endif
    @if($firstUnreadId && $msg->id == $firstUnreadId && !$isMe)
        <div class="chat-unread-divider"><span>رسائل غير مقروءة</span></div>
    @endif
    <div class="message-row {{ $isMe ? 'message-me' : 'message-other' }}" data-id="{{ $msg->id }}">
        <div class="message-bubble">
            <div class="message-sender">{{ $senderName }}</div>
            <div class="message-text">{{ $msg->body }}@if($msg->edited_at) <span class="edited-label">(معدّلة)</span>@endif</div>
            <div class="message-meta">
                <span class="message-time">{{ $msg->created_at->format('h:i A') }}</span>
                @if($isMe)
                    <i class="bi {{ $msg->is_read ? 'bi-check2-all' : 'bi-check2' }} message-tick" style="{{ $msg->is_read ? 'color:#a5f3fc;' : '' }}"></i>
                @endif
                @if($canEdit)
                    <button type="button" class="btn btn-link btn-sm edit-message-btn p-0 ms-1" title="تعديل الرسالة" data-id="{{ $msg->id }}">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
@endforeach
