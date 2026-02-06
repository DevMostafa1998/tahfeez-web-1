@extends('layouts.app')

@section('content')
<div class="container-fluid p-4" dir="rtl text-right">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3 class="fw-bold text-primary">تقرير بيانات المحفظين والدورات</h3>
        <div id="excel_button_container"></div>
    </div>

    {{-- الفلاتر --}}
    @if(auth()->user()->is_admin)
    <div class="card shadow-sm border-0 mb-4 no-print" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form id="filterForm">
                <div class="row g-3 text-start align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">تصفية حسب المحفظ</label>
                        <select name="teacher_id" id="teacher_id" class="form-select filter-input">
                            <option value="">-- عرض الجميع --</option>
                            @foreach($teachers as $teacher)
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
                            <th>اسم المحفظ</th>
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
    <style>
        table th, table td { white-space: nowrap; font-size: 0.85rem; padding: 10px !important; }
        .btn-excel { background-color: #1d6f42 !important; color: white !important; font-weight: bold !important; border-radius: 5px; }
        .dataTables_filter { text-align: left !important; margin-bottom: 10px; }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap4.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function() {
    function fetchReports() {
        let tId = $('#teacher_id').val() || '';

        if ($.fn.DataTable.isDataTable('#teachersTable')) {
            $('#teachersTable').DataTable().destroy();
        }

        $('#tableBody').html('<tr><td colspan="15" class="text-center py-4">جاري التحميل...</td></tr>');

        $.ajax({
            url: "{{ route('reports.teachers_courses') }}",
            type: 'GET',
            data: { teacher_id: tId },
            success: function(response) {
                let html = '';
                if (response && response.length > 0) {

                    let groupedData = {};

                    $.each(response, function(i, item) {
                        let key = item.id_number; // نستخدم رقم الهوية كمفتاح فريد للمحفظ

                        if (!groupedData[key]) {
                            // إذا كان المحفظ غير موجود في القائمة نقوم بإضافته
                            groupedData[key] = {
                                ...item,
                                courses: [] // مصفوفة لتخزين أسماء الدورات
                            };
                            if (item.course_name) {
                                groupedData[key].courses.push(item.course_name);
                            }
                        } else {
                            if (item.course_name && !groupedData[key].courses.includes(item.course_name)) {
                                groupedData[key].courses.push(item.course_name);
                            }
                        }
                    });

                    // بناء صفوف الجدول من البيانات المدمجة
                    $.each(groupedData, function(key, item) {
                        let residencyStatus = item.is_displaced == 1
                            ? '<span class="badge bg-warning text-dark">نازح</span>'
                            : '<span class="badge bg-success text-white">مقيم</span>';

                    let separator = ' <span style="color: #ff8c00; font-weight: 900; margin: 0 8px;">-</span> ';
                    let coursesList = item.courses.length > 0 ? item.courses.join(separator) : 'لا يوجد';
                    html += `<tr>
                        <td class="fw-bold">${item.full_name || '-'}</td>
                        <td>${item.id_number || '-'}</td>
                        <td>${item.date_of_birth || '-'}</td>
                        <td>${item.birth_place || '-'}</td>
                        <td>${item.phone_number || '-'}</td>
                        <td>${item.wallet_number || '-'}</td>
                        <td>${item.whatsapp_number || '-'}</td>
                        <td>${item.qualification || '-'}</td>
                        <td>${item.specialization || '-'}</td>
                        <td>${item.parts_memorized || '0'}</td>
                        <td>${item.mosque_name || '-'}</td>
                        <td>${item.address || '-'}</td>
                        <td>${residencyStatus}</td>
                        <td class="text-success fw-bold">${coursesList}</td>
                    </tr>`;
                });

                $('#tableBody').html(html);
            } else {
                $('#tableBody').html('<tr><td colspan="15" class="py-5 text-center text-muted">لا توجد بيانات</td></tr>');
            }
            initDataTable();
        },
        error: function() {
            $('#tableBody').html('<tr><td colspan="15" class="py-5 text-center text-danger">حدث خطأ أثناء تحميل البيانات</td></tr>');
        }
    });
}

function initDataTable() {
        let today = new Date().toISOString().slice(0, 10);

        if ($.fn.DataTable.isDataTable('#teachersTable')) {
            $('#teachersTable').DataTable().destroy();
        }

        let table = $('#teachersTable').DataTable({
            "dom": "<'row'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'B>>rtip",
            "language": {
                "sSearch": "بحث سريع:",
                "sInfo": "إجمالي: _TOTAL_"
            },
            "buttons": [{
                extend: 'excelHtml5',
                text: 'تصدير إكسل',
                className: 'btn-excel',
                title: 'تقرير دورات المحفظين الشامل',
                filename: 'تقرير_دورات_المحفظين_' + today
            }]
        });

        $('#excel_button_container').empty();
        table.buttons().container().appendTo('#excel_button_container');
    }

    $('#teacher_id').on('change', function() {
        fetchReports();
    });
    fetchReports();
});
</script>
@endpush
