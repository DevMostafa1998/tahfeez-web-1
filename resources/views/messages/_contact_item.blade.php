<a href="{{ route('messages.chat', $teacher->id) }}"
   data-user-id="{{ $teacher->id }}"
   data-search="{{ $teacher->full_name }} {{ $teacher->phone_number }}"
   class="contact-item list-group-item d-flex align-items-center {{ isset($activeTeacher) && $activeTeacher->id == $teacher->id ? 'active-contact' : '' }}">
    <div class="avatar-circle {{ $teacher->is_admin ? 'avatar-admin' : 'avatar-keeper' }} me-3">
        {{ mb_substr($teacher->full_name, 0, 2) }}
    </div>
    <div class="flex-grow-1 overflow-hidden">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-1 text-truncate fw-bold {{ isset($activeTeacher) && $activeTeacher->id == $teacher->id ? 'text-white' : 'text-dark' }}" style="font-size: 0.9rem;">
                {{ $teacher->full_name }}
            </h6>
            <span class="badge {{ $teacher->is_admin ? 'bg-warning text-dark' : 'bg-light text-secondary border' }}" style="font-size: 0.62rem;">
                {{ $teacher->is_admin ? 'مسؤول' : 'مُحفظ' }}
            </span>
        </div>
        <p class="mb-0 small contact-sub text-muted text-truncate {{ isset($activeTeacher) && $activeTeacher->id == $teacher->id ? 'text-white' : '' }}" style="font-size: 0.78rem;">
            {{ $teacher->phone_number ?? 'لا يوجد هاتف' }}
        </p>
    </div>
    @if(isset($teacher->unread_count) && $teacher->unread_count > 0)
        <span class="badge unread-badge rounded-pill ms-2">{{ $teacher->unread_count }}</span>
    @endif
</a>
