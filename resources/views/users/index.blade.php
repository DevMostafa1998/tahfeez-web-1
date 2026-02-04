@extends('layouts.app')
@section('title', 'إدارة المستخدمين')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

    <style>
        .dataTables_wrapper .row:first-child {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding: 0 15px;
        }
        .btn-excel {
            background-color: #1d6f42 !important;
            color: white !important;
            border-radius: 8px !important;
            padding: 6px 15px !important;
            font-weight: bold !important;
            border: none;
        }
        .action-btn {
            width: 34px; height: 34px;
            display: flex; align-items: center; justify-content: center;
            transition: transform 0.2s;
        }
        .action-btn:hover { transform: scale(1.1); }
        .cursor-pointer { cursor: pointer; }

        /* تنسيق الأرقام والهوية */
        .id-badge {
            letter-spacing: 1.5px;
            min-width: 110px;
            display: inline-block;
        }

        /* تحسين شكل خانة البحث وعدد الصفوف */
        .dataTables_filter input { border-radius: 20px; padding: 5px 15px; border: 1px solid #ddd; }
        .custom-select { border-radius: 8px !important; height: auto !important; padding: 5px !important; }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4" dir="rtl">

        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-primary fw-bold mb-0">
                <i class="bi bi-people-fill me-2"></i>إدارة المستخدمين
            </h1>
            <a href="{{ route('user.create') }}" class="btn btn-primary px-4 shadow-sm fw-bold rounded-pill">
                <i class="bi bi-plus-lg ms-1"></i> مستخدم جديد
            </a>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
    <div class="card-body p-0">
        <div class="p-3">
            <div class="table-responsive">
                <table id="usersTable" class="table table-striped table-bordered align-middle mb-0 text-center">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="text-start ps-4">اسم المستخدم</th>
                            <th class="text-center">رقم الهوية</th> <th class="text-center">رقم الجوال</th> <th>التصنيف</th>
                            <th>الصلاحية</th>
                            <th>الدورات</th>
                            <th style="width: 150px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="text-start ps-4">
                                    <strong>{{ $user->full_name }}</strong>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-4 py-2 fw-bold fs-7 id-badge"
                                          style="display: inline-block; min-width: 110px;">
                                        {{ $user->id_number }}
                                    </span>
                                </td>

                                <td dir="ltr" class="fw-bold fs-6 text-primary-emphasis text-center">
                                    {{ $user->phone_number }}
                                </td>

                                <td>
                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info px-3 py-1" style="font-size: 0.85rem;">
                                        {{ $user->category->name ?? '---' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge rounded-pill {{ $user->is_admin ? 'bg-primary' : 'bg-success' }} px-3 py-1" style="font-size: 0.85rem;">
                                        {{ $user->is_admin ? 'مسؤول' : 'محفظ' }}
                                    </span>
                                </td>

                                <td>
                                    @if (!$user->is_admin)
                                        <span class="badge bg-warning text-dark rounded-pill shadow-sm px-3"
                                              style="padding-top: 5px; padding-bottom: 5px; font-size: 0.85rem;">
                                            {{ $user->courses->count() }} دورات
                                        </span>
                                    @else
                                        <span class="text-muted small">--</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @if (!$user->is_admin)
                                            <button class="btn btn-sm btn-outline-info rounded-circle action-btn course-btn"
                                                data-bs-toggle="modal" data-bs-target="#courseUserModal"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->full_name }}"
                                                data-user-courses="{{ json_encode($user->courses->pluck('id')) }}"
                                                title="إدارة الدورات">
                                                <i class="bi bi-journal-plus"></i>
                                            </button>
                                        @else
                                            <div style="width: 34px;"></div>
                                        @endif

                                        <a href="{{ route('teachers.show', $user->id) }}"
                                           class="btn btn-sm btn-outline-primary rounded-circle action-btn" title="عرض">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <button class="btn btn-sm btn-outline-warning rounded-circle action-btn"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" title="تعديل">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('user.destroy', $user->id) }}" method="POST" id="deleteForm{{ $user->id }}" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete({{ $user->id }})"
                                                class="btn btn-sm btn-outline-danger rounded-circle action-btn" title="حذف">
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

    </div>

    {{-- مودال إدارة الدورات المطور --}}
    <div class="modal fade" id="courseUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form id="courseForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-info text-white border-0 py-3">
                        <h5 class="modal-title fw-bold">دورات المحفظ: <span id="course_user_name"></span></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <input type="hidden" name="update_courses_only" value="1">
                        <div class="row g-2">
                            @foreach ($all_courses as $course)
                                <div class="col-6 mb-2">
                                    <div class="p-2 bg-light rounded border d-flex align-items-center justify-content-start cursor-pointer">
                                        <input class="form-check-input course-checkbox m-0" type="checkbox" name="courses[]"
                                            value="{{ $course->id }}" id="user_course_{{ $course->id }}"
                                            style="position: relative; margin-left: 10px !important;">
                                        <label class="form-check-label fw-bold cursor-pointer mb-0 flex-grow-1"
                                               for="user_course_{{ $course->id }}" style="text-align: right; padding-right: 5px;">
                                            {{ $course->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-info text-white px-5 rounded-pill fw-bold">حفظ التغييرات</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap4.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
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
        var d = new Date();
        var dateString = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();

        if (!$.fn.dataTable.isDataTable('#usersTable')) {
            $('#usersTable').DataTable({
                "pageLength": 10,
                "language": {
                    "sProcessing": "جاري التحميل...",
                    "sLengthMenu": "أظهر _MENU_ سجلات",
                    "sSearch": "بحث سريع:",
                    "sInfo": "عرض _START_ إلى _END_ من أصل _TOTAL_ مستخدم",
                    "oPaginate": { "sPrevious": "السابق", "sNext": "التالي" }
                },
                "dom": "<'row mb-3'<'col-md-4 text-right'l><'col-md-4 text-center'B><'col-md-4 text-left d-flex justify-content-end'f>>t<'row'<'col-12 d-flex justify-content-between'ip>>",
                "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel-fill ms-1"></i> تصدير إكسل',
                    className: 'btn-excel',
                    title: 'بيانات المستخدمين - تاريخ ' + dateString,
                    filename: 'تقرير_المستخدمين_' + dateString,
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                }]
            });
        }

            $(document).on('click', '.course-btn', function() {
                const userId = $(this).data('user-id');
                const userName = $(this).data('user-name');
                const userCourses = $(this).data('user-courses');

                $('#courseForm').attr('action', "{{ url('user') }}/" + userId);
                $('#course_user_name').text(userName);
                $('.course-checkbox').prop('checked', false);

                if (Array.isArray(userCourses)) {
                    userCourses.forEach(id => {
                        $(`#user_course_${id}`).prop('checked', true);
                    });
                }
            });
        });
        @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#0dcaf0',
                    timer: 2500
                });
            @endif
    </script>
@endpush
