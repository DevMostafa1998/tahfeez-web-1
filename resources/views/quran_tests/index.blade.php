@extends('layouts.app')

@section('title', 'سجل اختبارات تسميع القرآن')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* تنسيق صف الفلاتر العلوي داخل الجدول */
        .dataTables_wrapper .row:first-child {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center;
            background: #f8f9fa;
            padding: 10px 15px;
            margin: 0 0 1rem 0 !important;
            border-radius: 8px;
            border: 1px solid #ebedef;
        }

        /* تنسيق حقول التاريخ المصغرة */
        .date-filter-box {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .date-filter-box input {
            width: 130px !important;
            height: 30px !important;
            padding: 2px 8px !important;
            font-size: 0.8rem !important;
            border-radius: 5px;
        }

        /* تصحيح لون خلفية "النوع" */
        .bg-soft-info {
            background-color: #e7f1ff !important;
            color: #0d6efd !important; /* اللون الأزرق */
            border: 1px solid #cfe2ff;
            font-weight: 500;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .btn-excel-top {
            background-color: #1d6f42 !important;
            color: white !important;
            border: none !important;
        }

        .btn-excel-top:hover { background-color: #155a35 !important; }

        /* موازنة المسافات في قائمة عدد الصفوف والبحث */
        .dataTables_length select {
            height: 30px !important;
            padding: 0 5px !important;
            margin: 0 5px;
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: hue-rotate(180deg) brightness(0.8);
            opacity: 1;
        }
        input[type="date"]:focus {
            box-shadow: none !important;
            outline: none !important;
        }
        #testsTable { width: 100% !important; margin: 0 !important; }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4" dir="rtl">
        {{-- الهيدر العلوي --}}
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-3 shadow-sm border">
                    <i class="bi bi-journal-check fs-3 text-primary"></i>
                </div>
                <h1 class="page-title m-0 h4 fw-bold">سجل اختبارات تسميع القرآن</h1>
            </div>

            <div class="d-flex gap-2">
                <button id="exportExcelCustom" class="btn btn-excel-top btn-sm d-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm">
                    <i class="bi bi-file-earmark-excel"></i><span>تصدير إكسل</span>
                </button>

                <a href="{{ route('quran_tests.create') }}"
                    class="btn btn-success btn-sm d-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm">
                    <i class="bi bi-plus-lg"></i><span>إضافة اختبار جديد</span>
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="card-body p-3">
                <div class="table-responsive">
                    <div id="filterContainer" class="d-none">
                        <div class="d-flex align-items-center justify-content-center">

                            <label class="small mx-2">&emsp; فلتر التاريخ من :</label>
                            <input type="date" id="minDate" class="form-control form-control-sm" style="width: 150px; cursor: pointer;">

                            <label class="small mx-2">إلي :</label>
                            <input type="date" id="maxDate" class="form-control form-control-sm" style="width: 150px; cursor: pointer;">

                            <button id="resetDate" class="btn btn-light btn-sm border ms-2">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>

                        </div>
                    </div>

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
                                    <td class="text-center"><span class="badge badge-light border">{{ $test->juz_count }} أجزاء</span></td>
                                    <td class="text-center"><span class="badge bg-soft-info px-3">{{ $test->examType }}</span></td>
                                    <td class="text-center">
                                        <span class="badge {{ $test->result_status == 'ناجح' ? 'bg-success' : 'bg-danger' }} text-white px-3">
                                            {{ $test->result_status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-circle action-btn edit-test-btn"
                                                data-bs-toggle="modal" data-bs-target="#editTestModal" data-id="{{ $test->id }}"
                                                data-student-name="{{ $test->student?->full_name }}" data-date="{{ $test->date->format('Y-m-d') }}"
                                                data-juz="{{ $test->juz_count }}" data-type="{{ $test->examType }}"
                                                data-status="{{ $test->result_status }}" data-note="{{ $test->note }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle action-btn delete-test-btn"
                                                data-id="{{ $test->id }}" data-name="{{ $test->student?->full_name }}">
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
            // محرك فلترة التاريخ
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var min = $('#minDate').val();
                var max = $('#maxDate').val();
                var date = data[1];
                if ((min === "" && max === "") || (min === "" && date <= max) || (min <= date && max === "") || (min <= date && date <= max)) {
                    return true;
                }
                return false;
            });

            var table = $('#testsTable').DataTable({
                "responsive": true,
                "language": {
                    "sSearch": "البحث:",
                    "sLengthMenu": "إظهار _MENU_ سجلات",
                    "sInfo": "عرض _TOTAL_ سجل",
                    "paginate": { "next": "›", "previous": "‹" }
                },
                // إعادة هيكلة DOM لإظهار قائمة عدد الصفوف (l) بجانب الفلتر والبحث (f)
                "dom": "<'row mb-2'<'col-md-auto'l><'col-md-auto' <'#datePlaceholder'>><'col-md'f>>" +
                       "<'row'<'col-sm-12'tr>>" +
                       "<'row mt-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "buttons": [{
                    extend: 'excelHtml5',
                    exportOptions: { columns: [0, 1, 2, 3, 4] }
                }]
            });

            // وضع فلاتر التاريخ في مكانها المخصص داخل الجدول
            $('#datePlaceholder').append($('#filterContainer').contents());
            $('#filterContainer').remove();

            // الفلترة الفورية
            $(document).on('change', '#minDate, #maxDate', function() {
                table.draw();
            });

            // إعادة ضبط التاريخ
            $(document).on('click', '#resetDate', function() {
                $('#minDate, #maxDate').val('');
                table.draw();
            });

            // زر التصدير العلوي
            $('#exportExcelCustom').on('click', function() {
                table.button('.buttons-excel').trigger();
            });

            // منطق الحذف AJAX
            $(document).on('click', '.delete-test-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف سجل ${name}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'حذف',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/quran_tests/${id}`,
                            type: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function() {
                                Swal.fire('تم الحذف!', '', 'success').then(() => location.reload());
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
