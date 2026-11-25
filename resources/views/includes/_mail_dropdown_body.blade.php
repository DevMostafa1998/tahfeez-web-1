<span class="dropdown-item dropdown-header fw-bold">
    <i class="bi bi-envelope-fill me-1"></i> الرسائل الواردة
    @if($globalUnreadMessages > 0)
        <span class="badge bg-danger rounded-pill">{{ $globalUnreadMessages }} غير مقروءة</span>
    @endif
</span>
<div class="dropdown-divider"></div>

@forelse($unreadConversations as $conv)
    @php $sender = $unreadSenders->get($conv->sender_id); @endphp
    @if($sender)
        <a href="{{ route('messages.chat', $sender->id) }}" class="dropdown-item d-flex align-items-center">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold me-2 flex-shrink-0 {{ $sender->is_admin ? 'bg-warning text-dark' : 'bg-primary text-white' }}" style="width: 38px; height: 38px; font-size: 0.85rem;">
                {{ mb_substr($sender->full_name, 0, 2) }}
            </span>
            <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-truncate" style="font-size: 0.88rem;">{{ $sender->full_name }}</span>
                    <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">{{ $conv->total }}</span>
                </div>
                <small class="text-muted text-truncate d-block" style="font-size: 0.75rem;">
                    {{ $sender->is_admin ? 'مسؤول' : 'مُحفظ' }} ·
                    {{ $conv->total }} رسالة غير مقروءة ·
                    {{ \Carbon\Carbon::parse($conv->last_at)->diffForHumans() }}
                </small>
            </div>
        </a>
    @endif
@empty
    <div class="dropdown-item text-center text-muted small py-3">
        <i class="bi bi-check2-circle text-success fs-4 d-block mb-1"></i>
        لا توجد رسائل غير مقروءة
    </div>
@endforelse

<div class="dropdown-divider"></div>
<a href="{{ route('messages.index') }}" class="dropdown-item dropdown-footer text-primary fw-bold text-center">
    <i class="bi bi-chat-dots me-1"></i> عرض كل المحادثات
</a>
