@extends('layouts.app')

@section('content')
<style>
    .chat-shell {
        border-radius: 20px;
        overflow: hidden;
        height: 80vh;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15);
        border: 1px solid rgba(15, 23, 42, 0.06);
    }
    .chat-sidebar {
        background: linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
    }
    .chat-sidebar-header {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: #fff;
    }
    .list-section-title {
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
        padding: 10px 16px 4px;
        letter-spacing: 0.5px;
    }
    .contact-item {
        border: none !important;
        border-radius: 14px !important;
        margin: 2px 8px;
        padding: 10px 12px !important;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .contact-item:hover {
        background: #e0e7ff !important;
        transform: translateX(-2px);
    }
    .contact-item.active-contact {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
        color: #fff;
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
    }
    .contact-item.active-contact .text-muted,
    .contact-item.active-contact .contact-sub {
        color: rgba(255,255,255,0.85) !important;
    }
    .contact-item.active-contact .badge.bg-light {
        background: #fff !important;
        color: #4f46e5 !important;
    }
    .avatar-circle {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        color: #fff;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
    }
    .avatar-admin { background: linear-gradient(135deg, #f59e0b, #f97316); }
    .avatar-keeper { background: linear-gradient(135deg, #0ea5e9, #6366f1); }
    .avatar-active { background: linear-gradient(135deg, #4f46e5, #8b5cf6); }

    .chat-header {
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
    }
    .chat-area {
        background-color: #eef2f7;
        background-image: radial-gradient(#d7dee8 1px, transparent 1px);
        background-size: 22px 22px;
    }
    .chat-date-separator {
        display: flex;
        justify-content: center;
        margin: 14px 0;
    }
    .chat-date-separator span {
        background: rgba(255,255,255,0.9);
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 5px 16px;
        border-radius: 999px;
        box-shadow: 0 2px 6px rgba(15,23,42,0.06);
    }
    .chat-unread-divider {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 14px 0;
    }
    .chat-unread-divider::before,
    .chat-unread-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #c7d2fe;
    }
    .chat-unread-divider span {
        font-size: 0.72rem;
        font-weight: 700;
        color: #4f46e5;
        background: #eef2ff;
        border: 1px solid #c7d2fe;
        padding: 3px 14px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .message-row {
        display: flex;
        margin-bottom: 10px;
        animation: msgIn 0.25s ease;
    }
    @keyframes msgIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .message-me { justify-content: flex-end; }
    .message-other { justify-content: flex-start; }
    .message-bubble {
        max-width: 68%;
        padding: 9px 14px 7px;
        border-radius: 18px;
        position: relative;
        box-shadow: 0 2px 8px rgba(15,23,42,0.08);
        word-break: break-word;
    }
    .message-me .message-bubble {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #fff;
        border-bottom-right-radius: 6px;
    }
    .message-other .message-bubble {
        background: #ffffff;
        color: #1e293b;
        border: 1px solid #e8edf3;
        border-bottom-left-radius: 6px;
    }
    .message-sender {
        font-size: 0.72rem;
        font-weight: 700;
        margin-bottom: 2px;
        opacity: 0.85;
    }
    .message-me .message-sender { color: #ddd6fe; }
    .message-text { font-size: 0.92rem; line-height: 1.6; white-space: pre-wrap; }
    .message-meta {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 4px;
        margin-top: 3px;
    }
    .message-time { font-size: 0.68rem; opacity: 0.75; }
    .message-tick { font-size: 0.8rem; opacity: 0.85; }
    .edited-label { font-size: 0.68rem; opacity: 0.7; font-style: italic; }
    .edit-message-btn {
        color: inherit;
        opacity: 0.6;
        font-size: 0.85rem;
        text-decoration: none !important;
    }
    .edit-message-btn:hover { opacity: 1; }
    .message-edit-box {
        display: flex;
        gap: 6px;
        margin-top: 6px;
    }
    .message-edit-box input {
        flex: 1;
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 10px;
        padding: 5px 10px;
        font-size: 0.85rem;
        background: rgba(255,255,255,0.15);
        color: inherit;
    }
    .message-edit-box input::placeholder { color: rgba(255,255,255,0.6); }
    .message-other .message-edit-box input {
        background: #f1f5f9;
        border-color: #e2e8f0;
        color: #1e293b;
    }
    .message-edit-box input::placeholder { color: #94a3b8; }
    .message-edit-box button {
        border: none;
        border-radius: 10px;
        padding: 5px 12px;
        font-size: 0.8rem;
        font-weight: 700;
        background: #10b981;
        color: #fff;
    }
    .message-edit-box .cancel-edit {
        background: #64748b;
    }
    .older-loader {
        display: none;
        justify-content: center;
        padding: 10px;
    }
    .older-loader.show { display: flex; }
    .send-btn {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        border-radius: 999px;
        padding: 8px 22px;
        font-weight: 700;
        box-shadow: 0 6px 14px rgba(79,70,229,0.35);
        transition: transform 0.15s ease;
    }
    .send-btn:hover { transform: translateY(-1px); }
    .send-input {
        border-radius: 999px;
        border: 1.5px solid #e2e8f0;
        padding: 10px 20px;
        transition: border-color 0.2s;
    }
    .send-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.12); }
    .unread-badge {
        background: linear-gradient(135deg, #ef4444, #f43f5e);
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card chat-shell border-0">
                <div class="row g-0 h-100">

                    {{-- القائمة الجانبية --}}
                    <div class="col-md-4 chat-sidebar border-end d-flex flex-column h-100">
                        <div class="chat-sidebar-header p-3">
                            <h5 class="mb-2 fw-bold"><i class="bi bi-chat-dots-fill me-2"></i> المحادثات والرسائل</h5>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text border-0" style="background: rgba(255,255,255,0.2);"><i class="bi bi-search text-white"></i></span>
                                <input type="text" id="searchTeacher" class="form-control border-0" placeholder="ابحث عن مسؤول أو محفظ...">
                            </div>
                        </div>
                        <div class="overflow-auto flex-grow-1 py-2" id="teachersList">
                            @foreach($teachers as $teacher)
                                @include('messages._contact_item', ['teacher' => $teacher])
                            @endforeach
                            @if($teachers->isEmpty())
                                <div class="text-center p-4 text-muted">لا توجد جهات اتصال متاحة حالياً.</div>
                            @endif
                        </div>
                    </div>

                    {{-- قسم المحادثة الرئيسي --}}
                    <div class="col-md-8 d-flex flex-column h-100">
                        <div id="chatPanel" class="d-flex flex-column h-100 {{ isset($activeTeacher) ? '' : 'd-none' }}">
                            {{-- هيدر المحادثة --}}
                            <div class="chat-header p-3 d-flex align-items-center">
                                <div id="chatAvatar" class="avatar-circle avatar-active me-3">
                                    {{ isset($activeTeacher) ? mb_substr($activeTeacher->full_name, 0, 2) : '' }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold text-dark" id="chatName">{{ $activeTeacher->full_name ?? '' }}</h6>
                                    <small class="text-muted" id="chatRole">
                                        @if(isset($activeTeacher))
                                            <i class="bi bi-circle-fill text-success" style="font-size: 8px;"></i>
                                            {{ $activeTeacher->is_admin ? 'مدير النظام' : 'مُحفظ معتمد' }} - هاتف: {{ $activeTeacher->phone_number ?? 'غير متوفر' }}
                                        @endif
                                    </small>
                                </div>
                                <span class="badge bg-light text-secondary border" id="chatRoleBadge">
                                    {{ isset($activeTeacher) ? ($activeTeacher->is_admin ? 'مسؤول / إداري' : 'مُحفظ') : '' }}
                                </span>
                            </div>

                            {{-- صندوق الرسائل --}}
                            <div class="chat-area flex-grow-1 overflow-auto p-4" id="chatBox">
                                <div class="older-loader" id="olderLoader">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                </div>
                                <div id="chatMessages">
                                    @if(isset($activeTeacher))
                                        @include('messages._message_rows', [
                                            'messages' => $messages,
                                            'currentUser' => auth()->user(),
                                            'otherUser' => $activeTeacher,
                                            'prevMessage' => null,
                                            'firstUnreadId' => $firstUnreadId ?? null,
                                        ])
                                        @if($messages->isEmpty())
                                            <div class="text-center text-muted my-auto py-5 chat-empty-state">
                                                <i class="bi bi-chat-square-text display-4 text-secondary opacity-50"></i>
                                                <p class="mt-2">لا توجد رسائل سابقة مع {{ $activeTeacher->full_name }}. ابدأ المحادثة الآن!</p>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- نموذج الإرسال --}}
                            <div class="p-3 bg-white border-top">
                                <form id="sendMessageForm" action="{{ route('messages.send') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="receiver_id" id="receiverIdInput" value="{{ $activeTeacher->id ?? '' }}">
                                    <div class="input-group">
                                        <input type="text" name="body" id="messageInput" class="form-control send-input" placeholder="اكتب رسالتك هنا..." autocomplete="off" required>
                                        <button class="btn send-btn text-white ms-2" type="submit">
                                            <i class="bi bi-send-fill ms-1"></i> إرسال
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div id="emptyChatPanel" class="d-flex flex-column align-items-center justify-content-center h-100 {{ isset($activeTeacher) ? 'd-none' : '' }}">
                            <div class="empty-icon-wrap mb-3">
                                <i class="bi bi-chat-heart display-1 text-primary opacity-25"></i>
                            </div>
                            <h4 class="fw-bold text-dark">صندوق المحادثات والرسائل</h4>
                            <p class="text-muted">اختر جهة اتصال من القائمة لعرض المحادثة وبدء المراسلة.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const BASE = "{{ url('admin/messages') }}";
    const SEND_URL = "{{ route('messages.send') }}";
    const UPDATE_BASE = "{{ url('admin/messages/message') }}";
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const chatBox = document.getElementById('chatBox');
    const chatMessages = document.getElementById('chatMessages');
    const olderLoader = document.getElementById('olderLoader');
    const chatPanel = document.getElementById('chatPanel');
    const emptyPanel = document.getElementById('emptyChatPanel');
    const form = document.getElementById('sendMessageForm');
    const messageInput = document.getElementById('messageInput');
    const receiverIdInput = document.getElementById('receiverIdInput');
    const teachersList = document.getElementById('teachersList');

    let currentTeacherId = {{ isset($activeTeacher) ? (int) $activeTeacher->id : 'null' }};
    let hasMore = {{ isset($activeTeacher) && $hasMoreMessages ? 'true' : 'false' }};
    let oldestId = null;
    let newestId = null;
    let loadingOlder = false;

    function refreshOldestId() {
        const first = chatMessages.querySelector('.message-row');
        oldestId = first ? parseInt(first.dataset.id, 10) : null;
    }

    function refreshNewestId() {
        const rows = chatMessages.querySelectorAll('.message-row');
        newestId = rows.length ? parseInt(rows[rows.length - 1].dataset.id, 10) : null;
    }

    function removeEmptyState() {
        chatMessages.querySelectorAll('.chat-empty-state').forEach(el => el.remove());
    }

    function appendMessagesHtml(html) {
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        const existing = new Set(
            [...chatMessages.querySelectorAll('.message-row')].map(r => r.dataset.id)
        );
        let added = false;
        let pendingSep = null;

        [...tmp.childNodes].forEach(node => {
            if (node.nodeType !== 1) return;
            if (node.classList.contains('chat-date-separator')) {
                pendingSep = node;
                return;
            }
            if (node.classList.contains('message-row')) {
                if (existing.has(node.dataset.id)) {
                    pendingSep = null;
                    return;
                }
                if (pendingSep) {
                    chatMessages.appendChild(pendingSep);
                    pendingSep = null;
                }
                chatMessages.appendChild(node);
                existing.add(node.dataset.id);
                added = true;
                return;
            }
            if (pendingSep) {
                chatMessages.appendChild(pendingSep);
                pendingSep = null;
            }
            chatMessages.appendChild(node);
        });
        return added;
    }

    function moveContactToTop(userId) {
        const item = teachersList.querySelector(`.contact-item[data-user-id="${userId}"]`);
        if (item && teachersList.firstElementChild !== item) {
            teachersList.prepend(item);
        }
    }

    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function setActiveContact(userId) {
        teachersList.querySelectorAll('.contact-item').forEach(el => {
            el.classList.toggle('active-contact', parseInt(el.dataset.userId, 10) === userId);
        });
    }

    function clearUnreadBadge(userId) {
        const item = teachersList.querySelector(`.contact-item[data-user-id="${userId}"] .unread-badge`);
        if (item) item.remove();
    }

    function updateChatHeader(teacher) {
        document.getElementById('chatAvatar').textContent = teacher.full_name.substring(0, 2);
        document.getElementById('chatAvatar').className = 'avatar-circle avatar-active me-3';
        document.getElementById('chatName').textContent = teacher.full_name;
        document.getElementById('chatRole').innerHTML =
            '<i class="bi bi-circle-fill text-success" style="font-size: 8px;"></i> ' +
            teacher.role_label + ' - هاتف: ' + (teacher.phone_number || 'غير متوفر');
        document.getElementById('chatRoleBadge').textContent = teacher.is_admin ? 'مسؤول / إداري' : 'مُحفظ';
    }

    function showChat() {
        chatPanel.classList.remove('d-none');
        emptyPanel.classList.add('d-none');
    }

    function loadChat(userId, pushState = true) {
        if (currentTeacherId === userId && chatMessages.children.length) return;

        fetch(`${BASE}/${userId}/data`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;

            currentTeacherId = userId;
            hasMore = data.has_more;
            oldestId = data.oldest_id;

            updateChatHeader(data.teacher);
            receiverIdInput.value = userId;
            setActiveContact(userId);
            clearUnreadBadge(userId);
            chatMessages.innerHTML = data.html || emptyStateHtml(data.teacher.full_name);
            refreshNewestId();
            showChat();
            scrollToBottom();

            if (pushState) {
                history.pushState({ teacherId: userId }, '', `${BASE}/${userId}`);
            }
        })
        .catch(err => console.error('loadChat error:', err));
    }

    function emptyStateHtml(name) {
        return `<div class="text-center text-muted my-auto py-5 chat-empty-state">
            <i class="bi bi-chat-square-text display-4 text-secondary opacity-50"></i>
            <p class="mt-2">لا توجد رسائل سابقة مع ${name}. ابدأ المحادثة الآن!</p>
        </div>`;
    }

    teachersList.addEventListener('click', function (e) {
        const item = e.target.closest('.contact-item');
        if (!item) return;
        e.preventDefault();
        loadChat(parseInt(item.dataset.userId, 10));
    });

    window.addEventListener('popstate', function (e) {
        if (e.state && e.state.teacherId) {
            loadChat(e.state.teacherId, false);
        } else {
            currentTeacherId = null;
            chatMessages.innerHTML = '';
            chatPanel.classList.add('d-none');
            emptyPanel.classList.remove('d-none');
            setActiveContact(null);
        }
    });

    chatBox.addEventListener('scroll', function () {
        if (!hasMore || loadingOlder || !oldestId || !currentTeacherId) return;
        if (chatBox.scrollTop > 80) return;

        loadingOlder = true;
        olderLoader.classList.add('show');

        const prevHeight = chatBox.scrollHeight;
        const prevTop = chatBox.scrollTop;

        fetch(`${BASE}/${currentTeacherId}/data?before=${oldestId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.html) {
                chatMessages.insertAdjacentHTML('afterbegin', data.html);
                hasMore = data.has_more;
                oldestId = data.oldest_id;
                // الحفاظ على موضع التمرير بعد إضافة المحتوى بالأعلى
                chatBox.scrollTop = chatBox.scrollHeight - prevHeight + prevTop;
            } else {
                hasMore = false;
            }
        })
        .catch(err => console.error('loadOlder error:', err))
        .finally(() => {
            loadingOlder = false;
            olderLoader.classList.remove('show');
        });
    });

    // إرسال رسالة جديدة عبر AJAX
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!receiverIdInput.value) return;

        const formData = new FormData(form);
        const sentToId = parseInt(receiverIdInput.value, 10);
        messageInput.disabled = true;

        fetch(SEND_URL, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(r => r.json().catch(() => null))
        .then(data => {
            if (data && data.success) {
                removeEmptyState();
                appendMessagesHtml(data.html || '');
                newestId = data.id || newestId;
                refreshNewestId();
                moveContactToTop(sentToId);
                scrollToBottom();
            } else {
                // fallback: إعادة جلب المحادثة للتأكد من ظهور الرسالة
                reloadCurrentChat();
            }
        })
        .catch(err => {
            console.error('send error:', err);
            reloadCurrentChat();
        })
        .finally(() => {
            messageInput.disabled = false;
            messageInput.value = '';
            messageInput.focus();
        });
    });

    // إعادة تحميل رسائل المحادثة الحالية بالكامل (fallback عند فشل الإرسال AJAX)
    function reloadCurrentChat() {
        if (!currentTeacherId) return;
        fetch(`${BASE}/${currentTeacherId}/data`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                chatMessages.innerHTML = data.html || emptyStateHtml(data.teacher.full_name);
                refreshOldestId();
                refreshNewestId();
                scrollToBottom();
            }
        })
        .catch(err => console.error('reloadChat error:', err));
    }

    chatMessages.addEventListener('click', function (e) {
        const btn = e.target.closest('.edit-message-btn');
        if (!btn) return;
        startEdit(btn);
    });

    function startEdit(btn) {
        const row = btn.closest('.message-row');
        const bubble = row.querySelector('.message-bubble');
        const textEl = row.querySelector('.message-text');
        const currentText = textEl.childNodes[0].textContent.trim();

        const box = document.createElement('div');
        box.className = 'message-edit-box';
        box.innerHTML = `
            <input type="text" value="${currentText.replace(/"/g, '&quot;')}" maxlength="1000">
            <button type="button" class="save-edit">حفظ</button>
            <button type="button" class="cancel-edit">إلغاء</button>
        `;

        const meta = row.querySelector('.message-meta');
        meta.style.display = 'none';
        textEl.style.display = 'none';
        bubble.appendChild(box);
        box.querySelector('input').focus();

        box.querySelector('.cancel-edit').addEventListener('click', () => {
            box.remove();
            textEl.style.display = '';
            meta.style.display = '';
        });

        box.querySelector('.save-edit').addEventListener('click', () => {
            const newBody = box.querySelector('input').value.trim();
            if (!newBody) return;

            fetch(`${UPDATE_BASE}/${btn.dataset.id}`, {
                method: 'PATCH',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CSRF,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ body: newBody })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    textEl.childNodes[0].textContent = ' ' + data.body;
                    if (!row.querySelector('.edited-label')) {
                        textEl.insertAdjacentHTML('beforeend', ' <span class="edited-label">(معدّلة)</span>');
                    }
                    box.remove();
                    textEl.style.display = '';
                    meta.style.display = '';
                } else {
                    alert(data.message || 'تعذر تعديل الرسالة');
                }
            })
            .catch(err => console.error('edit error:', err));
        });
    }

    let polling = false;
    function pollNewMessages() {
        if (!currentTeacherId || polling || document.hidden) return;
        if (!newestId) { refreshNewestId(); if (!newestId) return; }

        polling = true;
        fetch(`${BASE}/${currentTeacherId}/data?after=${newestId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.html) {
                const nearBottom = chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight < 120;
                removeEmptyState();
                const added = appendMessagesHtml(data.html);
                if (!added) return;
                refreshNewestId();
                moveContactToTop(currentTeacherId);
                if (nearBottom) scrollToBottom();
            }
        })
        .catch(err => console.error('poll error:', err))
        .finally(() => { polling = false; });
    }
    setInterval(pollNewMessages, 5000);

    const searchInput = document.getElementById('searchTeacher');
    searchInput.addEventListener('input', function () {
        const filter = this.value.trim().toLowerCase();
        teachersList.querySelectorAll('.contact-item').forEach(item => {
            const haystack = (item.dataset.search || item.textContent || '').toLowerCase();
            const hide = !!filter && !haystack.includes(filter);
            if (hide) {
                item.style.setProperty('display', 'none', 'important');
            } else {
                item.style.removeProperty('display');
            }
        });
    });

    if (currentTeacherId) {
        refreshOldestId();
        refreshNewestId();
        scrollToBottom();
    }
})();
</script>
@endsection
