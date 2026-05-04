@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    /* التنسيق العام لجدول البيانات */
    .dataTables_wrapper .row:first-child {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between !important;
        align-items: center;
        width: 100%;
        margin: 0 0 1rem 0;
        padding: 0 15px;
    }

    /* ألوان الجنس (ذكر/أنثى) داخل الجدول */
    .bg-blue-subtle {
        background-color: #e7f1ff !important;
        color: #0d6efd !important;
    }

    .bg-pink-subtle {
        background-color: #fff0f3 !important;
        color: #d63384 !important;
    }

    /* أزرار العمليات داخل الجدول */
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

    /* زر تصدير الإكسيل */
    .btn-excel {
        background-color: #1d6f42 !important;
        color: white !important;
        border-radius: 8px !important;
        padding: 5px 15px !important;
        font-weight: bold !important;
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
        border: none;
    }

    /* التنسيق الأساسي لأزرار التبويبات (حاليين / أرشيف) */
    .btn-tab-custom {
        background-color: #ffffff;
        font-weight: 600;
        transition: all 0.3s ease;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
    }

    /* تثبيت اللون الأزرق لزر المستخدمين الحاليين */
    #btn-current {
        color: #0d6efd !important;
        border-color: #0d6efd !important;
    }

    /* تثبيت اللون الأحمر لزر الأرشيف */
    #btn-archived {
        color: #dc3545 !important;
        border-color: #dc3545 !important;
    }

    /* حالة الزر النشط (تأثير تكبير الإطار باستخدام الظل) */
    .active-tab {
        background-color: #ffffff !important;
        border-width: 2px !important;
        z-index: 1;
    }

    /* ظل أزرق عند تفعيل زر الحاليين */
    #btn-current.active-tab {
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25) !important;
    }

    /* ظل أحمر عند تفعيل زر الأرشيف */
    #btn-archived.active-tab {
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.25) !important;
    }

    /* زر إضافة جديد */
    .btn-add-new {
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-add-new:hover {
        background-color: #0b5ed7;
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
    }

    /* تحسين شكل التنسيق عند تمرير الماوس فوق الأزرار غير النشطة */
    .btn-tab-custom:hover:not(.active-tab) {
        background-color: #f8f9fa;
        transform: translateY(-1px);
    }

    /* التجاوب مع الجوال */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start !important;
        }
        .dataTables_wrapper .row:first-child {
            flex-direction: column !important;
            gap: 10px;
        }
    }
