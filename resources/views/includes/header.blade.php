<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">الرئيسية</a></li>
        </ul>
        <ul class="navbar-nav ms-auto">

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
                                عضو منذ {{ Auth::user()->creation_at ? Auth::user()->creation_at->format('Y') : '2026' }}
                            </small>
                        </p>
                    </li>

                    <li class="user-footer">
                        <a href="#" class="btn btn-default btn-flat border">الملف الشخصي</a>
                        <a href="{{ route('logout') }}" class="btn btn-default btn-flat float-end border">
                            تسجيل الخروج
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
