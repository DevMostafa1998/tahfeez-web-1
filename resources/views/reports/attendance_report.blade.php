@extends('layouts.app')

@section('content')
    <div class="container-fluid p-4" dir="rtl text-right">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h3 class="fw-bold text-primary">تقرير حضور وغياب الطلاب</h3>
            <div id="excel_button_container"></div>
        </div>

        {{-- بطاقة الفلاتر --}}
        <div class="card shadow-sm border-0 mb-4 no-print" style="border-radius: 15px;">
            <div class="card-body p-4">
                <form id="filterForm">
                    <div class="row g-3 text-start">
                        <div class="col-md-2">
                            <label class="form-label fw-bold">من تاريخ <span class="text-danger">*</span></label>
                            <input type="date" name="date_from" id="date_from"
                                value="{{ request('date_from', date('Y-m-d')) }}" class="form-control filter-input"
                                required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">إلى تاريخ <span class="text-danger">*</span></label>
                            <input type="date" name="date_to" id="date_to"
                                value="{{ request('date_to', date('Y-m-d')) }}" class="form-control filter-input" required>
                        </div>

                        @if (auth()->user()->is_admin)
                            <div class="col-md-3">
                                <label class="form-label fw-bold">اسم المحفظ/ة</label>
                                <select name="teacher_id" class="form-select filter-input">
                                    <option value="">-- الكل --</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">المجموعة</label>
                                <select name="group_id" class="form-select filter-input">
                                    <option value="">-- الكل --</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->GroupName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="teacher_id" value="{{ auth()->id() }}">
                            <div class="col-md-5">
                                <label class="form-label fw-bold">اختر من مجموعاتك</label>
                                <select name="group_id" class="form-select filter-input">
                                    <option value="">-- كل مجموعاتي --</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->GroupName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-md-3">
                            <label class="form-label fw-bold">اسم الطالب/ة</label>
                            <select name="student_id" class="form-select filter-input">
                                <option value="">-- الكل --</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table id="attendanceTable" class="table table-bordered table-hover align-middle text-center m-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="py-3">التاريخ</th>
                                <th class="py-3">اسم الطالب/ة</th>
                                <th class="py-3">رقم الهوية</th>
                                <th class="py-3">رقم الهاتف</th>
                                <th class="py-3">الحالة</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            {{-- سيتم ملء البيانات عبر AJAX --}}
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
        .btn-excel {
            background-color: #1d6f42 !important;
            color: white !important;
            border-radius: 8px !important;
            padding: 8px 20px !important;
            font-weight: bold !important;
            border: none !important;
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
        }

        .dataTables_filter {
            display: none;
        }

        .badge-present {
            background-color: #28a745;
            color: white;
        }

        .badge-absent {
            background-color: #dc3545;
            color: white;
        }

        .badge-excused {
            background-color: #ffc107;
            color: black;
        }
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

            /**
             * دالة المزامنة الكاملة:
             * تقوم بتحديث كافة القوائم المنسدلة بناءً على العنصر الذي تم تغييره.
             */
            function syncFilters(changedElement) {
                let teacherId = $('[name="teacher_id"]').val();
                let groupId = $('select[name="group_id"]').val();
                let studentId = $('select[name="student_id"]').val();

                $.ajax({
                    url: "{{ route('reports.filters.data') }}",
                    type: 'GET',
                    data: {
                        teacher_id: teacherId,
                        group_id: groupId,
                        student_id: studentId,
                        changed_element: changedElement
                    },
                    success: function(response) {
                        // للأدمن: تحديث قائمة المحفظين (إلا إذا كان هو من قام بتغيير المحفظ يدوياً)
                        if (changedElement !== 'teacher_id') {
                            updateSelect('teacher_id', response.teachers, 'id', 'full_name', response
                                .selected_teacher_id);
                        }

                        // تحديث قائمة المجموعات
                        if (changedElement !== 'group_id') {
                            updateSelect('group_id', response.groups, 'id', 'GroupName', groupId);
                        }

                        // تحديث قائمة الطلاب
                        if (changedElement !== 'student_id') {
                            updateSelect('student_id', response.students, 'id', 'full_name', studentId);
                        }
                    }
                });
            }
            /**
             * دالة مساعدة لتحديث خيارات الـ Select
             */
            function updateSelect(name, items, valueKey, textKey, currentValue = null) {
                let select = $(`select[name="${name}"]`);
                if (select.length === 0) return;

                select.empty().append('<option value="">-- الكل --</option>');
                $.each(items, function(i, item) {
                    let selected = (item[valueKey] == currentValue) ? 'selected' : '';
                    select.append(
                        `<option value="${item[valueKey]}" ${selected}>${item[textKey]}</option>`);
                });
            }

            /**
             * دالة جلب بيانات الحضور والغياب للجدول
             */
            function fetchAttendance() {
                let dateFrom = $('#date_from').val();
                let dateTo = $('#date_to').val();

                if (!dateFrom || !dateTo) {
                    $('#tableBody').html(
                        '<tr><td colspan="5" class="text-center py-4 text-danger fw-bold">يرجى تحديد التاريخ (من وإلى) لعرض البيانات</td></tr>'
                    );
                    return;
                }

                let data = {
                    date_from: dateFrom,
                    date_to: dateTo,
                    teacher_id: $('[name="teacher_id"]').val(),
                    group_id: $('select[name="group_id"]').val(),
                    student_id: $('select[name="student_id"]').val(),
                };

                if ($.fn.DataTable.isDataTable('#attendanceTable')) {
                    $('#attendanceTable').DataTable().clear().destroy();
                }

                $('#tableBody').html(
                    '<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm"></div> جاري تحميل البيانات...</td></tr>'
                );

                $.ajax({
                    url: "{{ route('reports.attendance.data') }}",
                    type: 'GET',
                    data: data,
                    success: function(response) {
                        let html = '';
                        if (response && response.length > 0) {
                            // ... (بقية الكود الخاص بملء الجدول كما هو)
                            $.each(response, function(index, item) {
                                let statusBadge = '';
                                if (item.status === 'حاضر') statusBadge =
                                    '<span class="badge bg-success">حاضر</span>';
                                else if (item.status === 'غائب') statusBadge =
                                    '<span class="badge bg-danger">غائب</span>';
                                else statusBadge =
                                    '<span class="badge bg-warning text-dark">مستأذن</span>';

                                html += `<tr>
                        <td>${item.attendance_date || '-'}</td>
                        <td class="fw-bold text-dark">${item.student_name || '-'}</td>
                        <td>${item.student_id_number || '-'}</td>
                        <td>${item.student_phone || '-'}</td>
                        <td>${statusBadge}</td>
                    </tr>`;
                            });
                            $('#tableBody').html(html);
                            initDataTable();
                        } else {
                            $('#tableBody').html(
                                '<tr><td colspan="6" class="py-5 text-center text-muted fw-bold">لا توجد سجلات حضور مطابقة للبحث</td></tr>'
                            );
                        }
                    }
                });
            }

            function initDataTable() {
                table = $('#attendanceTable').DataTable({
                    "dom": "<'row mb-3'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'B>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row mt-3'<'col-sm-12 d-flex justify-content-between align-items-center'ip>>",
                    "language": {
                        "sSearch": "بحث سريع:",
                        "sLengthMenu": "عرض _MENU_ سجلات",
                        "sInfo": "عرض من _START_ إلى _END_ من أصل _TOTAL_ سجل",
                        "sInfoEmpty": "عرض 0 إلى 0 من أصل 0 سجل",
                        "sInfoFiltered": "(تمت التصفية من إجمالي _MAX_ سجل)",
                        "sZeroRecords": "لم يتم العثور على سجلات مطابقة",
                        "sEmptyTable": "لا توجد بيانات متاحة في الجدول",
                        "paginate": {
                            "first": "«",
                            "last": "»",
                            "next": "›",
                            "previous": "‹"
                        }
                    },
                    "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-earmark-excel"></i> تصدير إكسل',
                        className: 'btn-excel',
                        title: 'تقرير حضور الطلاب - ' + new Date().toLocaleDateString('ar-EG')
                    }]
                });

                $('#excel_button_container').empty();
                table.buttons().container().appendTo('#excel_button_container');
            }

            $(document).on('change', '.filter-input', function() {
                let name = $(this).attr('name');

                if (name === 'group_id' || name === 'teacher_id' || name === 'student_id') {
                    syncFilters(name);
                }

                fetchAttendance();
            });

            fetchAttendance();
        });
    </script>
@endpush