</style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    <div class="container-fluid p-3" dir="rtl">
        <div class="page-header d-flex justify-content-between align-items-center mb-3 px-3 flex-wrap">

            <div class="d-flex align-items-center gap-2">
                <div class="bg-white p-1 rounded-2 shadow-sm d-flex align-items-center justify-content-center border"
                    style="width: 40px; height: 40px;">
                    <i class="bi bi-people-fill fs-4 text-primary"></i>
                </div>
                <h1 class="page-title m-0 h4 fw-bold text-primary">إدارة المستخدمين</h1>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="header-btns-wrapper d-flex gap-2">
                    <button type="button" id="btn-current" class="btn btn-tab-custom active-tab">
                        <i class="bi bi-person-check-fill ms-1"></i> المستخدمين الحاليين
                    </button>

                    <button type="button" id="btn-archived" class="btn btn-tab-custom text-danger">
                        <i class="bi bi-trash3-fill ms-1"></i> الأرشيف
                    </button>
                </div>

                <div class="vr mx-2 text-muted opacity-25 d-none d-md-block" style="height: 30px;"></div>

                <a href="{{ route('user.create') }}" class="btn btn-primary btn-add-new shadow-sm px-4 py-2">
                    <i class="bi bi-plus-lg ms-1"></i> مستخدم جديد
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="p-3">
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
    </div>

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
                    <div class="modal-body p-4 text-start">
                        <input type="hidden" name="update_courses_only" value="1">
                        <div class="row g-2">
                            @foreach ($all_courses as $course)
                                <div class="col-6 mb-2">
                                    <div class="p-2 bg-light rounded border d-flex align-items-center justify-content-start">
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
                        <button type="submit" class="btn btn-info text-white px-5 rounded-pill fw-bold">حفظ التغييرات</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap4.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script>
        let showArchived = false;

        $(document).ready(function() {
            var table = $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('user.index') }}",
                    type: 'GET',
                    data: function(d) {
                        d.archived = showArchived; // إرسال حالة الأرشيف للسيرفر
                    }
                },
                columns: [
                    { data: 'full_name', name: 'full_name', className: 'text-start ps-4' },
                    { data: 'id_number', name: 'id_number' },
                    { data: 'phone_number', name: 'phone_number' },
                    { data: 'gender', name: 'gender' },
                    { data: 'category', name: 'category' },
                    { data: 'role', name: 'role' },
                    { data: 'courses_count', name: 'courses_count', orderable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                language: {
                    "sProcessing": "جاري التحميل...",
                    "sLengthMenu": "أظهر _MENU_ سجلات",
                    "sSearch": "بحث سريع:",
                    "sInfo": "عرض _START_ إلى _END_ من أصل _TOTAL_ مستخدم",
                    "paginate": { "next": "›", "previous": "‹" }
                },
                dom: "<'row mb-3 align-items-center'<'col-md-4 text-right'l><'col-md-4 text-center'B><'col-md-4 text-left'f>>" +
                     "<'row'<'col-sm-12' <'table-responsive' tr> >>" +
                     "<'row mt-3'<'col-sm-12'p>>",
                buttons: [{
                    text: '<i class="bi bi-file-earmark-excel-fill ms-1"></i> تصدير إكسل (الكل)',
                    className: 'btn-excel',
                    action: function() {
                        window.location.href = "{{ route('user.export.excel') }}";
                    }
                }]
            });

            // تبديل التبويبات
           $('#btn-current').on('click', function() {
    showArchived = false;
    $(this).addClass('active-tab');
    $('#btn-archived').removeClass('active-tab');
    table.ajax.reload();
});

$('#btn-archived').on('click', function() {
    showArchived = true;
    $(this).addClass('active-tab');
    $('#btn-current').removeClass('active-tab');
    table.ajax.reload();
});

            // معالجة التنبيهات
            @if (session('success'))
                Swal.fire({ icon: 'success', title: 'تم بنجاح', text: "{{ session('success') }}", confirmButtonColor: '#0dcaf0', timer: 2500 });
            @endif
        });

        // دالة الحذف (النقل للأرشيف)
        function confirmDelete(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم نقل المستخدم إلى الأرشيف!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'تراجع',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = $(`<form action="{{ url('user') }}/${id}" method="POST">@csrf @method('DELETE')</form>`);
                    $('body').append(form);
                    form.submit();
                }
            });
        }

        // دالة الاستعادة (Restore)
        function restoreUser(id) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "سيتم استعادة هذا المحفظ إلى القائمة النشطة!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'نعم، استعادة!',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            // توجيه المتصفح إلى رابط الاستعادة الذي عرفناه في الـ web.php
            window.location.href = "{{ url('user/restore') }}/" + id;
        }
    });
}

        // دالة التعديل
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
                if (user.date_of_birth) $('#edit_date_of_birth').val(user.date_of_birth.substring(0, 10));

                if (user.is_admin == 1) {
                    $('#professional_section_wrapper').hide();
                    $('#edit_is_admin').val("1");
                } else {
                    $('#professional_section_wrapper').show();
                    $('#edit_is_admin').val("0");
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
            });
        }

        // مودال الدورات
        $(document).on('click', '.course-btn', function() {
            const userId = $(this).data('user-id');
            const userName = $(this).data('user-name');
            const userCourses = $(this).data('user-courses');
            $('#courseForm').attr('action', "{{ url('user') }}/" + userId);
            $('#course_user_name').text(userName);
            $('.course-checkbox').prop('checked', false);
            if (Array.isArray(userCourses)) {
                userCourses.forEach(id => { $(`#user_course_${id}`).prop('checked', true); });
            }
            $('#courseUserModal').modal('show');
        });
    </script>
@endpush
