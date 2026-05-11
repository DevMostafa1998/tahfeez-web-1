<style>
    .brand-link {
        text-decoration: none !important;
        transition: background-color 0.3s ease;
    }

    .brand-link:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .logo-wrapper {
        transition: transform 0.3s ease;
    }

    .brand-link:hover .logo-wrapper {
        transform: scale(1.11);
    }

    @keyframes pulse-green {
        0% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }

    .bg-success-pulse {
        animation: pulse-green 2s infinite;
        background-color: #28a745 !important;
    }

    #downloadAppModal {
        z-index: 1080 !important;
    }

    /* .modal-backdrop { */
    /* z-index: 1070 !important; */
    /* } */

    #downloadAppModal .modal-content {
        pointer-events: auto !important;
        border-radius: 20px;
        background-color: #1a1d21;
    }



    .footer-download-btn {
        background: rgba(40, 167, 69, 0.15);
        color: #28a745 !important;
        border: 1px solid rgba(40, 167, 69, 0.3);
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .footer-download-btn:hover {
        background: #28a745;
        color: #fff !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
    }

    .sidebar-footer {
        background: rgba(0, 0, 0, 0.1);
    }

    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');
</style>

<aside class="app-sidebar bg-body-secondary shadow d-flex flex-column" data-bs-theme="dark">
    <div class="sidebar-brand border-secondary-subtle mb-3">
        <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center gap-2 py-3 px-3 transition-all">
            <div class="logo-wrapper bg-white shadow-sm d-flex align-items-center justify-content-center"
                style="width: 40px; height: 40px; border-radius: 12px; overflow: hidden; border: 2px solid rgba(255,255,255,0.1);">
                <img src="{{ asset('assets/img/logo.jpeg') }}" alt="Logo" class="brand-image img-fluid"
                    style="object-fit: cover; width: 100%; height: 100%;">
            </div>
            <div class="brand-text-wrapper">
                <span class="brand-text fw-bold fs-5 mb-0" style="letter-spacing: 0.5px;"> التحفيظ </span>
                <div class="brand-status d-flex align-items-center gap-1">
                    <span class="badge rounded-pill bg-success" style="font-size: 0.6rem; padding: 2px 6px;">النظام
                        الذكي</span>
                </div>
            </div>
        </a>
    </div>

    <div class="sidebar-wrapper flex-grow-1 overflow-y-auto">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="true">
                {{-- لوحة التحكم --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>لوحة التحكم</p>
                    </a>
                </li>

                <li class="nav-header custom-sidebar-header">إدارة النظام</li>

                @if (auth()->user()->is_admin == 1 || auth()->user()->is_admin_rouls == 1)
                    @php $isPeopleActive = request()->routeIs('student.*') || request()->routeIs('user.*'); @endphp
                    <li class="nav-item {{ $isPeopleActive ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $isPeopleActive ? 'active' : '' }}">
                            <i class="nav-icon bi bi-people-fill"></i>
                            <p>إدارة الحسابات والطلاب <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('user.index') }}"
                                    class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}">
                                    <i
                                        class="nav-icon bi {{ request()->routeIs('user.*') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                    <p>المستخدمين (المحفظين)</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('student.index') }}"
                                    class="nav-link {{ request()->routeIs('student.*') ? 'active' : '' }}">
                                    <i
                                        class="nav-icon bi {{ request()->routeIs('student.*') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                    <p>الطلاب</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- الحلقات والبرامج --}}
                @php $isEduActive = request()->routeIs('group.*') || request()->routeIs('quran_tests.*'); @endphp
                <li class="nav-item {{ $isEduActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isEduActive ? 'active' : '' }}">
                        <i class="nav-icon bi bi-book"></i>
                        <p>إدارة الحلقات والبرامج <i class="nav-arrow bi bi-chevron-right"></i></p>
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
                        @if (auth()->user()->is_admin == 1 || auth()->user()->is_admin_rouls == 1)
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

                {{-- متابعة الحضور --}}
                @php $isAttendanceActive = request()->routeIs('attendance.*') || request()->routeIs('teachers.attendance'); @endphp
                <li class="nav-item {{ $isAttendanceActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isAttendanceActive ? 'active' : '' }}">
                        <i class="nav-icon bi bi-calendar-check"></i>
                        <p>متابعة الحضور والغياب <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        @if (auth()->user()->is_admin == 1 || auth()->user()->is_admin_rouls == 1)
                            <li class="nav-item">
                                <a href="{{ route('teachers.attendance') }}"
                                    class="nav-link {{ request()->routeIs('teachers.attendance') ? 'active' : '' }}">
                                    <i
                                        class="nav-icon bi {{ request()->routeIs('teachers.attendance') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                    <p>حضور المحفظين</p>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ route('attendance.index') }}"
                                class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                                <i
                                    class="nav-icon bi {{ request()->routeIs('attendance.*') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                <p>حضور الطلاب</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- التقارير --}}
                @php $isReportsActive = request()->routeIs('reports.*'); @endphp
                <li class="nav-item {{ $isReportsActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isReportsActive ? 'active' : '' }}">
                        <i class="nav-icon bi bi-bar-chart-fill"></i>
                        <p>التقارير والإحصائيات <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('reports.teachers_courses') }}"
                                class="nav-link {{ request()->routeIs('reports.teachers_courses') ? 'active' : '' }}">
                                <i class="nav-icon bi {{ request()->routeIs('reports.teachers_courses') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                <p>تقرير المحفظين</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.students') }}"
                                class="nav-link {{ request()->routeIs('reports.students') ? 'active' : '' }}">
                                <i class="nav-icon bi {{ request()->routeIs('reports.students') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                <p>تقرير الطلاب</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.memorization') }}"
                                class="nav-link {{ request()->routeIs('reports.memorization') ? 'active' : '' }}">
                                <i class="nav-icon bi {{ request()->routeIs('reports.memorization') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                <p>تقرير التسميع</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.attendance') }}"
                                class="nav-link {{ request()->routeIs('reports.attendance') ? 'active' : '' }}">
                                <i class="nav-icon bi {{ request()->routeIs('reports.attendance') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                <p>تقرير الحضور للطلاب</p>
                            </a>
                        </li>
                    </ul>
                </li>


                {{-- إعدادات الإدارة --}}
                @if (auth()->user()->is_admin == 1 || auth()->user()->is_admin_rouls == 1)
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
                                    <i class="nav-icon bi {{ request()->routeIs('courses.*') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                    <p>الدورات العلمية</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('category.index') }}"
                                    class="nav-link {{ request()->routeIs('category.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi {{ request()->routeIs('category.*') ? 'bi-circle-fill' : 'bi-circle' }}"></i>
                                    <p>التصنيفات</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- قسم إرسال التنبيهات - خارجي ومستقل --}}
                @if (auth()->user()->is_admin == 1 || auth()->user()->is_admin_rouls == 1)
                <li class="nav-item">
                    <a href="{{ route('notifications.index') }}"
                        class="nav-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                        <i class="nav-icon bi {{ request()->routeIs('notifications.index') ? 'bi-bell-fill' : 'bi-bell' }}"></i>
                        <p>إرسال التنبيهات</p>
                    </a>
                </li>
                @endif
                {{-- ولي الأمر --}}
                <li class="nav-item">
                    <a href="{{ route('parent.login') }}"
                        class="nav-link {{ request()->routeIs('parent.login') || request()->routeIs('parents.index') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people"></i>
                        <p>ولي الأمر</p>
                    </a>
                </li>
                {{-- زر تحميل التطبيق --}}
                {{-- <li class="nav-item mt-4 px-3">
                    <button type="button"
                        class="nav-link bg-success-pulse text-white d-flex align-items-center justify-content-center gap-2 shadow-sm w-100 border-0"
                        style="border-radius: 10px; transition: all 0.3s ease; padding: 10px;" data-bs-toggle="modal"
                        data-bs-target="#downloadAppModal">
                        <i class="bi bi-phone-fill"></i>
                        <span class="mb-0">تحميل التطبيق</span>
                    </button>
                </li> --}}
            </ul>
        </nav>
    </div>

    <div class="sidebar-footer p-3 border-top border-secondary-subtle mt-auto">
        <div class="d-flex align-items-center justify-content-between">
            <div class="version-info">
                <small class="text-white-50 d-block"
                    style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px;">إصدار النظام</small>
                <span class="fw-bold text-white"
                    style="font-size: 0.85rem;">{{ config('app.version', '1.0.0') }}</span>
            </div>

            <button type="button" class="footer-download-btn d-flex align-items-center gap-2" data-bs-toggle="modal"
                data-bs-target="#downloadAppModal">
                <i class="bi bi-android2"></i>
                <span>تحميل التطبيق</span>
            </button>
        </div>
    </div>
