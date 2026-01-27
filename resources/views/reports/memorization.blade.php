@extends('layouts.app')

@section('content')
<div class="container-fluid p-4" dir="rtl text-right">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3 class="fw-bold text-primary">تقرير التسميع للطلاب</h3>

        <div id="excel_button_container"></div>
    </div>

    {{-- بطاقة الفلاتر --}}
    <div class="card shadow-sm border-0 mb-4 no-print" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form id="filterForm">
                <div class="row g-3 text-start">
                    <div class="col-md-2">
                        <label class="form-label fw-bold">من تاريخ <span class="text-danger">*</span></label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from', date('Y-m-d')) }}" class="form-control filter-input">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">إلى تاريخ <span class="text-danger">*</span></label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to', date('Y-m-d')) }}" class="form-control filter-input">
                    </div>

                    @if(auth()->user()->is_admin)
                    <div class="col-md-2">
                        <label class="form-label fw-bold">المحفظ</label>
                        <select name="teacher_id" class="form-select filter-input">
                            <option value="">-- الكل --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-2">
                        <label class="form-label fw-bold">المجموعة</label>
                        <select name="group_id" class="form-select filter-input">
                            <option value="">-- الكل --</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->GroupName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold">اسم الطالب</label>
                        <select name="student_id" class="form-select filter-input">
                            <option value="">-- الكل --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- جدول البيانات --}}
    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table id="reportsTable" class="table table-bordered table-hover align-middle text-center m-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="py-3">التاريخ</th>
                            <th class="py-3">اسم الطالب</th>
                            <th class="py-3">رقم الهوية</th>
                            <th class="py-3">المجموعة</th>
                            <th class="py-3">المحفظ</th>
                            <th class="py-3">السورة</th>
                            <th class="py-3">من آية</th>
                            <th class="py-3">إلى آية</th>
                            <th class="py-3">الملاحظات</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        {{-- سيتم ملء البيانات هنا عبر AJAX --}}
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
        .btn-excel { background-color: #1d6f42 !important; color: white !important; border-radius: 8px !important; padding: 8px 20px !important; font-weight: bold !important; border: none !important; display: flex !important; align-items: center !important; gap: 5px !important; }
        .dataTables_filter { display: none; }
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
    let table = null;

    function fetchReports() {
        let data = {
            date_from: $('input[name="date_from"]').val(),
            date_to: $('input[name="date_to"]').val(),
            teacher_id: $('select[name="teacher_id"]').val(),
            group_id: $('select[name="group_id"]').val(),
            student_id: $('select[name="student_id"]').val(),
        };

        // 1. تدمير الجدول تماماً قبل البدء بالبحث الجديد لمنع أي تعارض (حل مشكلة الـ Alert)
        if ($.fn.DataTable.isDataTable('#reportsTable')) {
            $('#reportsTable').DataTable().clear().destroy();
        }

        $('#tableBody').html('<tr><td colspan="9" class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm"></div> جاري البحث...</td></tr>');

        $.ajax({
            url: "{{ route('reports.memorization') }}",
            type: 'GET',
            data: data,
            dataType: 'json',
            success: function(response) {
                let html = '';

                if (response && response.length > 0) {
                    $.each(response, function(index, memo) {
                        html += `<tr>
                            <td>${memo.recitation_date || '-'}</td>
                            <td class="fw-bold text-dark">${memo.student_name || '-'}</td>
                            <td>${memo.student_id_number || '-'}</td>
                            <td><span class="badge bg-light text-primary border">${memo.group_name || '-'}</span></td>
                            <td>${memo.teacher_name || '-'}</td>
                            <td class="text-success fw-bold">${memo.sura_name || '-'}</td>
                            <td>${memo.verses_from || '-'}</td>
                            <td>${memo.verses_to || '-'}</td>
                            <td class="small text-muted">${memo.note || '-'}</td>
                        </tr>`;
                    });

                    $('#tableBody').html(html);

                   // 2. تفعيل DataTable فقط في حال وجود بيانات
                    table = $('#reportsTable').DataTable({
                        "dom": "<'row'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'B>>rtip",
                        "language": {
                            "emptyTable": "لا توجد سجلات",
                            "sSearch": "بحث سريع داخل النتائج:",
                            "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ سجل",
                            "sInfoEmpty": "إظهار 0 إلى 0 من أصل 0 سجل",
                            "oPaginate": {
                                "sNext": "التالي",
                                "sPrevious": "السابق"
                            }
                        },
                        "buttons": [{
                            extend: 'excelHtml5',
                            text: '<i class="bi bi-file-earmark-excel"></i> تصدير إكسل',
                            className: 'btn-excel',
                            title: 'تقرير التسميع - ' + new Date().toLocaleDateString('ar-EG').replace(/\//g, '-'),
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] 
                            }
                        }]
                    });

                    // نقل أزرار الإكسل إلى الحاوية المخصصة في الأعلى
                    $('#excel_button_container').empty();
                    table.buttons().container().appendTo('#excel_button_container');

                    } else {
                        // 3. في حال عدم وجود نتائج
                        $('#tableBody').html('<tr><td colspan="9" class="py-5 text-center text-muted fw-bold">عذراً، لا توجد سجلات تسميع لهذا الاختيار</td></tr>');
                        $('#excel_button_container').empty();
                    }
            },
            error: function(xhr) {
                $('#tableBody').html('<tr><td colspan="9" class="text-center text-danger">حدث خطأ أثناء الاتصال بالسيرفر</td></tr>');
            }
        });
    }

    // ربط البحث التلقائي بكل حقول الفلتر
    $(document).on('change', '.filter-input', function() {
        fetchReports();
    });

    // تشغيل البحث عند تحميل الصفحة
    fetchReports();
});
    </script>
@endpush
