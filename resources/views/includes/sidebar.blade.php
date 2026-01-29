<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <img src="{{ asset('assets/img/logo.jpeg') }}" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
            <span class="brand-text fw-light">التحفيظ</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation">

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>لوحة التحكم</p>
                    </a>
                </li>

                @if (auth()->user()->is_admin)
                    {{-- المستخدمين --}}
                    <li class="nav-item">
                        <a href="{{ route('user') }}" class="nav-link {{ request()->routeIs('user') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-palette"></i>
                            <p>المستخدمين</p>
                        </a>
                    </li>

                    {{-- الطلاب --}}
                    <li class="nav-item">
                        <a href="{{ route('student.index') }}"
                            class="nav-link {{ request()->routeIs('student.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-mortarboard"></i>
                            <p>الطلاب</p>
                        </a>
                    </li>
                @endif

                {{-- المجموعات - تظهر للجميع --}}
                <li class="nav-item">
                    <a href="{{ route('group.index') }}"
                        class="nav-link {{ request()->routeIs('group.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people"></i>
                        <p>المجموعات</p>
                    </a>
                </li>

                @if (auth()->user()->is_admin)
                    {{-- إدارة الدورات العلمية --}}
                    <li class="nav-item">
                        <a href="{{ route('courses.index') }}"
                            class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-book-half"></i>
                            <p>إدارة الدورات</p>
                        </a>
                    </li>

                    {{-- الاختبارات --}}
                    <li class="nav-item">
                        <a href="{{ route('quran_tests.index') }}"
                            class="nav-link {{ request()->routeIs('quran_tests.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-pencil-square"></i>
                            <p>الاختبارات</p>
                        </a>
                    </li>

                    {{-- إدارة التصنيفات --}}
                    <li class="nav-item">
                        <a href="{{ route('category.index') }}"
                            class="nav-link {{ request()->routeIs('category.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-tags-fill"></i>
                            <p>إدارة التصنيفات</p>
                        </a>
                    </li>
                @endif
                {{-- تقرير التسميع اليومي - يظهر للجميع --}}
                <li class="nav-item">
                    <a href="{{ route('reports.memorization') }}"
                        class="nav-link {{ request()->routeIs('reports.memorization') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-file-earmark-bar-graph"></i>
                        <p>تقرير التسميع </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports.students') }}"
                        class="nav-link {{ request()->routeIs('reports.students') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-file-earmark-spreadsheet-fill"></i>
                        <p>تقرير الطلاب</p>
                    </a>
                </li>
                {{-- حضور وغياب الطلاب - تظهر للجميع --}}
                <li class="nav-item">
                    <a href="{{ route('attendance.index') }}"
                        class="nav-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-check-fill"></i>
                        <p>حضور وغياب الطلاب</p>
                    </a>
                </li>



                @if (auth()->user()->is_admin)
                    {{-- حضور وغياب المحفظين --}}
                    <li class="nav-item">
                        <a href="{{ route('teachers.attendance') }}"
                            class="nav-link {{ request()->routeIs('teachers.attendance') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-person-check"></i>
                            <p>حضور وغياب المحفظين</p>
                        </a>
                    </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>
