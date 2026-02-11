@extends('layouts.app')

@section('title', 'إدارة الطلاب')

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

        .bg-blue-subtle {
            background-color: #e7f1ff !important;
            color: #0d6efd !important;
        }

        .bg-pink-subtle {
            background-color: #fff0f3 !important;
            color: #d63384 !important;
        }

        #studentsTable {
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

        .dataTables_filter {
            text-align: left !important;
        }

        .dataTables_length {
            text-align: right !important;
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

        /* تحسين استجابة الجدول على الشاشات الصغيرة */
        @media (max-width: 768px) {

            /* جعل الهيدر عمودياً */
            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start !important;
            }

            /* ضبط أزرار الـ DataTables لتكون تحت بعضها أو متراصة بشكل أفضل */
            .dataTables_wrapper .row:first-child {
                flex-direction: column !important;
                gap: 10px;
            }

            .dataTables_length,
            .dataTables_filter,
            .dt-buttons {
                text-align: center !important;
                width: 100%;
            }

            /* جعل الجدول يدعم التمرير الأفقي بوضوح */
            .card-table .p-3 {
                padding: 5px !important;
            }

            /* تصغير حجم الخط والأيقونات في الأكشن */
            .action-btn {
                width: 30px;
                height: 30px;
            }

            /* تحسين شكل المودال على الجوال */
            .modal-dialog {
                margin: 10px;
            }

            .col-6 {
                width: 100% !important;
                /* جعل خيارات الدورات تأخذ العرض الكامل */
            }
        }

        /* حل مشكلة تداخل الأزرار في الجدول */
        .table-responsive {
            border: none;
        }

        /* تنسيق إضافي لزر الإكسل ليكون بعرض كامل على الجوال */
        @media (max-width: 576px) {
            .btn-excel {
                width: 100%;
                justify-content: center;
            }
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
                    <i class="bi bi-person-badge-fill fs-3 text-primary"></i>
                </div>
                <div>
                    <h1 class="page-title m-0 h3">إدارة الطلاب</h1>
                </div>
            </div>
            <a href="{{ route('student.create') }}"
                class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 rounded-3">
                <i class="bi bi-plus-lg"></i><span>طالب جديد</span>
            </a>
        </div>

        <div class="card card-table shadow-sm border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="p-3">
                    <div class="table-responsive">
                        <table id="studentsTable" class="table table-striped table-bordered align-middle mb-0"
                            style="width:100%">
                            <thead class="bg-light text-secondary text-center">
                                <tr>
                                    <th>اسم الطالب/ة</th>
                                    <th>رقم الهوية</th>
                                    <th>الجنس</th>
                                    <th>الحالة</th>
                                    <th>الدورات</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                        {{-- ضع المودال خارج الحلقة (أخرجناه من الـ foreach) --}}
                        @include('students.edit_modal_unified')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال إدارة الدورات --}}
    <div class="modal fade" id="courseStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form id="courseForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-info text-white border-0 py-3">
                        <h5 class="modal-title fw-bold"><i class="bi bi-book-half ms-2"></i>دورات الطالب: <span
                                id="modal_student_name"></span></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <input type="hidden" name="update_courses_only" value="1">
                        <div class="row g-2">
                            @foreach (\DB::table('courses')->whereIn('type', ['students', null])->orWhereNull('type')->get() as $course)
                                <div class="col-6 mb-2">
                                    <div class="p-2 bg-light rounded border d-flex align-items-center justify-content-start"
                                        style="cursor: pointer;">
                                        <input class="form-check-input course-checkbox m-0" type="checkbox" name="courses[]"
                                            value="{{ $course->id }}" id="student_course_{{ $course->id }}"
                                            style="position: relative; margin-left: 10px !important;">

                                        <label class="form-check-label fw-bold mb-0 flex-grow-1 cursor-pointer"
                                            for="student_course_{{ $course->id }}"
                                            style="text-align: right; padding-right: 10px;">
                                            {{ $course->name }}
                                        </label>
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
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap4.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script>
        // 1. دالة حذف الطالب (استخدام SweetAlert2)
        function confirmDelete(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف بيانات الطالب نهائياً!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'تراجع',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // إنشاء فورم وهمي للحذف لإرسال طلب DELETE
                    let form = $(`<form action="{{ url('student') }}/${id}" method="POST">
                        @csrf
                        @method('DELETE')
                    </form>`);
                    $('body').append(form);
                    form.submit();
                }
            });
        }

        $(document).ready(function() {
            const d = new Date();
            const dateString = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();

            // 2. تهيئة DataTable بنظام Server-side
            const table = $('#studentsTable').DataTable({
                processing: true,
                serverSide: true, // تفعيل المعالجة من طرف السيرفر
                ajax: {
                    url: "{{ route('student.index') }}",
                    type: 'GET',
                    error: function(xhr, error, thrown) {
                        console.error("حدث خطأ في جلب البيانات:", error);
                    }
                },
                columns: [{
                        data: 'full_name',
                        name: 'full_name',
                        className: 'text-right'
                    },
                    {
                        data: 'id_number',
                        name: 'id_number',
                        className: 'text-center'
                    },
                    {
                        data: 'gender',
                        name: 'gender',
                        className: 'text-center'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center'
                    },
                    {
                        data: 'courses',
                        name: 'courses',
                        className: 'text-center'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [0, 'asc']
                ], // الترتيب الافتراضي حسب الاسم
                language: {
                    "sProcessing": "جاري التحميل...",
                    "sLengthMenu": "أظهر _MENU_ طلاب",
                    "sSearch": "بحث سريع:",
                    "sInfo": "عرض _START_ إلى _END_ من أصل _TOTAL_ طالب",
                    "paginate": {
                        "first": "«",
                        "last": "»",
                        "next": "›",
                        "previous": "‹"
                    }
                },
                dom: "<'row mb-3 align-items-center'<'col-md-4 text-right'l><'col-md-4 text-center'B><'col-md-4 text-left'f>>" +
                    "<'row'<'col-sm-12' <'table-responsive' tr> >>" +
                    "<'row mt-3'<'col-sm-12'p>>" +
                    "<'row'<'col-sm-12 text-center'i>>",
                buttons: [{
                    text: '<i class="bi bi-file-earmark-excel-fill ms-1"></i> تصدير إكسل (كامل البيانات)',
                    className: 'btn btn-excel',
                    action: function(e, dt, node, config) {
                        window.location.href = "{{ route('student.export') }}";
                    }
                }]
            });

            // 3. فتح مودال التعديل وجلب بيانات الطالب عبر AJAX
            $(document).on('click', '.edit-student-btn', function() {
                const id = $(this).data('id');
                const modal = $('#unifiedEditModal');
                const form = $('#editStudentForm');
                form[0].reset();

                $.ajax({
                    url: `{{ url('student-data') }}/${id}`,
                    type: 'GET',
                    success: function(student) {
                        form.attr('action', `{{ url('student') }}/${id}`);
                        $('#edit_full_name').val(student.full_name);
                        $('#edit_id_number').val(student.id_number);
                        $('#edit_birth_place').val(student.birth_place);
                        $('#edit_gender').val(student.gender);
                        $('#edit_phone_number').val(student.phone_number);
                        $('#edit_whatsapp_number').val(student.whatsapp_number);
                        $('#edit_address').val(student.address);
                        $('#edit_center_name').val(student.center_name);
                        $('#edit_mosque_name').val(student.mosque_name);
                        $('#edit_mosque_address').val(student.mosque_address);
                        if (student.date_of_birth) {
                            let formattedDate = student.date_of_birth.substring(0, 10);
                            $('#edit_date_of_birth').val(formattedDate);
                        }

                        $('#edit_is_displaced option').each(function() {
                            if ($(this).val() == student.is_displaced) {
                                $(this).prop('selected', true);
                            }
                        });
                        modal.modal('show');
                    },
                    error: function() {
                        Swal.fire('خطأ', 'تعذر جلب بيانات الطالب', 'error');
                    }
                });
            });

            // 4. فتح مودال إدارة الدورات
            $(document).on('click', '.course-btn', function() {
                const studentId = $(this).data('id');
                const studentName = $(this).data('name');
                const modal = $('#courseStudentModal');

                // تحديث واجهة المودال
                $('#courseForm').attr('action', `{{ url('student') }}/${studentId}`);
                $('#modal_student_name').text(studentName);
                $('.course-checkbox').prop('checked', false);

                // جلب الدورات الحالية للطالب
                $.get(`{{ url('student-courses') }}/${studentId}`, function(courseIds) {
                    courseIds.forEach(id => {
                        $(`#student_course_${id}`).prop('checked', true);
                    });
                    modal.modal('show');
                });
            });

            // 5. رسائل النجاح (من الـ Session)
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#0dcaf0',
                    timer: 2500
                });
            @endif
        });
    </script>
@endpush
