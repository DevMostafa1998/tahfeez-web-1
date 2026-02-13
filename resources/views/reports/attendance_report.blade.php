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

    <script>
        $(document).ready(function() {
            let table = null;

            /**
             * تهيئة DataTable بنظام Server-side مع تفعيل البحث السريع
             */
            function initDataTable() {
                table = $('#attendanceTable').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true, // تم التفعيل للسماح بالبحث السريع
                    ajax: {
                        url: "{{ route('reports.attendance.data') }}",
                        type: 'GET',
                        data: function(d) {
                            d.date_from = $('#date_from').val();
                            d.date_to = $('#date_to').val();
                            d.teacher_id = $('[name="teacher_id"]').val();
                            d.group_id = $('select[name="group_id"]').val();
                            d.student_id = $('select[name="student_id"]').val();
                        }
                    },
                    columns: [{
                            data: 'attendance_date',
                            name: 'attendance_date'
                        },
                        {
                            data: 'student_name',
                            name: 'student_name',
                            orderable: false,

                            className: 'fw-bold text-dark'
                        },
                        {
                            data: 'student_id_number',
                            name: 'student_id_number',
                            orderable: false
                        },
                        {
                            data: 'student_phone',
                            name: 'student_phone',
                            orderable: false
                        },
                        {
                            data: 'status',
                            name: 'status',
                            render: function(data) {
                                let badgeClass = '';
                                if (data === 'حاضر') badgeClass = 'bg-success';
                                else if (data === 'غائب') badgeClass = 'bg-danger';
                                else badgeClass = 'bg-warning text-dark';

                                return `<span class="badge ${badgeClass}">${data}</span>`;
                            }
                        }
                    ],

                    language: {
                        "sProcessing": "جاري التحميل...",
                        "sLengthMenu": "عرض _MENU_ سجلات",
                        "sZeroRecords": "لم يتم العثور على سجلات مطابقة",
                        "sInfo": "عرض من _START_ إلى _END_ من أصل _TOTAL_ سجل",
                        "sInfoEmpty": "عرض 0 إلى 0 من أصل 0 سجل",
                        "sInfoFiltered": "(تمت التصفية من إجمالي _MAX_ سجل)",
                        "sSearch": "بحث سريع:",
                        "paginate": {
                            "first": "«",
                            "last": "»",
                            "next": "›",
                            "previous": "‹"
                        }
                    },
                    // f: تعني Search filter، ستظهر الآن في الأعلى جهة اليسار
                    dom: "<'row mb-3'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'B>>" +
                        "<'row'<'col-sm-12' <'table-responsive' tr> >>" +
                        "<'row mt-3'<'col-sm-12'p>>" +
                        "<'row'<'col-sm-12 text-center'i>>",
                    pageLength: 10
                });

                // إضافة زر التصدير المخصص
                $('#excel_button_container').html(`
                    <button type="button" id="btnExportExcel" class="btn btn-excel">
                        <i class="bi bi-file-earmark-excel"></i> تصدير إكسل (الكل)
                    </button>
                `);
            }

            /**
             * دالة المزامنة للفلاتر (Ajax)
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
                        if (changedElement !== 'teacher_id') {
                            updateSelect('teacher_id', response.teachers, 'id', 'full_name', response
                                .selected_teacher_id);
                        }
                        if (changedElement !== 'group_id') {
                            updateSelect('group_id', response.groups, 'id', 'GroupName', groupId);
                        }
                        if (changedElement !== 'student_id') {
                            updateSelect('student_id', response.students, 'id', 'full_name', studentId);
                        }
                    }
                });
            }

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

            // تفعيل الجدول عند التحميل
            initDataTable();

            /**
             * تحديث الجدول عند تغيير الفلاتر العلوية
             */
            $(document).on('change', '.filter-input', function() {
                let name = $(this).attr('name');
                if (['group_id', 'teacher_id', 'student_id'].includes(name)) {
                    syncFilters(name);
                }
                table.draw();
            });

            /**
             * تصدير البيانات بناءً على الفلاتر الحالية
             */
            $(document).on('click', '#btnExportExcel', function() {
                let queryData = {
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val(),
                    teacher_id: $('[name="teacher_id"]').val(),
                    group_id: $('select[name="group_id"]').val(),
                    student_id: $('select[name="student_id"]').val(),
                    search: {
                        value: $('.dataTables_filter input').val()
                    } // تضمين نص البحث في التصدير
                };

                let queryString = $.param(queryData);
                window.location.href = "{{ route('reports.attendance.export') }}?" + queryString;
            });
        });
    </script>
@endpush
