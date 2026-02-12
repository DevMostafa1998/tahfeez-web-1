@extends('layouts.app')

@section('content')
    <div class="container-fluid p-4" dir="rtl text-right">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h3 class="fw-bold text-primary">تقرير التسميع للطلاب</h3>
            {{-- حاوية زر الإكسل المخصص --}}
            <div id="excel_button_container">
                <button id="btnExportExcel" class="btn btn-excel">
                    <i class="bi bi-file-earmark-excel"></i> تصدير إكسل (الكل)
                </button>
            </div>
        </div>

        {{-- بطاقة الفلاتر --}}
        <div class="card shadow-sm border-0 mb-4 no-print" style="border-radius: 15px;">
            <div class="card-body p-4">
                <form id="filterForm">
                    <div class="row g-3 text-start">
                        <div class="col-md-2">
                            <label class="form-label fw-bold">من تاريخ <span class="text-danger">*</span></label>
                            <input type="date" name="date_from" id="date_from"
                                value="{{ request('date_from', date('Y-m-d')) }}" class="form-control filter-input">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">إلى تاريخ <span class="text-danger">*</span></label>
                            <input type="date" name="date_to" id="date_to"
                                value="{{ request('date_to', date('Y-m-d')) }}" class="form-control filter-input">
                        </div>

                        @if (auth()->user()->is_admin)
                            <div class="col-md-2">
                                <label class="form-label fw-bold">اسم المحفظ/ة</label>
                                <select name="teacher_id" class="form-select filter-input">
                                    <option value="">-- الكل --</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-md-2">
                            <label class="form-label fw-bold">المجموعة</label>
                            <select name="group_id" class="form-select filter-input">
                                <option value="">-- الكل --</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->GroupName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
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

        {{-- جدول البيانات --}}
        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table id="reportsTable" class="table table-bordered table-hover align-middle text-center m-0 w-100">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="py-3">التاريخ</th>
                                <th class="py-3">اسم الطالب/ة</th>
                                <th class="py-3">رقم الهوية</th>
                                <th class="py-3">المجموعة</th>
                                <th class="py-3">المحفظ/ة</th>
                                <th class="py-3">السورة</th>
                                <th class="py-3">من آية</th>
                                <th class="py-3">إلى آية</th>
                                <th class="py-3">الملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- يتم الملء بواسطة DataTables Server-side --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
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

        .dataTables_wrapper .dataTables_filter {
            text-align: right !important;
            display: flex;
            justify-content: flex-start;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            justify-content: flex-start !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // 1. تعريف جدول DataTables بنظام Server-side
            let table = $('#reportsTable').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: {
                    url: "{{ route('reports.memorization') }}", // المسار في الـ Controller
                    type: 'GET',
                    data: function(d) {
                        // إرسال الفلاتر مع كل طلب للسيرفر
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                        d.teacher_id = $('select[name="teacher_id"]').val();
                        d.group_id = $('select[name="group_id"]').val();
                        d.student_id = $('select[name="student_id"]').val();
                    }
                },
                columns: [{
                        data: 'recitation_date',
                        name: 'recitation_date'
                    },
                    {
                        data: 'student_name',
                        name: 'student_name',
                        className: 'fw-bold text-dark'
                    },
                    {
                        data: 'student_id_number',
                        name: 'student_id_number'
                    },
                    {
                        data: 'group_name',
                        name: 'group_name',
                        render: function(data) {
                            return `<span class="badge bg-light text-primary border">${data || '-'}</span>`;
                        }
                    },
                    {
                        data: 'teacher_name',
                        name: 'teacher_name'
                    },
                    {
                        data: 'sura_name',
                        name: 'sura_name',
                        className: 'text-success fw-bold'
                    },
                    {
                        data: 'verses_from',
                        name: 'verses_from'
                    },
                    {
                        data: 'verses_to',
                        name: 'verses_to'
                    },
                    {
                        data: 'note',
                        name: 'note',
                        className: 'small text-muted'
                    }
                ],
                dom: "<'row mb-3'<'col-sm-12 col-md-6 text-right'f><'col-sm-12 col-md-6'B>>" +
                    "<'row'<'col-sm-12' <'table-responsive' tr> >>" +
                    "<'row mt-3'<'col-sm-12'p>>" +
                    "<'row'<'col-sm-12 text-center'i>>",
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
                order: [
                    [0, 'desc']
                ] // الترتيب الافتراضي حسب التاريخ
            });

            // 2. معالجة التصدير للإكسل (باستخدام كلاس ExportExcel في السيرفر)
            $(document).on('click', '#btnExportExcel', function(e) {
                e.preventDefault();

                // تجميع الفلاتر الحالية لإرسالها لطلب الإكسل
                let params = {
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val(),
                    teacher_id: $('select[name="teacher_id"]').val(),
                    group_id: $('select[name="group_id"]').val(),
                    student_id: $('select[name="student_id"]').val()
                };

                // بناء الرابط مع الـ Query String
                let url = "{{ route('reports.export') }}?" + $.param(params);

                // فتح رابط التحميل
                window.location.href = url;
            });

            // 3. تحديث الفلاتر التبادلية (Ajax)
            function syncFilters(changedElement) {
                let data = {
                    teacher_id: $('select[name="teacher_id"]').val(),
                    group_id: $('select[name="group_id"]').val(),
                    student_id: $('select[name="student_id"]').val(),
                };

                $.ajax({
                    url: "{{ route('reports.filters.data') }}", //
                    type: 'GET',
                    data: data,
                    success: function(response) {
                        if (changedElement !== 'teacher_id' && $('select[name="teacher_id"]').length) {
                            updateSelect('teacher_id', response.teachers, 'id', 'full_name', data
                                .teacher_id);
                        }
                        if (changedElement !== 'group_id') {
                            updateSelect('group_id', response.groups, 'id', 'GroupName', data.group_id);
                        }
                        if (changedElement !== 'student_id') {
                            updateSelect('student_id', response.students, 'id', 'full_name', data
                                .student_id);
                        }
                    }
                });
            }

            function updateSelect(name, items, valueKey, textKey, currentValue) {
                let select = $(`select[name="${name}"]`);
                select.empty().append('<option value="">-- الكل --</option>');
                $.each(items, function(i, item) {
                    let selected = (item[valueKey] == currentValue) ? 'selected' : '';
                    select.append(
                        `<option value="${item[valueKey]}" ${selected}>${item[textKey]}</option>`);
                });
            }

            // 4. مراقبة التغييرات في الفلاتر
            $(document).on('change', '.filter-input', function() {
                let name = $(this).attr('name');

                // تحديث القوائم المنسدلة بناءً على التغيير
                if (['teacher_id', 'group_id', 'student_id'].includes(name)) {
                    syncFilters(name);
                }

                // إعادة تحميل الجدول من السيرفر (DataTable Draw)
                table.draw();
            });
        });
    </script>
@endpush
