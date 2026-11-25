<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="{{ route('dashboard') }}" class="nav-link">الرئيسية</a>
            </li>
        </ul>

        <!-- قائمة اختيار المركز والجنس -->
        @if (Auth::user()->is_admin)
            @if (isset($header_centers))
                <div class="d-flex align-items-center mx-auto my-auto gap-3">
                    <!-- اختيار المركز -->
                    <form action="{{ route('center.select') }}" method="POST" id="headerCenterSelectForm"
                        class="d-flex align-items-center m-0">
                        @csrf
                        <label for="header_center_id" class="me-2 mb-0 fw-bold text-nowrap" style="font-size: 0.85rem;">
                            <i class="bi bi-building text-primary me-1"></i> المركز:
                        </label>
                        <select name="center_id" id="header_center_id" class="form-select form-select-sm shadow-sm"
                            onchange="document.getElementById('headerCenterSelectForm').submit();"
                            style="min-width: 170px;">
                            <option value="">-- جميع المراكز --</option>
                            @foreach ($header_centers as $center)
                                <option value="{{ $center->id }}"
                                    {{ isset($selected_center_id) && $selected_center_id == $center->id ? 'selected' : '' }}>
                                    {{ $center->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <!-- اختيار الجنس -->
                    <div class="d-flex align-items-center m-0">

                        <div class="btn-group" role="group">
                            <!-- اختيار الفئة (طلاب/طالبات) -->
                            <form action="{{ route('gender.select') }}" method="POST" id="headerGenderSelectForm"
                                class="d-flex align-items-center m-0">
                                @csrf
                                <label for="header_gender" class="me-2 mb-0 fw-bold text-nowrap"
                                    style="font-size: 0.85rem;">
                                    <i class="bi bi-people-fill text-success me-1"></i> الفئة:
                                </label>
                                <select name="gender" id="header_gender" class="form-select form-select-sm shadow-sm"
                                    onchange="document.getElementById('headerGenderSelectForm').submit();"
                                    style="min-width: 130px;">
                                    <option value=""
                                        {{ !isset($selected_gender) || empty($selected_gender) ? 'selected' : '' }}>
                                        -- الكل --
                                    </option>
                                    <option value="male"
                                        {{ isset($selected_gender) && $selected_gender == 'male' ? 'selected' : '' }}>
                                        طلاب
                                    </option>
                                    <option value="female"
                                        {{ isset($selected_gender) && $selected_gender == 'female' ? 'selected' : '' }}>
                                        طالبات
                                    </option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endif
        <ul class="navbar-nav ms-auto">
            @php
                $globalUnreadMessages = \App\Models\Message::where('receiver_id', auth()->id())
                    ->where('is_read', false)
                    ->count();

                $unreadConversations = \App\Models\Message::where('receiver_id', auth()->id())
                    ->where('is_read', false)
                    ->selectRaw('sender_id, COUNT(*) as total, MAX(created_at) as last_at')
                    ->groupBy('sender_id')
                    ->orderByDesc('last_at')
                    ->limit(8)
                    ->get();

                $unreadSenders = \App\Models\User::whereIn('id', $unreadConversations->pluck('sender_id'))
                    ->get()
                    ->keyBy('id');
            @endphp
            <li class="nav-item dropdown">
                <a class="nav-link js-mail-dropdown" data-bs-toggle="dropdown" href="#" style="position: relative;" title="الرسائل الواردة">
                    <i class="bi bi-envelope-fill" style="font-size: 1.1rem;"></i>
                    <span class="badge bg-danger rounded-circle unread-total-badge"
                        style="position: absolute; top: 5px; right: 2px; padding: 3px 5px; font-size: 0.6rem; border: 1px solid white; {{ $globalUnreadMessages > 0 ? '' : 'display: none;' }}">
                        {{ $globalUnreadMessages }}
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end js-mail-menu" style="min-width: 320px;">
                    @include('includes._mail_dropdown_body')
                </div>
            </li>
            <!-- التنبيهات -->
            <li class="nav-item dropdown">
                <a class="nav-link js-notif-dropdown" data-bs-toggle="dropdown" href="#" style="position: relative;">
                    <i class="bi bi-bell-fill"></i>
                    <span class="badge bg-danger rounded-circle notification-count-badge"
                        style="position: absolute; top: 5px; right: 5px; padding: 3px 5px; font-size: 0.6rem; border: 1px solid white; {{ auth()->user()->unreadNotifications->count() > 0 ? '' : 'display: none;' }}">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end js-notif-menu">
                    @include('includes._notifications_dropdown_body')
                </div>
            </li>

            <!-- ملء الشاشة -->
            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                </a>
            </li>

            <!-- قائمة المستخدم -->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                    <div class="rounded-circle shadow-sm bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                        style="width: 32px; height: 32px; font-size: 14px;">
                        {{ mb_substr(Auth::user()->full_name, 0, 1) }}
                    </div>
                    <span class="d-none d-md-inline ms-2">{{ Auth::user()->full_name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <li
                        class="user-header text-bg-primary d-flex flex-column align-items-center justify-content-center">
                        <div class="rounded-circle shadow bg-white text-primary d-flex align-items-center justify-content-center fw-bold mb-2"
                            style="width: 55px; height: 55px; font-size: 30px; border: 3px solid rgba(255,255,255,0.5);">
                            {{ mb_substr(Auth::user()->full_name, 0, 1) }}
                        </div>
                        <p>
                            {{ Auth::user()->full_name }}
                            <small>
                                {{ Auth::user()->is_admin ? 'مدير النظام' : 'محفظ' }}
                                <br>
                                عضو منذ
                                {{ Auth::user()->created_at ? Auth::user()->created_at->format('Y') : '2026' }}
                            </small>
                        </p>
                    </li>

                    <li class="user-footer">
                        <a href="{{ route('profile.index') }}" class="btn btn-default btn-flat border">
                            الملف الشخصي
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-default btn-flat float-end border">
                                تسجيل الخروج
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