</aside>

{{-- تم نقل المودال خارج الـ aside لضمان عمله بشكل صحيح في المتصفح --}}
<div class="modal fade" id="downloadAppModal" tabindex="-1" aria-labelledby="downloadAppModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-qr-code text-success fs-1"></i>
                </div>

                <div id="qrcode-container" class="bg-white p-2 mb-3 d-inline-block shadow-sm"
                    style="border-radius: 12px;"></div>

                <p class="text-white-50 small mb-3">امسح الكود بالكاميرا للتحميل المباشر</p>

                @php $app_url = 'https://ahlalrahman.com/public/apps/Tahfiz_v1.apk'; @endphp

                <a href="{{ $app_url }}" target="_blank" rel="noopener noreferrer"
                    class="btn btn-success w-100 rounded-pill py-2 fw-bold shadow">
                    <i class="bi bi-download me-1"></i> تحميل مباشر APK
                </a>

                <button type="button" class="btn btn-link text-white-50 btn-sm mt-2 text-decoration-none"
                    data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('downloadAppModal');
        modalElement.addEventListener('shown.bs.modal', function() {
            const container = document.getElementById('qrcode-container');
            container.innerHTML = "";
            if (typeof QRCode !== 'undefined') {
                new QRCode(container, {
                    text: "{{ $app_url }}",
                    width: 160,
                    height: 160,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }
        });
    });
</script>
