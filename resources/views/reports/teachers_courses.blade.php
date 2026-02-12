@extends('layouts.app')

@section('content')
    <div class="container-fluid p-4" dir="rtl text-right">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h3 class="fw-bold text-primary">تقرير بيانات المحفظين والدورات</h3>
            <div id="excel_button_container"></div>
        </div>

        {{-- الفلاتر --}}
        @if (auth()->user()->is_admin)
            <div class="card shadow-sm border-0 mb-4 no-print" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <form id="filterForm">
                        <div class="row g-3 text-start align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">تصفية حسب المحفظ</label>
                                <select name="teacher_id" id="teacher_id" class="form-select filter-input">
                                    <option value="">-- عرض الجميع --</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- الجدول --}}
        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table id="teachersTable" class="table table-bordered table-hover align-middle text-center m-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>اسم المحفظ/ة</th>
                                <th>رقم الهوية</th>
                                <th>تاريخ الميلاد</th>
                                <th>مكان الميلاد</th>
                                <th>رقم الجوال</th>
                                <th>رقم المحفظة</th>
                                <th>رقم الواتساب</th>
                                <th>المؤهل</th>
                                <th>التخصص</th>
                                <th>المحفوظ</th>
                                <th>المسجد</th>
                                <th>العنوان</th>
                                <th>الحالة</th>
                                <th>اسم الدورة</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/student_report.css') }}">

    <style>
        #teachersTable {
            border-collapse: separate !important;
            border-spacing: 0;
            border: 1px solid #dee2e6 !important;
            border-radius: 15px;
            overflow: hidden;
        }

        #teachersTable th,
        #teachersTable td {
            border-left: 1px solid #f5f8fb !important;
            border-bottom: 1px solid #dce2e9 !important;
        }

        #teachersTable th:first-child,
        #teachersTable td:first-child {
            border-left: none !important;
        }

        #teachersTable tr:last-child td {
            border-bottom: none !important;
        }

        #teachersTable thead th {
            background-color: white;
            color: black;
            border-top: none !important;
        }

        .table-responsive {
            padding-left: 1px;
        }

        .dataTables_wrapper {
            display: flex;
            flex-direction: column;
        }

        .table-responsive-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {

            .dataTables_wrapper .row:first-child {
                position: sticky;
                left: 0;
                z-index: 10;
                background: white;
                width: 100%;
            }

            .dataTables_paginate,
            .dataTables_info {
                position: relative;
                left: 0 !important;
                width: 100% !important;
                text-align: center !important;
                display: block !important;
            }

            .dataTables_paginate .pagination {
                justify-content: center !important;
            }

            .dataTables_wrapper .row:last-child {
                background: #fdfdfd;
                padding: 10px 0;
                border-top: 1px solid #eee;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap4.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            let table = $('#teachersTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('reports.teachers_courses') }}",
                    "type": "GET",
                    "data": function(d) {
                        d.teacher_id = $('#teacher_id').val();
                    }
                },
                "columns": [{
                        "data": "full_name",
                        "defaultContent": "-"
                    },
                    {
                        "data": "id_number",
                        "defaultContent": "-"
                    },
                    {
                        "data": "date_of_birth",
                        "defaultContent": "-"
                    },
                    {
                        "data": "birth_place",
                        "defaultContent": "-"
                    },
                    {
                        "data": "phone_number",
                        "defaultContent": "-"
                    },
                    {
                        "data": "wallet_number",
                        "defaultContent": "-"
                    },
                    {
                        "data": "whatsapp_number",
                        "defaultContent": "-"
                    },
                    {
                        "data": "qualification",
                        "defaultContent": "-"
                    },
                    {
                        "data": "specialization",
                        "defaultContent": "-"
                    },
                    {
                        "data": "parts_memorized",
                        "defaultContent": "0"
                    },
                    {
                        "data": "mosque_name",
                        "defaultContent": "-"
                    },
                    {
                        "data": "address",
                        "defaultContent": "-"
                    },
                    {
                        "data": "is_displaced",
                        "render": function(data) {
                            return data == 1 ?
                                '<span class="badge bg-warning text-dark">نازح</span>' :
                                '<span class="badge bg-success text-white">مقيم</span>';
                        }
                    },
                    {
                        "data": "course_name",
                        "className": "text-success fw-bold",
                        "defaultContent": "لا يوجد"
                    }
                ],
                "dom": "<'row mb-3'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6 text-end'B>>" +
                    "<'row'<'col-sm-12 table-responsive-container' tr >>" +
                    "<'row mt-3'<'col-sm-12'p>>" +
                    "<'row'<'col-sm-12 text-center'i>>",
                "language": {
                    "sProcessing": "جاري التحميل...",
                    "sSearch": "بحث سريع:",
                    "sInfo": "إجمالي السجلات: _TOTAL_",
                    "sInfoEmpty": "إجمالي السجلات: 0",
                    "sZeroRecords": "لم يتم العثور على نتائج مطابقة",
                    "sEmptyTable": "لا توجد بيانات متاحة في الجدول",
                    paginate: {
                        first: "«",
                        last: "»",
                        next: "›",
                        previous: "‹"
                    },
                },
                "buttons": [{
                    text: '<i class="fas fa-file-excel me-1"></i> تصدير إكسل (الكل)',
                    className: 'btn btn-success fw-bold btn-excel',
                    action: function(e, dt, node, config) {
                        // جلب الفلاتر الحالية
                        let teacherId = $('#teacher_id').val() || '';
                        let searchQuery = dt.search() || '';

                        let exportUrl = "{{ route('reports.teachers_courses') }}?export=1" +
                            "&teacher_id=" + teacherId +
                            "&search_value=" + encodeURIComponent(searchQuery);

                        window.location.href = exportUrl;
                    }
                }],
                "pageLength": 10,
                "ordering": true,
                "searching": true
            });

            $('#teacher_id').on('change', function() {
                table.draw();
            });

            table.buttons().container().appendTo('#excel_button_container');
        });
    </script>
@endpush
