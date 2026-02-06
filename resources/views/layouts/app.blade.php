<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>التحفيظ @yield('title', 'لوحة التحكم')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media='all'" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="{{ asset('assets/css/adminlte.rtl.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('css')

    <style>
        html,
        body {
            font-size: 15px !important;
        }
        .app-sidebar .nav-header {
            text-align: right !important;
            padding: 1.5rem 1rem 0.5rem !important;
            font-size: 1rem !important;
            font-weight: 700 !important;
            color: #6c757d !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            background: transparent;
        }

        /* الخط  بعد الكلمة */
        .app-sidebar .nav-header::after {
            content: "";
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin-right: 10px;
        }

        /*  تحسين شكل الروابط  */
        .app-sidebar .nav-link.active {
            background-color: rgba(13, 110, 253, 0.2) !important;
            border-right: 4px solid #0d6efd !important;
            color: #fff !important;
        }

        .sidebar-menu .nav-link:hover:not(.active) {
            background-color: rgba(13, 110, 253, 0.1) !important;
            color: #fff !important;
            transition: all 0.3s ease;
        }


        .sidebar-menu .nav-link:hover .nav-icon {
            color: #0d6efd !important;
            transition: color 0.3s ease;
        }

        /*  تنسيق الدوائر */
        .nav-treeview .nav-icon.bi-circle,
        .nav-treeview .nav-icon.bi-circle-fill {
            font-size: 0.5rem !important;
            transition: all 0.2s ease;
        }

        .nav-link.active .bi-circle-fill {
            color: #0d6efd !important;
        }

        /*  ضبط اتجاه الأسهم  */
        .app-sidebar .nav-arrow {
            margin-right: auto !important;
            margin-left: 0 !important;
            display: inline-block !important;
            line-height: 1 !important;
            vertical-align: middle !important;
            margin-top: -3px !important;
            transform: rotate(180deg) !important;
            transition: transform 0.3s ease-in-out;
        }

        .sidebar-menu .menu-open > .nav-link .nav-arrow {
            transform: rotate(90deg) !important;
            margin-top: -3px !important;
        }

        .sidebar-menu .nav-link p {
            display: inline-block !important;
            margin: 0 !important;
            line-height: 1.5 !important;
            vertical-align: middle !important;
        }

        /* مسافة للأيقونات */
        .nav-icon {
            margin-left: 8px;
            vertical-align: middle;
        }
        .app-main .form-control,
        .app-main .form-select,
        .app-main .input-group-text {
            height: 38px !important;
            font-size: 0.9rem !important;
            padding: 6px 10px !important;
            border-radius: 8px !important;
        }

        .app-main .btn {
            padding: 6px 15px !important;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
        }

        .app-main .table td,
        .app-main .table th {
            padding: 10px 8px !important;
            font-size: 0.9rem !important;
        }

        label {
            font-size: 0.85rem !important;
            margin-bottom: 6px !important;
        }

        .sidebar-menu .nav-link {
            padding: 8px 12px !important;
            font-size: 0.9rem !important;
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        @auth
            @include('includes.header')

            @include('includes.sidebar')
        @endauth
        <main class="app-main">
            @yield('content')
        </main>

        @include('includes.footer')

    </div>

    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/js/adminlte.js') }}"></script>

    <script>
        const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
        const Default = {
            scrollbarTheme: 'os-theme-light',
            scrollbarAutoHide: 'leave',
            scrollbarClickScroll: true,
        };
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
