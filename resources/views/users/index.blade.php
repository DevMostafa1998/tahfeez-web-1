@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="{{ asset('assets/css/user_table.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

    <style>
        .dataTables_wrapper .row:first-child {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center;
            width: 100%;
            margin: 0 0 1rem 0;
            padding: 0 15px;
        }

        #usersTable {
            width: 100% !important;
            margin: 0 !important;
        }

        .action-btn {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        select.custom-select {
            direction: ltr !important;
            text-align: center !important;
            background-image: none !important;
            appearance: menulist !important;
            -webkit-appearance: menulist !important;
            -moz-appearance: menulist !important;
            padding: 4px 30px 4px 10px !important;
            min-width: auto !important;
            height: auto !important;
            border-radius: 4px;
        }

        .dataTables_filter {
            text-align: left !important;
        }

        .dataTables_filter input {
            margin-right: 10px;
        }

        .dataTables_length {
            text-align: right !important;
        }

        .btn-excel {
            background-color: #1d6f42 !important;
            color: white !important;
            border-radius: 8px !important;
            padding: 5px 15px !important;
            font-weight: bold !important;
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    <div class="container-fluid p-4" dir="rtl">
        {{-- الهيدر الموحد --}}
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-3 shadow-sm">
                    <i class="bi bi-people-fill fs-3 text-primary"></i>
                </div>
                <div>
                    <h1 class="page-title m-0 h3">إدارة المستخدمين</h1>
                </div>
            </div>
            <a href="{{ route('user.create') }}"
                class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 rounded-3">
                <i class="bi bi-plus-lg"></i><span>مستخدم جديد</span>
            </a>
        </div>

        <div class="card card-table shadow-sm border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="p-3">
                    <table id="usersTable" class="table table-striped table-bordered align-middle mb-0" style="width:100%">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="text-center">اسم المستخدم</th>
                                <th class="text-center">رقم الهوية</th>
                                <th class="text-center">التصنيف</th>
                                <th class="text-center">الصلاحية</th>
                                <th class="text-center">الدورات</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center rounded-3"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <span class="fw-bold">{{ $user->full_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-light text-dark border px-3 py-2 fw-bold">{{ $user->id_number }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-info-subtle text-info border px-3">
                                            {{ $user->category->name ?? '---' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill {{ $user->is_admin ? 'bg-primary' : 'bg-success' }} px-3">
                                            {{ $user->is_admin ? 'مسؤول' : 'محفظ' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning-subtle text-warning border px-3">
                                            {{ optional($user->courses)->count() ?? 0 }} دورات
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            @if (!$user->is_admin)
                                                <button
                                                    class="btn btn-sm btn-outline-info rounded-circle action-btn course-btn"
                                                    data-bs-toggle="modal" data-bs-target="#courseUserModal"
                                                    data-user-id="{{ $user->id }}"
                                                    data-user-name="{{ $user->full_name }}"
                                                    data-user-courses="{{ json_encode($user->courses->pluck('id')) }}"
                                                    title="إدارة الدورات">
                                                    <i class="bi bi-journal-plus"></i>
                                                </button>
                                            @endif

                                            <a href="{{ route('teachers.show', $user->id) }}"
                                                class="btn btn-sm btn-outline-primary rounded-circle action-btn"
                                                title="عرض">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <button class="btn btn-sm btn-outline-warning rounded-circle action-btn"
                                                data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}"
                                                title="تعديل">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <form action="{{ route('user.destroy', $user->id) }}" method="POST"
                                                id="deleteForm{{ $user->id }}" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDelete({{ $user->id }})"
                                                    class="btn btn-sm btn-outline-danger rounded-circle action-btn"
                                                    title="حذف">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @include('users.edit_modal', ['user' => $user])
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال إدارة الدورات بتصميم صفحة الطلاب --}}
    <div class="modal fade" id="courseUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form id="courseForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-info text-white border-0 py-3">
                        <h5 class="modal-title fw-bold"><i class="bi bi-book-half ms-2"></i>دورات المستخدم: <span
                                id="modal_user_name"></span></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <input type="hidden" name="update_courses_only" value="1">
                        <div class="row g-2">
                            @foreach ($all_courses as $course)
                                <div class="col-6">
                                    <div class="form-check p-2 bg-light rounded border d-flex align-items-center">
                                        <input class="form-check-input course-checkbox ms-2" type="checkbox"
                                            name="courses[]" value="{{ $course->id }}"
                                            id="user_course_{{ $course->id }}">
                                        <label class="form-check-label fw-bold w-100"
                                            for="user_course_{{ $course->id }}">{{ $course->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-info text-white px-5 fw-bold rounded-pill">حفظ
                            التغييرات</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- 1. مكتبات jQuery و Bootstrap --}}
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    {{-- 2. مكتبات DataTables الأساسية --}}
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap4.js"></script>

    {{-- 3. مكتبات الأزرار وتصدير الإكسل (ضرورية لظهور الزر) --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script>
        // دالة تأكيد الحذف باستخدام SweetAlert2
        function confirmDelete(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف هذا المستخدم نهائياً!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'نعم، احذف!',
                cancelButtonText: 'تراجع',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm' + id).submit();
                }
            });
        }

        $(document).ready(function() {
            // تهيئة الجدول مع تفعيل الأزرار
            if (!$.fn.dataTable.isDataTable('#usersTable')) {
                $('#usersTable').DataTable({
                    "language": {
                        "sProcessing": "جاري التحميل...",
                        "sLengthMenu": "أظهر _MENU_ مستخدمين",
                        "sSearch": "بحث:",
                        "sInfo": "عرض _START_ إلى _END_ من أصل _TOTAL_ مستخدم",
                        "oPaginate": {
                            "sPrevious": "السابق",
                            "sNext": "التالي"
                        }
                    },
                    // تعريف أماكن العناصر: f=بحث، B=أزرار، l=عدد الصفوف
                    "dom": "<'row mb-3'<'col-md-4 text-right'l><'col-md-4 text-center'B><'col-md-4 text-left'f>>" +
                        "<'row'<'col-12'tr>>" +
                        "<'row mt-3'<'col-12 d-flex justify-content-between align-items-center'ip>>",
                    "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-earmark-excel-fill ms-1"></i> تصدير إكسل',
                        className: 'btn btn-excel', // يستخدم التنسيق المعرف في الـ CSS الخاص بك
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4] // تصدير كل الأعمدة عدا عمود "الإجراءات"
                        }
                    }]
                });
            }

            // عرض رسائل النجاح
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'تمت العملية بنجاح',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#0dcaf0',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // منطق مودال الدورات
            $(document).on('click', '.course-btn', function() {
                const userId = $(this).data('user-id');
                const userName = $(this).data('user-name');
                const userCourses = $(this).data('user-courses');

                $('#courseForm').attr('action', "{{ url('user') }}/" + userId);
                $('#modal_user_name').text(userName);

                $('.course-checkbox').prop('checked', false);

                if (Array.isArray(userCourses)) {
                    userCourses.forEach(id => {
                        $(`#user_course_${id}`).prop('checked', true);
                    });
                }
            });
        });
    </script>
@endpush
