@extends('layouts.app')

@section('title', 'إدارة المجموعات')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="{{ asset('assets/css/user_table.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
    <style>
        /* التنسيقات العامة للجدول */
        .dataTables_wrapper .row:first-child {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center;
            width: 100%;
            margin: 0 0 1rem 0;
            padding: 0 15px;
        }

        #groupsTable {
            width: 100% !important;
            margin: 0 !important;
        }

        /* تحسين شكل الجدول على الجوال */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            /* جعل الأزرار تأخذ العرض الكامل */
            .btn-primary {
                width: 100%;
                justify-content: center;
            }

            /* تحويل عناصر التحكم في الجدول (البحث والعدد) لتكون تحت بعضها */
            .dataTables_wrapper .row:first-child {
                flex-direction: column !important;
                gap: 10px;
            }

            .dataTables_length,
            .dataTables_filter {
                text-align: center !important;
                width: 100%;
            }

            .btn-excel {
                margin: 10px 0 !important;
                width: 100%;
                justify-content: center;
            }

            /* جعل الجدول قابل للتمرير عرضياً بشكل أفضل */
            .card-table {
                border-radius: 0;
                margin: 0 -1.5rem;
                /* إزالة الهوامش الجانبية للاستفادة من المساحة */
            }

            .table thead {
                display: none;
                /* إخفاء الرأس في الشاشات الصغيرة جداً إذا كنت تفضل عرض الكروت */
            }

            /* اختيارياً: تصغير الخطوط في الجوال */
            .table td {
                font-size: 0.85rem;
                padding: 10px 5px !important;
            }
        }

        select.custom-select {
            direction: ltr !important;
            text-align: center !important;
            background-image: none !important;
            appearance: menulist !important;
            -webkit-appearance: menulist !important;
            padding: 4px 30px 4px 10px !important;
        }

        .dataTables_wrapper .row:first-child {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center;
            width: 100%;
            margin: 0 0 1rem 0;
            padding: 0 15px;
            overflow-x: hidden !important;
        }

        #groupsTable {
            width: 100% !important;
            margin: 0 !important;
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
            border: none !important;
            margin-left: 10px !important;
            padding: 5px 15px !important;
            font-weight: bold !important;
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
        }

        .btn-excel:hover {
            background-color: #155231 !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@section('content')
    <div class="container-fluid p-4" dir="rtl">
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'تمت العملية',
                    text: "{{ session('success') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            </script>
        @endif

        {{-- الهيدر --}}
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-3 shadow-sm">
                    <i class="bi bi-people-fill fs-3 text-primary"></i>
                </div>
                <div>
                    <h1 class="page-title m-0 h3">إدارة المجموعات</h1>
                </div>
            </div>
            @if (auth()->check() && auth()->user()->is_admin)
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 rounded-3"
                    data-bs-toggle="modal" data-bs-target="#createGroupModal">
                    <i class="bi bi-plus-lg"></i><span>مجموعة جديدة</span>
                </button>
            @endif
        </div>

        {{-- جدول البيانات --}}
        <div class="card card-table shadow-sm border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="groupsTable" class="table table-striped table-bordered align-middle mb-0" style="width:100%">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="text-center">اسم المجموعة</th>
                                <th class="text-center">اسم المحفظ/ة</th>
                                <th class="text-center">تاريخ الإنشاء</th>
                                <th class="text-center">عدد الطلاب</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groups as $group)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center rounded-3"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-collection-fill"></i>
                                            </div>
                                            <span class="fw-bold">{{ $group->GroupName }}</span>
                                        </div>
                                    </td>
                                    {{-- باقي الأعمدة... --}}
                                    <td class="text-center">
                                        <span class="fw-medium">{{ $group->teacher->full_name ?? 'غير محدد' }}</span>
                                    </td>
                                    <td class="text-center text-muted small">{{ $group->creation_at }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info-subtle text-info border px-3">
                                            {{ $group->students_count }} طلاب
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('attendance.index', ['group_id' => $group->id]) }}"
                                                class="btn btn-action text-warning" title="تسجيل الحضور والغياب">
                                                <i class="bi bi-calendar-check-fill"></i>
                                            </a>

                                            <a href="{{ route('group.show', $group->id) }}" class="btn btn-action text-info"
                                                title="عرض الطلاب">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>

                                            @if (auth()->check() && auth()->user()->is_admin)
                                                <button class="btn btn-action text-success" data-bs-toggle="modal"
                                                    data-bs-target="#manageStudents{{ $group->id }}"
                                                    title="إدارة الطلاب">
                                                    <i class="bi bi-people-fill"></i>
                                                </button>
                                                <button class="btn btn-action text-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editGroup{{ $group->id }}" title="تعديل">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <form action="{{ route('group.destroy', $group->id) }}" method="POST"
                                                    id="deleteForm{{ $group->id }}" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-action text-danger"
                                                        onclick="confirmDelete({{ $group->id }})" title="حذف">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                @include('groups.edit_modal')
                                @include('groups.manage_students_modal')
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
    {{-- مودال عرض تفاصيل المجموعة --}}
    <div class="modal fade" id="viewGroup{{ $group->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-warning text-white border-0" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-info-circle me-2"></i> تفاصيل المجموعة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- معلومات المجموعة الأساسية --}}
                    <div class="text-center mb-4">
                        <h4 class="text-primary fw-bold mb-1">{{ $group->GroupName }}</h4>
                        <span class="badge bg-light text-secondary border">بإشراف المحفظ:
                            {{ $group->teacher->full_name ?? 'غير محدد' }}</span>
                    </div>

                    <hr class="text-muted opacity-25">

                    {{-- قائمة الطلاب --}}
                    <h6 class="fw-bold mb-3"><i class="bi bi-people me-2"></i> الطلاب المسجلون
                        ({{ $group->students->count() }})</h6>
                    <div class="list-group list-group-flush rounded-3 border overflow-auto" style="max-height: 250px;">
                        @forelse($group->students as $index => $student)
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <span class="text-dark">{{ $index + 1 }}. {{ $student->full_name }}</span>
                                @if ($student->date_of_birth)
                                    <span class="badge bg-success-subtle text-success border-0 fw-normal">
                                        {{ \Carbon\Carbon::parse($student->date_of_birth)->age }} سنة
                                    </span>
                                @endif
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted small py-3">
                                لا يوجد طلاب مسجلين في هذه المجموعة حالياً
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary px-4 rounded-3"
                        data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
    {{-- استدعاء مودال الإضافة خارج الحلقة --}}
    @include('groups.create_modal')

@endsection

@push('scripts')
    {{-- استدعاء مكتبة DataTables --}}
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap4.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    {{-- <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap4.js"></script> --}}

    <script>
        // دالة تصفية الطلاب داخل المودال
        function filterStudents(input, listId) {
            let filter = input.value.toLowerCase();
            let items = document.getElementById(listId).getElementsByClassName('student-item');
            for (let item of items) {
                let text = item.innerText.toLowerCase();
                item.style.display = text.includes(filter) ? "" : "none";
            }
        }

        // دالة تأكيد الحذف باستخدام SweetAlert
        function confirmDelete(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف المجموعة نهائياً!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'تراجع',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('deleteForm' + id).submit();
            });
        }

        $(document).ready(function() {
            // 1. تفعيل DataTable مع الإعدادات العربية (اليقظة والذكاء)
            let table = $('#groupsTable').DataTable({
                "order": [
                    [2, "desc"]
                ],
                "responsive": true, // تفعيل الاستجابة

                "language": {
                    "sProcessing": "جاري التحميل...",
                    "sLengthMenu": "أظهر _MENU_ مجموعات",
                    "sZeroRecords": "لم يعثر على أية سجلات",
                    "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                    "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
                    "sSearch": "بحث:",
                    "oPaginate": {
                        "sFirst": "الأول",
                        "sPrevious": "السابق",
                        "sNext": "التالي",
                        "sLast": "الأخير"
                    }
                },
                // إعدادات الأزرار
                "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel"></i> تصدير إكسل',
                    className: 'btn-excel',
                    title: 'قائمة مجموعات مركز التحفيظ - ' + new Date().toLocaleDateString('ar-EG'),
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                }],
                "dom": "<'row mb-4 align-items-center'<'col-md-4 text-right'l><'col-md-4 text-center'B><'col-md-4 text-left'f>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row mt-4 align-items-center'<'col-md-6 text-right'i><'col-md-6 d-flex justify-content-end'p>>",
                "columnDefs": [{
                    "orderable": false,
                    "targets": 4
                }]
            });

            // 2. معالجة نموذج إنشاء مجموعة جديدة عبر AJAX
            $('#createGroupForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let submitBtn = form.find('button[type="submit"]');
                let formData = form.serialize();

                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // إغلاق المودال وتصفير الفورم
                            $('#createGroupModal').modal('hide');
                            form[0].reset();

                            // تنبيه النجاح
                            Swal.fire({
                                icon: 'success',
                                title: 'تمت العملية',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).text('حفظ');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            Swal.fire('خطأ!', Object.values(errors)[0][0], 'error');
                        }
                    }
                });
            });
        });
    </script>
@endpush
