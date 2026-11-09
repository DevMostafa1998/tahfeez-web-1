@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/student_report.css') }}">
    <style>
        .badge-custom {
            display: inline-block;
            padding: 2px 8px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin: 2px;
            font-size: 11px;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }

        .bg-info {
            background-color: #2faecb !important;
        }

        .bg-success {
            background-color: #28a745 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-2 text-end" dir="rtl">
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <h4 class="fw-bold text-primary">تقرير الطلاب والمجموعات</h4>
            <div id="excel_button_container"></div>
        </div>

        <div class="card shadow-sm border-0 mb-3 no-print" style="border-radius: 10px;">
            <div class="card-body p-3">
                <form id="filterForm">
                    <div class="row g-2 text-start">
                        @if (auth()->user()->is_admin == 1)
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">اسم المحفظ/ة</label>
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

                        @if (auth()->user()->is_admin)
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">الجنس</label>
                                <select name="gender" id="gender" class="form-select form-select-sm filter-input">
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

        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-body p-1">
                <div class="table-responsive">
                    <table id="reportTable" class="table table-sm table-bordered table-hover align-middle text-center m-0" style="width:100%">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>الاسم الكامل</th>
                                <th>رقم هوية الطالب</th>
                                <th>رقم هوية الأب</th>
                                <th>الجنس</th>
                                <th>التصنيف</th>
                                <th>تاريخ الميلاد</th>
                                <th>مكان الميلاد</th>
                                <th>الهاتف</th>
                                <th>واتساب</th>
                                <th>العنوان</th>
                                <th>المركز</th>
                                <th>المسجد</th>
                                <th>المجموعة</th>
                                <th>المحفظ/ة</th>
                                <th>الحالة</th>
                                <th>نوع الاختبار</th>
                                <th>تاريخ الاختبار</th>
                                <th>معدل الاختبار (%)</th>
                                <th>تقييم ناجح/راسب</th>
                                <th>الشيخ المختبر</th>
                            </tr>
                        </thead>
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
            const originalTeachersHtml = $('#UserId').html();
            const originalGroupsHtml = $('#group_id').html();
            let isAutoUpdating = false;

            let table = $('#reportTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('reports.students') }}",
                    data: function(d) {
                        d.UserId = $('#UserId').val();
                        d.group_id = $('#group_id').val();
                        d.gender = $('#gender').val();
                    }
                },
                columns: [
                    { data: 'full_name', name: 'full_name', orderable: true },
                    { data: 'id_number', name: 'id_number', orderable: true },
                    { data: 'father_id', name: 'father_id', orderable: true },
                    { data: 'gender', name: 'gender', orderable: true },
                    { data: 'category', name: 'category', orderable: false },
                    { data: 'date_of_birth', name: 'date_of_birth', orderable: true },
                    { data: 'birth_place', name: 'birth_place', orderable: true },
                    { data: 'phone_number', name: 'phone_number', orderable: true },
                    { data: 'whatsapp_number', name: 'whatsapp_number', orderable: true },
                    { data: 'address', name: 'address', orderable: true },
                    { data: 'center_name', name: 'center_name', orderable: false, defaultContent: '-' },
                    { data: 'mosque_name', name: 'mosque_name', orderable: true },
                    { data: 'groups', name: 'groups', orderable: false },
                    { data: 'teacher_name', name: 'teacher_name', orderable: false },
                    { data: 'is_displaced', name: 'is_displaced', orderable: true },
                    { data: 'exam_type', name: 'exam_type', orderable: false },
                    { data: 'exam_date', name: 'exam_date', orderable: false },
                    { data: 'score_percentage', name: 'score_percentage', orderable: false },
                    { data: 'result_status', name: 'result_status', orderable: false },
                    { data: 'testing_sheikh', name: 'testing_sheikh', orderable: false }
                ],
                dom: "<'row mb-3'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6 text-end'B>>" +
                    "<'row'<'col-sm-12' tr>>" +
                    "<'row mt-3'<'col-sm-12'p>>" +
                    "<'row'<'col-sm-12 text-center'i>>",
                buttons: [{
                    text: '<i class="bi bi-file-earmark-excel"></i> تصدير إكسل (الكل)',
                    className: 'btn btn-success btn-sm',
                    action: function(e, dt, node, config) {
                        let userId = $('#UserId').val() || '';
                        let groupId = $('#group_id').val() || '';
                        let gender = $('#gender').val() || '';
                        let searchValue = dt.search() || '';

                        let url = "{{ route('reports.students.export') }}?" +
                            "UserId=" + encodeURIComponent(userId) +
                            "&group_id=" + encodeURIComponent(groupId) +
                            "&gender=" + encodeURIComponent(gender) +
                            "&search[value]=" + encodeURIComponent(searchValue);

                        window.location.href = url;
                    }
                }],
                language: {
                    sSearch: "بحث سريع:",
                    lengthMenu: "عرض _MENU_ سجلات",
                    info: "عرض سجلات من _START_ إلى _END_ من أصل _TOTAL_ سجل",
                    infoEmpty: "عرض سجلات من 0 إلى 0 من أصل 0 سجل",
                    infoFiltered: "(تمت تصفيتها من إجمالي _MAX_ سجلات)",
                    paginate: {
                        first: "«",
                        last: "»",
                        next: "›",
                        previous: "‹"
                    },
                    zeroRecords: "لم يتم العثور على أية سجلات مطابقة",
                    emptyTable: "لا توجد بيانات متاحة في الجدول",
                    loadingRecords: "جارٍ التحميل...",
                    processing: "جارٍ المعالجة..."
                }
            });

            table.buttons().container().appendTo('#excel_button_container');

            // تحديث الجدول عند تغيير فلتر الجنس
            $('#gender').on('change', function() {
                table.draw();
            });

            function restoreFullLists() {
                isAutoUpdating = true;
                $('#UserId').html(originalTeachersHtml);
                $('#group_id').html(originalGroupsHtml);
                isAutoUpdating = false;
            }

            // التعامل مع تغيير المحفظ
            $('#UserId').on('change', function() {
                if (isAutoUpdating) return;
                let teacherId = $(this).val();

                if (teacherId) {
                    $.ajax({
                        url: "{{ url('/get-groups-by-teacher') }}/" + teacherId,
                        type: 'GET',
                        success: function(groups) {
                            isAutoUpdating = true;
                            let groupSelect = $('#group_id');
                            groupSelect.html('<option value="">-- كل مجموعات هذا المحفظ --</option>');
                            $.each(groups, function(key, group) {
                                groupSelect.append(`<option value="${group.id}">${group.GroupName}</option>`);
                            });
                            isAutoUpdating = false;
                            table.draw();
                        }
                    });
                } else {
                    restoreFullLists();
                    $('#UserId').val('');
                    $('#group_id').val('');
                    table.draw();
                }
            });

            // التعامل مع تغيير المجموعة
            $('#group_id').on('change', function() {
                if (isAutoUpdating) return;
                let groupId = $(this).val();

                if (groupId) {
                    $.ajax({
                        url: "{{ url('/get-group-teacher') }}/" + groupId,
                        type: 'GET',
                        success: function(response) {
                            isAutoUpdating = true;
                            if (response.UserId) {
                                $('#UserId').html(`<option value="${response.UserId}">${response.teacher_name}</option>`);
                                $('#UserId').val(response.UserId);
                            }
                            isAutoUpdating = false;
                            table.draw();
                        }
                    });
                } else {
                    restoreFullLists();
                    $('#UserId').val('');
                    $('#group_id').val('');
                    table.draw();
                }
            });
        });
    </script>
@endpush
