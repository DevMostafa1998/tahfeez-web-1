@php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
<span class="dropdown-item dropdown-header fw-bold">
    {{ $unreadCount }} تنبيهات غير مقروءة
</span>
<div class="dropdown-divider"></div>

@forelse(auth()->user()->unreadNotifications->take(5) as $notification)
    <a href="{{ route('notifications.read', $notification->id) }}" class="dropdown-item">
        <i class="bi bi-info-circle-fill me-2 text-primary"></i>
        <span class="text-truncate" style="max-width: 150px; display: inline-block;">
            {{ $notification->data['title'] ?? 'تنبيه جديد' }}
        </span>
        <span class="float-end text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
    </a>
@empty
    <div class="dropdown-item text-center text-muted small">لا توجد تنبيهات جديدة</div>
@endforelse

<div class="dropdown-divider"></div>

@if ($unreadCount > 0)
    <a href="{{ route('notifications.markAllAsRead') }}"
        class="dropdown-item dropdown-footer text-primary fw-bold">
        تحديد الكل كمقروء
    </a>
@else
    <span class="dropdown-item dropdown-footer text-muted">صندوق الوارد فارغ</span>
@endif
