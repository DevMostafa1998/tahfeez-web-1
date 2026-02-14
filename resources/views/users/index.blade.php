@extends('layouts.app')
@section('title', 'إدارة المستخدمين')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

        .bg-blue-subtle {
            background-color: #e7f1ff !important;
            color: #0d6efd !important;
        }

        .bg-pink-subtle {
            background-color: #fff0f3 !important;
            color: #d63384 !important;
        }

        .action-btn {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .id-badge {
            letter-spacing: 1.5px;
            min-width: 110px;
            display: inline-block;
        }

        .modal-xl-custom {
            max-width: 75% !important;
        }

        .compact-body {
            padding: 1.5rem !important;
        }

        .compact-body label {
            font-size: 0.8rem;
            margin-bottom: 4px;
            color: #555;
        }
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
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-striped table-bordered align-middle mb-0 text-center w-100">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="text-start ps-4">اسم المستخدم</th>
                                <th>رقم الهوية</th>
                                <th>رقم الجوال</th>
                                <th>الجنس</th>
                                <th>التصنيف</th>
                                <th>الصلاحية</th>
                                <th>الدورات</th>
                                <th style="width: 150px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال التعديل الديناميكي الموحد --}}
    @include('users.edit_modal_dynamic')

    {{-- مودال إدارة الدورات --}}
    <div class="modal fade" id="courseUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form id="courseForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-info text-white border-0 py-3">
                        <h5 class="modal-title fw-bold">دورات المحفظ: <span id="course_user_name"></span></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="update_courses_only" value="1">
                        <div class="row g-2">
                            @foreach ($all_courses as $course)
                                <div class="col-6 mb-2">
                                    <div class="p-2 bg-light rounded border d-flex align-items-center">
                                        <input class="form-check-input course-checkbox m-0 ms-2" type="checkbox"
                                            name="courses[]" value="{{ $course->id }}"
                                            id="user_course_{{ $course->id }}">
                                        <label class="form-check-label fw-bold mb-0" for="user_course_{{ $course->id }}">
                                            {{ $course->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-info text-white px-5 rounded-pill fw-bold">حفظ
                            التغييرات</button>
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
        $(document).ready(function() {
            var table = $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('user.index') }}", // تأكد أن الـ Controller يتعامل مع طلب الـ Ajax
                columns: [{
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'id_number',
                        name: 'id_number'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'courses_count',
                        name: 'courses_count',
                        orderable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    "sProcessing": "جاري التحميل...",
                    "sLengthMenu": "أظهر _MENU_ سجلات",
                    "sSearch": "بحث سريع:",
                    "sInfo": "عرض _START_ إلى _END_ من أصل _TOTAL_ مستخدم",
                    "paginate": {
                        "first": "«",
                        "last": "»",
                        "next": "›",
                        "previous": "‹"
                    }
                },
                "dom": "<'row mb-3'<'col-md-4 text-right'l><'col-md-4 text-center'B><'col-md-4 text-left d-flex justify-content-end'f>>" +
                    "<'row'<'col-sm-12' <'table-responsive' tr> >>" +
                    "<'row mt-3'<'col-sm-12'p>>" +
                    "<'row'<'col-sm-12 text-center'i>>",
                buttons: [{
                    text: '<i class="bi bi-file-earmark-excel-fill ms-1"></i> تصدير إكسل (الكل)',
                    className: 'btn-excel',
                    action: function(e, dt, node, config) {
                        window.location.href = "{{ route('user.export.excel') }}";
                    }
                }]
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#0dcaf0',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#dc3545'
                });
            @endif
        });


        function editUser(id) {
            $.get("{{ url('user') }}/" + id + "/edit-data", function(user) {
                $('#editUserForm').attr('action', "{{ url('user') }}/" + id);

                $('#edit_full_name').val(user.full_name);
                $('#edit_id_number').val(user.id_number);
                $('#edit_phone_number').val(user.phone_number);
                $('#edit_address').val(user.address);
                $('#edit_gender').val(user.gender);
                $('#edit_category_id').val(user.category_id);
                $('#edit_is_displaced').val(user.is_displaced);

                if (user.date_of_birth) {
                    $('#edit_date_of_birth').val(user.date_of_birth.substring(0, 10));
                }

                if (user.is_admin == 1) {
                    $('#professional_section_wrapper').hide();
                    $('#edit_is_admin').val("1");
                    $('#adminPrivilegeDiv').hide();
                } else {
                    $('#professional_section_wrapper').show();
                    $('#edit_is_admin').val("0");
                    $('#adminPrivilegeDiv').show();

                    $('#edit_whatsapp_number').val(user.whatsapp_number);
                    $('#edit_qualification').val(user.qualification);
                    $('#edit_specialization').val(user.specialization);
                    $('#edit_parts_memorized').val(user.parts_memorized);
                    $('#edit_mosque_name').val(user.mosque_name);
                    $('#edit_wallet_number').val(user.wallet_number);
                    $('#edit_birth_place').val(user.birth_place);
                    $('#edit_is_admin_rouls').prop('checked', user.is_admin_rouls == 1);
                }

                $('#editUserModal').modal('show');
            }).fail(function() {
                Swal.fire('خطأ', 'فشل في جلب بيانات المستخدم', 'error');
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
            $('#courseUserModal').modal('show');
        });

        function confirmDelete(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف هذا المستخدم نهائياً من النظام!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'تراجع',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = "{{ url('user') }}/" + id;
                    form.method = 'POST';
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush
