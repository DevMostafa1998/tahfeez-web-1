@extends('layouts.app')

@section('title', 'سجل اختبارات تسميع القرآن')

@push('css')
    {{-- استيراد نفس مكتبات التنسيق من الملف المرجعي --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

    <style>
        /* التنسيقات الموحدة من الملف الآخر */
        .dataTables_wrapper .row:first-child {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center;
            width: 100%;
            margin: 0 0 1rem 0;
            padding: 0 15px;
        }

        #testsTable {
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

        .bg-soft-info {
            background-color: #e7f1ff !important;
            color: #0d6efd !important;
        }

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
@endpush

@section('content')
    <div class="container-fluid p-4" dir="rtl">
        {{-- الهيدر الموحد بنفس الستايل --}}
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-3 shadow-sm">
                    <i class="bi bi-journal-check fs-3 text-primary"></i>
                </div>
                <div>
                    <h1 class="page-title m-0 h3">سجل اختبارات تسميع القرآن</h1>
                </div>
            </div>
            <a href="{{ route('quran_tests.create') }}"
                class="btn btn-success d-flex align-items-center gap-2 px-4 py-2 rounded-3 shadow-sm">
                <i class="bi bi-plus-lg"></i><span>إضافة اختبار جديد</span>
            </a>
        </div>

        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table id="testsTable" class="table table-striped table-bordered align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="text-center">اسم الطالب/ة</th>
                                <th class="text-center">التاريخ</th>
                                <th class="text-center">الأجزاء</th>
                                <th class="text-center">النوع</th>
                                <th class="text-center">النتيجة</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tests as $test)
                                <tr>
                                    <td class="ps-4 fw-bold text-start">{{ $test->student?->full_name }}</td>
                                    <td class="text-center">{{ $test->date->format('Y-m-d') }}</td>
                                    <td class="text-center"><span class="badge badge-light border">{{ $test->juz_count }}
                                            أجزاء</span></td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-info px-3">{{ $test->examType }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge {{ $test->result_status == 'ناجح' ? 'bg-success' : 'bg-danger' }} text-white px-3">
                                            {{ $test->result_status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            {{-- زر التعديل --}}
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary rounded-circle action-btn edit-test-btn"
                                                data-bs-toggle="modal" data-bs-target="#editTestModal"
                                                data-id="{{ $test->id }}" data-student="{{ $test->studentId }}"
                                                data-student-name="{{ $test->student?->full_name }}"
                                                data-date="{{ $test->date->format('Y-m-d') }}"
                                                data-juz="{{ $test->juz_count }}" data-type="{{ $test->examType }}"
                                                data-status="{{ $test->result_status }}" data-note="{{ $test->note }}"
                                                title="تعديل">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            {{-- زر الحذف --}}
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger rounded-circle action-btn delete-test-btn"
                                                data-id="{{ $test->id }}" data-name="{{ $test->student?->full_name }}"
                                                title="حذف">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('quran_tests.edit_modal')
@endsection

@push('scripts')
    {{-- استيراد مكتبات JS اللازمة لـ DataTables --}}
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap4.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // إعداد DataTables بنفس إعدادات الملف الآخر
            var d = new Date();
            var dateString = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();

            if (!$.fn.dataTable.isDataTable('#testsTable')) {
                $('#testsTable').DataTable({
                    "responsive": true,
                    "language": {
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
                    "dom": "<'row mb-3 align-items-center'<'col-md-4 text-right'l><'col-md-4 text-center'B><'col-md-4 text-left'f>>" +
                        "<'row'<'col-sm-12' <'table-responsive' tr> >>" +
                        "<'row mt-3'<'col-sm-12'p>>" +
                        "<'row'<'col-sm-12 text-center'i>>",
                    "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-earmark-excel-fill ms-1"></i> تصدير إكسل',
                        className: 'btn btn-excel',
                        title: 'سجل الاختبارات - ' + dateString,
                        filename: 'سجل_الاختبارات_' + dateString,
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    }]
                });
            }

            // --- منطق AJAX الخاص بالتعديل والحذف (من ملفك الأصلي) ---
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on('click', '.edit-test-btn', function() {
                const id = $(this).data('id');
                $('#editTestForm').attr('action', `/quran_tests/${id}`);
                $('#edit_student_name_display').val($(this).data('student-name'));
                $('#edit_studentId').val($(this).data('student'));
                $('#edit_date').val($(this).data('date'));
                $('#edit_juz_count').val($(this).data('juz'));
                $('#edit_examType').val($(this).data('type'));
                $('#edit_result_status').val($(this).data('status'));
                $('#edit_note').val($(this).data('note'));
            });

            $('#editTestForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#editTestModal').modal('hide');
                        Swal.fire({
                                icon: 'success',
                                title: 'تم التحديث!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            })
                            .then(() => location.reload());
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $('.is-invalid').removeClass('is-invalid');
                            $.each(errors, function(key, value) {
                                let input = $(`[name="${key}"]`);
                                input.addClass('is-invalid').after(
                                    `<div class="invalid-feedback d-block">${value[0]}</div>`
                                );
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.delete-test-btn', function() {
                const id = $(this).data('id');
                const studentName = $(this).data('name');
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف سجل اختبار "${studentName}" نهائياً!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/quran_tests/${id}`,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire({
                                        icon: 'success',
                                        title: 'تم الحذف!',
                                        timer: 1500,
                                        showConfirmButton: false
                                    })
                                    .then(() => location.reload());
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
