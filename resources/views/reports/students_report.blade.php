@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
    <style>
        .custom-compact-table {
            width: 100% !important;
            table-layout: fixed;
            font-size: 11px !important;
        }

        .dataTables_paginate {
            float: left !important;
        }

        .dataTables_info {
            float: right !important;
            font-size: 12px;
            color: #6c757d;
        }

        .dataTables_wrapper::after {
            content: "";
            clear: both;
            display: table;
        }

        .custom-compact-table th,
        .custom-compact-table td {
            padding: 4px 2px !important;
            word-wrap: break-word;
            overflow: hidden;
        }

        .custom-compact-table th {
            font-size: 11.5px !important;
        }

        .badge-custom {
            display: inline-block;
            padding: 2px 4px;
            font-size: 10px;
            color: #0d6efd;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .status-badge {
            padding: 2px 5px;
            border-radius: 4px;
            color: white;
            font-size: 10px;
        }

        .btn-excel {
            background-color: #1d6f42 !important;
            color: white !important;
            border-radius: 5px !important;
            padding: 4px 12px !important;
            font-size: 12px !important;
            font-weight: bold !important;
            border: none !important;
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
        }

        .dataTables_filter {
            display: none;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid p-2" dir="rtl text-right">
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <h4 class="fw-bold text-primary">تقرير الطلاب والمجموعات</h4>
            {{-- الحاوية التي سيتم نقل زر الإكسل إليها --}}
            <div id="excel_button_container"></div>
        </div>

        {{-- بطاقة الفلاتر بنفس تصميم صفحة التسميع --}}
        <div class="card shadow-sm border-0 mb-3 no-print" style="border-radius: 10px;">
            <div class="card-body p-3">
                <form id="filterForm">
                    <div class="row g-2 text-start">
                        @if (auth()->user()->is_admin == 1)
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">المحفظ المسؤول</label>
                                <select name="UserId" id="UserId" class="form-select form-select-sm filter-input">
                                    <option value="">-- كل المحفظين --</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">المجموعة</label>
                            <select name="group_id" id="group_id" class="form-select form-select-sm filter-input">
                                <option value="">-- كل المجموعات --</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->GroupName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- جدول البيانات بالتنسيق المضغوط --}}
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-body p-1">
                <div class="table-responsive-none">
                    <table id="reportTable"
                        class="table table-sm table-bordered table-hover align-middle text-center m-0 custom-compact-table">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>الاسم الكامل</th>
                                <th>رقم الهوية</th>
                                <th>تاريخ الميلاد</th>
                                <th>مكان الميلاد</th>
                                <th>الهاتف</th>
                                <th>واتساب</th>
                                <th>العنوان</th>
                                <th>المركز</th>
                                <th>المسجد</th>
                                <th>المجموعة</th>
                                <th>المحفظ</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            {{-- يتم تعبئة البيانات هنا تلقائياً --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection



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
            // متغير لمنع التحديثات المتكررة عند الربط التلقائي
            let isAutoUpdating = false;

            function fetchStudents() {
                let data = {
                    UserId: $('#UserId').val(),
                    group_id: $('#group_id').val(),
                };

                if ($.fn.DataTable.isDataTable('#reportTable')) {
                    $('#reportTable').DataTable().clear().destroy();
                }

                $('#tableBody').html(
                    '<tr><td colspan="12" class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm"></div> جاري التحميل...</td></tr>'
                );

                $.ajax({
                    url: "{{ route('reports.students') }}",
                    type: 'GET',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        let html = '';
                        if (response && response.length > 0) {
                            $.each(response, function(index, student) {
                                let groupsHtml = '';
                                if (student.groups) {
                                    student.groups.forEach(g => {
                                        groupsHtml +=
                                            `<span class="badge-custom">${g.GroupName}</span> `;
                                    });
                                }
                                html += `<tr>
                                <td class="fw-bold text-dark">${student.full_name || '-'}</td>
                                <td>${student.id_number || '-'}</td>
                                <td>${student.date_of_birth ? student.date_of_birth.substring(0, 10) : '-'}</td>
                                <td>${student.birth_place || '-'}</td>
                                <td>${student.phone_number || '-'}</td>
                                <td>${student.whatsapp_number || '-'}</td>
                                <td>${student.address || '-'}</td>
                                <td>${student.center_name || '-'}</td>
                                <td>${student.mosque_name || '-'}</td>
                                <td>${groupsHtml}</td>
                                <td class="small">${student.teacher_name || '-'}</td>
                                <td><span class="status-badge ${student.is_displaced ? 'bg-danger' : 'bg-success'}">${student.is_displaced ? 'نازح' : 'مقيم'}</span></td>
                            </tr>`;
                            });
                            $('#tableBody').html(html);

                            let table = $('#reportTable').DataTable({
                                // 'i' للمعلومات، 'p' للتنقل. وضعناهما في حاوية واحدة للتحكم بهما
                                "dom": 'rt<"d-flex justify-content-between align-items-center p-2"ip>',
                                "language": {
                                    "emptyTable": "لا توجد سجلات",
                                    "info": "عرض _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                                    "infoEmpty": "عرض 0 إلى 0 من أصل 0 مدخل",
                                    "infoFiltered": "(تصفية من إجمالي _MAX_ مدخل)",
                                    "paginate": {
                                        "first": "«",
                                        "last": "»",
                                        "next": "التالي",
                                        "previous": "السابق"
                                    }
                                },
                                "buttons": [{
                                    extend: 'excelHtml5',
                                    text: '<i class="bi bi-file-earmark-excel"></i> تصدير إكسل',
                                    className: 'btn-excel',
                                    title: 'تقرير الطلاب'
                                }]
                            });
                            $('#excel_button_container').empty();
                            table.buttons().container().appendTo('#excel_button_container');
                        } else {
                            $('#tableBody').html(
                                '<tr><td colspan="12" class="py-5 text-center text-muted fw-bold">لا توجد نتائج</td></tr>'
                            );
                        }
                    }
                });
            }


            $('#UserId').on('change', function() {
                if (isAutoUpdating) return;

                let teacherId = $(this).val();
                let groupSelect = $('#group_id');

                if (teacherId) {
                    $.ajax({
                        url: "{{ url('/get-groups-by-teacher') }}/" + teacherId,
                        type: 'GET',
                        success: function(groups) {
                            groupSelect.html('<option value="">-- كل المجموعات --</option>');
                            $.each(groups, function(key, group) {
                                groupSelect.append('<option value="' + group.id + '">' +
                                    group.GroupName + '</option>');
                            });
                            fetchStudents();
                        }
                    });
                } else {
                    location.reload();
                }
            });



            $('#group_id').on('change', function() {
                let groupId = $(this).val();
                let teacherSelect = $('#UserId');

                if (groupId && teacherSelect.length > 0) {
                    isAutoUpdating = true;
                    $.ajax({
                        url: "{{ url('/get-group-teacher') }}/" + groupId,
                        type: 'GET',
                        success: function(response) {
                            if (response.UserId) {
                                teacherSelect.val(response.UserId);
                            }
                            fetchStudents();
                            isAutoUpdating = false;
                        },
                        error: function() {
                            isAutoUpdating = false;
                        }
                    });
                } else {
                    fetchStudents();
                }
            });

            fetchStudents();
        });
    </script>
@endpush
