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

        <ul class="navbar-nav ms-auto">

            <li class="nav-item dropdown">
                <a class="nav-link" data-bs-toggle="dropdown" href="#" style="position: relative;">
                    <i class="bi bi-bell-fill"></i>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="badge bg-danger rounded-circle"
                            style="position: absolute; top: 5px; right: 5px; padding: 3px 5px; font-size: 0.6rem; border: 1px solid white;">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <span class="dropdown-item dropdown-header fw-bold">
                        {{ auth()->user()->unreadNotifications->count() }} تنبيهات غير مقروءة
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

                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <a href="{{ route('notifications.markAllAsRead') }}" class="dropdown-item dropdown-footer text-primary fw-bold">
                            تحديد الكل كمقروء
                        </a>
                    @else
                        <span class="dropdown-item dropdown-footer text-muted">صندوق الوارد فارغ</span>
                    @endif
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                </a>
            </li>

            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                    <div class="rounded-circle shadow-sm bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                        style="width: 32px; height: 32px; font-size: 14px;">
                        {{ mb_substr(Auth::user()->full_name, 0, 1) }}
                    </div>
                    <span class="d-none d-md-inline ms-2">{{ Auth::user()->full_name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <li class="user-header text-bg-primary d-flex flex-column align-items-center justify-content-center">
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
