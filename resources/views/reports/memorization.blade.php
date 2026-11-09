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
                        @if (auth()->user()->is_admin)
                            <!-- الفلتر المضاف حديثاً: الجنس -->
                            <div class="col-md-2">
                                <label class="form-label fw-bold">الجنس</label>
                                <select name="gender" id="gender" class="form-select filter-input">
                                    <option value="">-- الكل --</option>
                                    <option value="male">ذكر</option>
                                    <option value="female">أنثى</option>
                                </select>
                            </div>
                        @endif
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
                                <th class="py-3">حالة التسميع</th>
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
                    url: "{{ route('reports.memorization') }}",
                    type: 'GET',
                    data: function(d) {
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                        d.teacher_id = $('select[name="teacher_id"]').val();
                        d.group_id = $('select[name="group_id"]').val();
                        d.student_id = $('select[name="student_id"]').val();
                        d.gender = $('#gender').val(); // تمرير فلتر الجنس المضاف حديثاً للسيرفر
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
                        data: 'examType_id',
                        name: 'examType_id',
                        render: function(data, type, row) {
                            let examTypeId = data;
                            let noteText = row.note || '';
                            let suraName = row.sura_name || '';

                            if (examTypeId == 0 || examTypeId === '0' || noteText.includes(
                                    'لم يقم بالتسميع اليوم') || suraName === 'لم يسمع') {
                                return `<span class="badge bg-danger-subtle text-danger border px-3 py-1 fw-bold" style="border-radius: 8px;">
                            <i class="bi bi-x-circle ms-1"></i> لَمْ يُسَمِّعْ
                        </span>`;
                            }

                            let typeText = row.exam_type_description || row.exam_type_name || row
                                .description || '';

                            if (!typeText || typeText.trim() === '') {
                                if (noteText.includes('مراجعة')) {
                                    typeText = 'مراجعة';
                                } else if (noteText.includes('سرد') || noteText.includes(
                                        'اختبار')) {
                                    typeText = 'سرد سورة';
                                } else if (examTypeId == 1 || examTypeId === '1') {
                                    typeText = 'تسميع';
                                } else if (examTypeId == 2 || examTypeId === '2') {
                                    typeText = 'مراجعة';
                                } else if (examTypeId == 3 || examTypeId === '3') {
                                    typeText = 'سرد سورة';
                                } else {
                                    typeText = 'تسميع';
                                }
                            }

                            let badgeColor = 'bg-primary-subtle text-primary border-primary';

                            if (typeText.includes('مراجعة')) {
                                badgeColor = 'bg-warning-subtle text-warning border-warning';
                            } else if (typeText.includes('سرد') || typeText.includes('اختبار')) {
                                badgeColor = 'bg-success-subtle text-success border-success';
                            }

                            return `<span class="badge ${badgeColor} border px-3 py-1 fw-bold" style="border-radius: 8px;">
                        <i class="bi bi-check-circle ms-1"></i> ${typeText}
                    </span>`;
                        }
                    },
                    {
                        data: 'sura_name',
                        name: 'sura_name',
                        className: 'text-success fw-bold',
                        render: function(data, type, row) {
                            let noteText = row.note || '';
                            let examTypeId = row.examType_id;
                            if (data === 'لم يسمع' || examTypeId == 0 || noteText.includes(
                                    'لم يقم بالتسميع اليوم')) {
                                return '';
                            }
                            return data || '';
                        }
                    },
                    {
                        data: 'verses_from',
                        name: 'verses_from',
                        render: function(data, type, row) {
                            let noteText = row.note || '';
                            let examTypeId = row.examType_id;
                            if (row.sura_name === 'لم يسمع' || examTypeId == 0 || noteText.includes(
                                    'لم يقم بالتسميع اليوم')) {
                                return '';
                            }
                            return (data == 0 || !data) ? '' : data;
                        }
                    },
                    {
                        data: 'verses_to',
                        name: 'verses_to',
                        render: function(data, type, row) {
                            let noteText = row.note || '';
                            let examTypeId = row.examType_id;
                            if (row.sura_name === 'لم يسمع' || examTypeId == 0 || noteText.includes(
                                    'لم يقم بالتسميع اليوم')) {
                                return '';
                            }
                            return (data == 0 || !data) ? '' : data;
                        }
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
                ]
            });

            // 2. معالجة التصدير للإكسل وتشمل فلتر الجنس
            $(document).on('click', '#btnExportExcel', function(e) {
                e.preventDefault();
                let params = {
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val(),
                    teacher_id: $('select[name="teacher_id"]').val(),
                    group_id: $('select[name="group_id"]').val(),
                    student_id: $('select[name="student_id"]').val(),
                    gender: $('#gender').val() // إضافة متغير الجنس لرابط الإكسل
                };
                window.location.href = "{{ route('reports.export') }}?" + $.param(params);
            });

            // 3. تحديث الفلاتر التبادلية (Ajax)
            function syncFilters(changedElement) {
                let data = {
                    teacher_id: $('select[name="teacher_id"]').val(),
                    group_id: $('select[name="group_id"]').val(),
                    student_id: $('select[name="student_id"]').val(),
                    gender: $('#gender').val(),
                };

                $.ajax({
                    url: "{{ route('reports.filters.data') }}",
                    type: 'GET',
                    data: data,
                    success: function(response) {
                        if (changedElement !== 'teacher_id' && $('select[name="teacher_id"]').length) {
                            updateSelect('teacher_id', response.teachers, 'id', 'full_name', data
                                .teacher_id);
                        }
                        if (changedElement !== 'group_id' && $('select[name="group_id"]').length) {
                            updateSelect('group_id', response.groups, 'id', 'GroupName', data.group_id);
                        }
                        if (changedElement !== 'student_id' && $('select[name="student_id"]').length) {
                            updateSelect('student_id', response.students, 'id', 'full_name', data
                                .student_id);
                        }
                    }
                });
            }

            function updateSelect(name, items, valueKey, textKey, currentValue) {
                let select = $(`select[name="${name}"]`);
                select.empty().append('<option value="">-- الكل --</option>');
                if (items) {
                    $.each(items, function(i, item) {
                        let selected = (item[valueKey] == currentValue) ? 'selected' : '';
                        select.append(
                            `<option value="${item[valueKey]}" ${selected}>${item[textKey]}</option>`);
                    });
                }
            }

            $(document).on('change',
                'select[name="teacher_id"], select[name="group_id"], select[name="student_id"], #gender, #date_from, #date_to',
                function() {
                    let name = $(this).attr('name');
                    if (name && ['teacher_id', 'group_id', 'student_id'].includes(name)) {
                        syncFilters(name);
                    }
                    table.draw(); // إعادة رسم الجدول عند تغيير أي فلتر بما فيهم الجنس
                });
        });
    </script>
@endpush
