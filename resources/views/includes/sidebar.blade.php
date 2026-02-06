<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <img src="{{ asset('assets/img/logo.jpeg') }}" alt="Logo" class="brand-image opacity-75 shadow" />
            <span class="brand-text fw-light">التحفيظ</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="true">

                {{--  لوحة التحكم --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>لوحة التحكم</p>
                    </a>
                </li>

                <li class="nav-header custom-sidebar-header">إدارة النظام</li>

                @php
                    $isEduActive =
                        request()->routeIs('group.*') ||
                        request()->routeIs('student.*') ||
                        request()->routeIs('user.*') ||
                        request()->routeIs('quran_tests.*');
                @endphp
                <li class="nav-item {{ $isEduActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isEduActive ? 'active' : '' }}">
                        <i class="nav-icon bi bi-book"></i>
                        <p>
                            إدارة الحلقات والبرامج
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('group.index') }}"
                                class="nav-link {{ request()->routeIs('group.*') ? 'active' : '' }}">
                                <i
                                    class="nav-icon bi {{ request()->routeIs('group.*') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                <p>المجموعات</p>
                            </a>
                        </li>
                        @if (auth()->user()->is_admin)
                            <li class="nav-item">
                                <a href="{{ route('student.index') }}"
                                    class="nav-link {{ request()->routeIs('student.*') ? 'active' : '' }}">
                                    <i
                                        class="nav-icon bi {{ request()->routeIs('student.*') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                    <p>الطلاب</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('user.index') }}"
                                    class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}">
                                    <i
                                        class="nav-icon bi {{ request()->routeIs('user.*') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                    <p>المستخدمين</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('quran_tests.index') }}"
                                    class="nav-link {{ request()->routeIs('quran_tests.*') ? 'active' : '' }}">
                                    <i
                                        class="nav-icon bi {{ request()->routeIs('quran_tests.*') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                    <p>الاختبارات</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                @php
                    $isAttendanceActive =
                        request()->routeIs('attendance.*') || request()->routeIs('teachers.attendance');
                @endphp
                <li class="nav-item {{ $isAttendanceActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isAttendanceActive ? 'active' : '' }}">
                        <i class="nav-icon bi bi-calendar-check"></i>
                        <p>
                            متابعة الحضور والغياب
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('attendance.index') }}"
                                class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                                <i
                                    class="nav-icon bi {{ request()->routeIs('attendance.*') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                <p>حضور الطلاب</p>
                            </a>
                        </li>
                        @if (auth()->user()->is_admin)
                            <li class="nav-item">
                                <a href="{{ route('teachers.attendance') }}"
                                    class="nav-link {{ request()->routeIs('teachers.attendance') ? 'active' : '' }}">
                                    <i
                                        class="nav-icon bi {{ request()->routeIs('teachers.attendance') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                    <p>حضور المحفظين</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                @php
                    $isReportsActive = request()->routeIs('reports.*');
                @endphp
                <li class="nav-item {{ $isReportsActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isReportsActive ? 'active' : '' }}">
                        <i class="nav-icon bi bi-bar-chart-fill"></i>
                        <p>
                            التقارير والإحصائيات
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('reports.memorization') }}"
                                class="nav-link {{ request()->routeIs('reports.memorization') ? 'active' : '' }}">
                                <i
                                    class="nav-icon bi {{ request()->routeIs('reports.memorization') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                <p>تقرير التسميع</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.students') }}"
                                class="nav-link {{ request()->routeIs('reports.students') ? 'active' : '' }}">
                                <i
                                    class="nav-icon bi {{ request()->routeIs('reports.students') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                <p>تقرير الطلاب</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.attendance') }}"
                                class="nav-link {{ request()->routeIs('reports.attendance') ? 'active' : '' }}">
                                <i
                                    class="nav-icon bi {{ request()->routeIs('reports.attendance') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                <p>تقرير الحضور للطلاب</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.teachers_courses') }}"
                                class="nav-link {{ request()->routeIs('reports.teachers_courses') ? 'active' : '' }}">
                                <i
                                    class="nav-icon bi {{ request()->routeIs('reports.teachers_courses') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                <p>تقرير بيانات المحفظين</p>
                            </a>
                        </li>
                    </ul>
                </li>


                @if (auth()->user()->is_admin)
                    @php
                        $isAdminSettingsActive = request()->routeIs('courses.*') || request()->routeIs('category.*');
                    @endphp
                    <li class="nav-item {{ $isAdminSettingsActive ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $isAdminSettingsActive ? 'active' : '' }}">
                            <i class="nav-icon bi bi-gear-fill"></i>
                            <p>
                                إعدادات الإدارة
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="{{ route('courses.index') }}"
                                    class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                                    <i
                                        class="nav-icon bi {{ request()->routeIs('courses.*') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                    <p>الدورات العلمية</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('category.index') }}"
                                    class="nav-link {{ request()->routeIs('category.*') ? 'active' : '' }}">
                                    <i
                                        class="nav-icon bi {{ request()->routeIs('category.*') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                    <p>التصنيفات</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                <li class="nav-item">
                    <a href="{{ route('parent.login') }}"
                        class="nav-link {{ request()->routeIs('parents.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people"></i>
                        <p>ولي الأمر</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
