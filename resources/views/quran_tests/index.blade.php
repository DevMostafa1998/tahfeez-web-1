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
            color: #0d6efd !important;
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

        .btn-excel-top:hover {
            background-color: #155a35 !important;
        }

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

        #testsTable {
            width: 100% !important;
            margin: 0 !important;
        }
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
                <button id="exportExcelCustom"
                    class="btn btn-excel-top btn-sm d-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm">
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
                            <input type="date" id="minDate" class="form-control form-control-sm"
                                style="width: 150px; cursor: pointer;">

                            <label class="small mx-2">إلي :</label>
                            <input type="date" id="maxDate" class="form-control form-control-sm"
                                style="width: 150px; cursor: pointer;">

                            <button id="resetDate" class="btn btn-light btn-sm border ms-2">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>

                    <table id="testsTable" class="table table-striped table-bordered align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="text-center">اسم الطالب/ة</th>
                                <th class="text-center">تصنيف الطالب</th>
                                <th class="text-center">التاريخ</th>
                                <th class="text-center">من جزء</th>
                                <th class="text-center">إلى جزء</th>
                                <th class="text-center">عدد الأجزاء</th>
                                <th class="text-center">النوع</th>
                                <th class="text-center">النسبة المئوية</th>
                                <th class="text-center">الشيخ المحفظ</th> {{-- تم التعديل هنا --}}
                                <th class="text-center">الشيخ المختبر</th> {{-- عمود جديد --}}
                                <th class="text-center">النتيجة والتقدير</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tests as $test)
                                <tr>
                                    <td class="ps-4 fw-bold text-start">{{ $test->student?->full_name }}</td>
                                    <td class="text-center text-primary fw-bold">
                                        {{ $test->student?->categorie?->name ?? 'عام' }}</td>
                                    <td class="text-center">{{ $test->date ? $test->date->format('Y-m-d') : '' }}</td>
                                    <td class="text-center">الجزء {{ $test->from_juz }}</td>
                                    <td class="text-center">الجزء {{ $test->to_juz }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-light border">{{ $test->juz_count }} أجزاء</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-info px-3">{{ $test->examType }}</span>
                                    </td>
                                    <td class="text-center fw-bold text-success">{{ $test->score_percentage }}%</td>

                                    {{-- عرض الشيخ المحفظ --}}
                                    <td class="text-center text-muted small">{{ $test->teacher_name ?? 'لا يوجد' }}</td>

                                    {{-- عرض الشيخ المختبر (الإضافة الجديدة) --}}
                                    <td class="text-center text-primary small fw-bold">
                                        {{ $test->tester_name ?? 'غير محدد' }}</td>

                                    <td class="text-center">
                                        <span
                                            class="badge {{ in_array($test->result_status, ['راسب', 'ضعيف']) ? 'bg-danger' : 'bg-success' }} text-white px-3">
                                            {{ $test->result_status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary rounded-circle action-btn edit-test-btn"
                                                data-bs-toggle="modal" data-bs-target="#editTestModal"
                                                data-id="{{ $test->id }}" data-student-id="{{ $test->studentId }}"
                                                data-student-name="{{ $test->student?->full_name }}"
                                                data-category="{{ $test->student?->categorie?->name ?? 'عام' }}"
                                                data-date="{{ $test->date ? $test->date->format('Y-m-d') : '' }}"
                                                data-from-juz="{{ $test->from_juz }}" data-to-juz="{{ $test->to_juz }}"
                                                data-juz="{{ $test->juz_count }}" data-type="{{ $test->examType }}"
                                                data-score="{{ $test->score_percentage }}"
                                                data-teacher="{{ $test->teacher_name }}"
                                                data-tester="{{ $test->tester_name }}" {{-- تمرير الشيخ المختبر --}}
                                                data-status="{{ $test->result_status }}" data-note="{{ $test->note }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger rounded-circle action-btn delete-test-btn"
                                                data-id="{{ $test->id }}"
                                                data-name="{{ $test->student?->full_name }}">
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
            // جلب الإعدادات من الكنترولر لمعالجتها في الـ Edit Modal
            const dbSettingsEdit = @json($settings ?? []);
            const editSettings = [];
            Object.values(dbSettingsEdit).forEach(item => {
                editSettings.push({
                    name: item.grade_name,
                    min: parseInt(item.min_score),
                    max: parseInt(item.max_score),
                    status: item.status,
                    textClass: item.text_class
                });
            });

            // تعبئة البيانات عند فتح المودال
            $(document).on('click', '.edit-test-btn', function() {
                const btn = $(this);

                // إعداد مسار الـ Form
                const testId = btn.data('id');
                $('#editTestForm').attr('action', `/quran_tests/${testId}`);

                // تعبئة الحقول الأساسية
                $('#edit_student_name_display').val(btn.data('student-name'));
                $('#edit_student_category').val(btn.data('category'));
                // يجب توفير بيانات studentId في الزر أو استخراجه بطريقة ما إن لزم الأمر
                $('#edit_studentId').val(btn.data('student-id'));

                $('#edit_teacher_name').val(btn.data('teacher'));
                $('#edit_tester_name').val(btn.data('tester'));
                $('#edit_date').val(btn.data('date'));
                $('#edit_examType').val(btn.data('type'));
                $('#edit_from_juz').val(btn.data('from-juz'));
                $('#edit_to_juz').val(btn.data('to-juz'));
                $('#edit_juz_count').val(btn.data('juz'));
                $('#edit_score_percentage').val(btn.data('score'));
                $('#edit_note').val(btn.data('note'));

                // تشغيل دالة تقييم الدرجة فوراً لعرض التقدير الصحيح عند الفتح
                triggerEditScoreCheck();
            });

            // حساب عدد الأجزاء تلقائياً في التعديل
            const editFromJuz = document.getElementById('edit_from_juz');
            const editToJuz = document.getElementById('edit_to_juz');
            const editJuzCount = document.getElementById('edit_juz_count');

            function calculateEditJuzCount() {
                const from = parseInt(editFromJuz.value);
                const to = parseInt(editToJuz.value);
                if (!isNaN(from) && !isNaN(to)) {
                    editJuzCount.value = Math.abs(to - from) + 1;
                } else {
                    editJuzCount.value = '';
                }
            }
            editFromJuz.addEventListener('change', calculateEditJuzCount);
            editToJuz.addEventListener('change', calculateEditJuzCount);

            // حساب التقدير والحالة تلقائياً في التعديل بناءً على النسبة
            const editScorePercentage = document.getElementById('edit_score_percentage');
            const editGradeLabel = document.getElementById('edit_grade_label');
            const editResultStatusLabel = document.getElementById('edit_result_status_label');
            const editResultStatusHidden = document.getElementById('edit_result_status_hidden');

            function triggerEditScoreCheck() {
                let score = parseInt(editScorePercentage.value);
                if (score > 100) {
                    editScorePercentage.value = 100;
                    score = 100;
                }
                if (score < 0) {
                    editScorePercentage.value = 0;
                    score = 0;
                }

                if (!isNaN(score)) {
                    let matched = false;
                    for (let i = 0; i < editSettings.length; i++) {
                        let range = editSettings[i];
                        if (score >= range.min && score <= range.max) {
                            editGradeLabel.value = range.name;
                            editGradeLabel.className =
                                `form-control text-center fw-bold bg-light ${range.textClass}`;

                            editResultStatusLabel.value = range.status;
                            editResultStatusHidden.value = range.status;
                            editResultStatusLabel.className =
                                `form-control text-center fw-bold bg-light ${range.status === 'ناجح' ? 'text-success' : 'text-danger'}`;
                            matched = true;
                            break;
                        }
                    }
                    if (!matched) clearEditLabels();
                } else {
                    clearEditLabels();
                }
            }

            function clearEditLabels() {
                editGradeLabel.value = '';
                editGradeLabel.className = "form-control text-center fw-bold bg-light";
                editResultStatusLabel.value = '';
                editResultStatusHidden.value = '';
                editResultStatusLabel.className = "form-control text-center fw-bold bg-light";
            }

            editScorePercentage.addEventListener('input', triggerEditScoreCheck);

            // إرسال طلب التعديل عبر AJAX (إذا كنت تستخدمه)
            $('#editTestForm').on('submit', function(e) {
                e.preventDefault();
                const actionUrl = $(this).attr('action');
                const formData = new FormData(this);

                Swal.fire({
                    title: 'جاري التحديث...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // استخدام Fetch كواجهة الـ Create أو $.ajax
                fetch(actionUrl, {
                        method: 'POST', // مع التمرير لـ @method('PUT') المخفي
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('تم التحديث بنجاح!', data.message, 'success')
                                .then(() => location.reload());
                        } else {
                            throw new Error(data.message || 'بيانات غير صحيحة');
                        }
                    })
                    .catch(error => {
                        Swal.fire('خطأ أثناء التحديث', error.message, 'error');
                    });
            });
            // محرك فلترة التاريخ (عمود التاريخ أصبح إندكس 2 بعد إضافة عمود التصنيف)
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var min = $('#minDate').val();
                var max = $('#maxDate').val();
                var date = data[2];
                if ((min === "" && max === "") || (min === "" && date <= max) || (min <= date && max ===
                        "") || (min <= date && date <= max)) {
                    return true;
                }
                return false;
            });

            var table = $('#testsTable').DataTable({
                "responsive": true,
                "ordering": true,
                "order": [
                    [2, "desc"]
                ], // ترتيب افتراضي حسب التاريخ الأحدث
                "language": {
                    "sSearch": "البحث:",
                    "sLengthMenu": "إظهار _MENU_ سجلات",
                    "sInfo": "عرض _TOTAL_ سجل",
                    "paginate": {
                        "next": "›",
                        "previous": "‹"
                    }
                },
                "dom": "<'row mb-2'<'col-md-auto'l><'col-md-auto' <'#datePlaceholder'>><'col-md'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "buttons": [{
                    extend: 'excelHtml5',
                    title: 'سجل اختبارات تسميع القرآن الكريم',
                    // تحديث مصفوفة التصدير لتشمل كافة الأعمدة من الإندكس 0 وحتى 9 وتستثني عمود الإجراءات (10)
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                    }
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
                            type: 'POST', // تحويل النوع إلى POST لخدعة جدار الحماية
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            data: {
                                _method: 'DELETE' // نخبر لارفيل هنا أن الطلب الحقيقي هو DELETE
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('تم الحذف!', response.message, 'success')
                                        .then(() =>
                                            location.reload()
                                        );
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('خطأ!', 'لم يتم الحذف، رمز الخطأ: ' + xhr
                                    .status, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
