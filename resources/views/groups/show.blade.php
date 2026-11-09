@extends('layouts.app')

@section('title', 'تفاصيل المجموعة')

@push('css')
    {{-- استخدام أيقونات Bootstrap و SweetAlert2 للتنبيهات --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="{{ asset('assets/css/user_table.css') }}" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/table_responsive.css') }}">

    <style>
        .select2-container .select2-selection--single {
            height: 45px;
            border: 2px solid #dee2e6;
            border-radius: 10px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 45px;
            padding-right: 15px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 43px;
        }

        .select2-container--default .select2-selection--multiple {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            min-height: 45px;
            padding: 2px 6px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #0d6efd;
            border: none;
            color: #fff;
            border-radius: 6px;
            padding: 2px 8px;
            margin-top: 6px;
            float: right !important;
            direction: rtl;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
            margin-right: 0;
            margin-left: 5px;
            float: right;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            background-color: rgba(255,255,255,0.2);
            color: #fff;
        }
        .select2-container .select2-search--inline {
            float: right !important;
        }
        .select2-dropdown {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            z-index: 99999 !important; /* لضمان ظهور القائمة المنسدلة فوق المودال */
        }

        .dataTables_wrapper .row:first-child {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center;
            width: 100%;
            margin: 0 0 1rem 0;
            padding: 0 15px;
            overflow-x: hidden !important;
        }

        #groupsTable {
            width: 100% !important;
            margin: 0 !important;
        }

        select.custom-select {
            direction: ltr !important;
            text-align: center !important;
            background-image: none !important;
            appearance: menulist !important;
            -webkit-appearance: menulist !important;
            -moz-appearance: menulist !important;
            padding: 4px 30px 4px 10px !important;
            min-width: auto !important;
            height: auto !important;
            border-radius: 4px;
        }

        /* محاذاة البحث */
        .dataTables_filter {
            text-align: left !important;
        }

        .dataTables_filter input {
            margin-right: 10px;
        }

        .dataTables_length {
            text-align: right !important;
        }
        .status-switcher .btn-check {
            position: absolute;
            clip: rect(0,0,0,0);
            pointer-events: none;
        }
        /* تحديد أقصى ارتفاع وظهور السكرول بار دون التأثير على الـ Placeholder */
        .select2-container--default .select2-selection--multiple {
            max-height: 90px;
            overflow-y: auto;
            min-height: 38px;
            height: auto !important;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }

        /* تحسين شكل السكرول الداخلي ليكون نحيفاً */
        .select2-container--default .select2-selection--multiple::-webkit-scrollbar {
            width: 5px;
        }
        .select2-container--default .select2-selection--multiple::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .select2-container--default .select2-selection--multiple::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            direction: rtl;
            float: right !important;
            margin-top: 5px !important;
            margin-right: 5px !important;
            padding: 2px 8px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-search--inline {
            float: right !important;
        }
        .select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field {
            margin-top: 6px !important;
            text-align: right !important;
            direction: rtl !important;
        }
        .select2-dropdown {
            direction: rtl !important;
        }

        .select2-container--default .select2-results__option {
            text-align: right !important;
            direction: rtl !important;
        }

        .select2-results__options {
            direction: rtl !important;
        }
        .btn-custom-warning {
            background-color: transparent !important;
            color: #ffc107 !important;
            border: 1px solid #ffc107 !important;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-custom-warning:hover {
            background-color: #ffc107 !important;
            color: #fff !important;               
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4" dir="rtl">

        {{-- القسم العلوي: بطاقة معلومات المجموعة والمحفظ --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 bg-primary text-white rounded-3 shadow">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-4">
                        <div class="bg-white text-primary p-3 rounded-circle shadow-sm">
                            <i class="bi bi-collection-fill fs-3"></i>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1">{{ $group->GroupName }}</h2>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-person-badge me-1"></i>
                                المحفظ: <strong>{{ $group->teacher->full_name ?? 'غير محدد' }}</strong>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap justify-content-end">
                        <a href="{{ route('excel.manage.page', $group->id) }}" class="btn btn-warning px-4 shadow-sm fw-bold">
                            <i class="bi bi-file-earmark-excel-fill me-1"></i> إدارة الحفظ (Excel)
                        </a>

                        <span class="badge bg-white text-primary px-3 py-2 fs-6 shadow-sm d-flex align-items-center">
                            عدد الطلاب: {{ $students->count() }}
                        </span>

                        <a href="{{ route('group.index') }}" class="btn btn-light px-4 shadow-sm fw-bold">
                            <i class="bi bi-arrow-right me-1"></i> عودة للمجموعات
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- القسم السفلي: جدول بيانات الطلاب --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-people-fill me-2 text-primary"></i> قائمة طلاب المجموعة
                </h5>
            </div>
            <div class="card-body p-0 text-end">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table id="studentsTable" class="table table-bordered table-hover align-middle mb-0 shadow-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center py-3">اسم الطالب/ة</th>
                                    <th class="text-center py-3">العمر</th>
                                    <th class="text-center py-3">آخر سورة</th>
                                    <th class="text-center py-3">آخر آية</th>
                                    <th class="text-center py-3">نوع التسميع</th>
                                    <th class="text-center py-3">رقم الهوية</th>
                                    <th class="text-center py-3">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $student)
                                    <tr id="row-student-{{ $student->id }}">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center fw-bold text-secondary shadow-sm"
                                                    style="width: 40px; height: 40px;">
                                                    {{ mb_substr($student->full_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $student->full_name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info-subtle text-info border px-3">
                                                {{ \Carbon\Carbon::parse($student->date_of_birth)->age }} سنة
                                            </span>
                                        </td>
                                        <td class="text-center fw-bold text-success sura-cell">
                                            {{ $student->latestMemorization->sura_name ?? '---' }}
                                        </td>

                                        <td class="text-center verse-cell">
                                            <span class="badge bg-light text-dark border">
                                                {{ $student->latestMemorization->verses_to ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center text-primary fw-bold">
                                            <span class="badge bg-primary-subtle text-primary border px-2">
                                                {{ $student->latestMemorization->examType->description ?? 'غير محدد' }}
                                            </span>
                                        </td>
                                        <td class="text-center text-muted">
                                            {{ $student->id_number }}
                                        </td>
                                        <td class="text-center">
                                            <button
                                                class="btn btn-success btn-sm px-3 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#memorizeModal{{ $student->id }}">
                                                <i class="bi bi-journal-plus"></i> تسجيل حفظ
                                            </button>
                                           <a href="{{ route('memorization.edit_student_surahs', $student->id) }}"
                                            class="btn btn-custom-warning btn-sm px-3 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm"
                                            title="تعديل السور">
                                                <i class="bi bi-pencil-square"></i> تعديل السور
                                            </a>

                                    {{-- المودال الخاص بكل طالب --}}
                                    <div class="modal fade" id="memorizeModal{{ $student->id }}" tabindex="-1" aria-hidden="true" dir="rtl">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; text-align: right;">

                                                <form class="memorizationForm" action="{{ route('memorization.store') }}" method="POST" novalidate>
                                                    @csrf
                                                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                                                    {{-- رأس المودال --}}
                                                    <div class="modal-header border-0 bg-primary text-white" style="border-radius: 15px 15px 0 0; flex-direction: row-reverse; padding: 12px 20px;">
                                                        <h5 class="modal-title fw-bold m-0 fs-6" style="text-align: right; width: 100%;">
                                                            <i class="bi bi-journal-plus ms-2"></i>تسجيل حفظ: {{ $student->full_name }}
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white m-0" data-bs-dismiss="modal" aria-label="Close" style="margin-right: auto !important; margin-left: 0 !important; font-size: 0.8rem;"></button>
                                                    </div>

                                                    <div class="modal-body p-3">
                                                        <div class="row g-3">

                                                            {{-- 1. أزرار حالة التسميع: سمع / لم يسمع --}}
                                                            <div class="col-12 text-center mb-1">
                                                                <label class="form-label fw-bold text-secondary d-block text-right mb-2 small">
                                                                    <i class="bi bi-question-circle text-primary ms-1"></i> حالة التسميع اليوم:
                                                                </label>
                                                                <div class="btn-group btn-group-toggle d-flex w-100 status-switcher" data-toggle="buttons">
                                                                    <label class="btn btn-outline-success w-50 py-2 active fw-bold small" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                                                                        <input type="radio" name="is_memorized" class="status-radio" value="1" checked autocomplete="off">
                                                                        <i class="bi bi-check-circle-fill ms-1"></i> سَمَّعَ
                                                                    </label>
                                                                    <label class="btn btn-outline-danger w-50 py-2 fw-bold small" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                                                        <input type="radio" name="is_memorized" class="status-radio" value="0" autocomplete="off">
                                                                        <i class="bi bi-x-circle-fill ms-1"></i> لَمْ يُسَمِّعْ
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            {{-- 2. تاريخ اليوم) --}}
                                                            <div class="col-12" style="text-align: right;">
                                                                <label class="form-label fw-bold text-secondary mb-1 small">
                                                                    <i class="bi bi-calendar-event ms-1 text-primary"></i> تاريخ اليوم
                                                                </label>
                                                                <input type="date" name="date" class="form-control form-control-sm border-2" value="{{ date('Y-m-d') }}" required style="border-radius: 8px; text-align: right; direction: rtl; font-size: 0.9rem;">
                                                            </div>

                                                            <div class="col-12 p-0 row m-0 g-3 memorization-fields">

                                                                <div class="col-12 text-right">
                                                                    <label class="form-label fw-bold text-secondary mb-1 small">
                                                                        <i class="bi bi-tags ms-1 text-primary"></i> نوع التسميع
                                                                    </label>
                                                                    <select name="examType_id" class="form-control form-control-sm border-2 type-select" required style="border-radius: 8px; text-align: right; direction: rtl; font-size: 0.9rem;">
                                                                        <option value="">اختر نوع الاختبار...</option>
                                                                        @foreach($examTypes as $type)
                                                                            <option value="{{ $type->id }}">{{ $type->description }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-12 text-right">
                                                                    <div class="form-check form-switch p-0 d-flex align-items-center justify-content-between" style="direction: ltr; background: #f8f9fa; padding: 6px 12px !important; border-radius: 8px; border: 1px dashed #dee2e6;">
                                                                        <input class="form-check-input multi-surah-toggle m-0" type="checkbox" name="is_multi_surah" value="1" role="switch" id="multiSurahToggle{{ $student->id }}">
                                                                        <label class="form-check-label fw-bold text-dark small m-0" for="multiSurahToggle{{ $student->id }}" style="direction: rtl; width: 100%; text-align: right;">
                                                                            <i class="bi bi-layers text-primary ms-1"></i> تسميع أكثر من سورة؟
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-12 p-0 row m-0 g-3 single-surah-container">
                                                                    <div class="col-12 text-right">
                                                                        <label class="form-label fw-bold text-secondary mb-1 small">
                                                                            <i class="bi bi-book ms-1 text-primary"></i> اسم السورة
                                                                        </label>
                                                                        <select name="sura_name" class="form-select surah-select" style="width: 100%;">
                                                                            <option value="">ابحث عن اسم السورة...</option>
                                                                            @foreach ($surahs as $surah)
                                                                                <option value="{{ $surah->name_ar }}" data-verses="{{ $surah->verses_count }}" {{ isset($student->latestMemorization->sura_name) && $student->latestMemorization->sura_name == $surah->name_ar ? 'selected' : '' }}>
                                                                                    {{ $surah->number }}. {{ $surah->name_ar }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-6" style="text-align: right;">
                                                                        <label class="form-label fw-bold text-secondary mb-1 small">
                                                                            <i class="bi bi-hash ms-1 text-primary"></i> من آية
                                                                        </label>
                                                                        <input type="number" name="verses_from" value="{{ isset($student->latestMemorization->verses_to) ? $student->latestMemorization->verses_to + 1 : 1 }}" class="form-control form-control-sm border-2" min="1" style="border-radius: 8px; text-align: right; direction: rtl;">
                                                                    </div>

                                                                    <div class="col-6" style="text-align: right;">
                                                                        <label class="form-label fw-bold text-secondary mb-1 small">
                                                                            <i class="bi bi-hash ms-1 text-primary"></i> إلى آية
                                                                        </label>
                                                                        <input type="number" name="verses_to" value="" class="form-control form-control-sm border-2" min="1" style="border-radius: 8px; text-align: right; direction: rtl;">
                                                                    </div>
                                                                </div>

                                                                <div class="col-12 p-0 row m-0 g-3 multi-surah-container" style="display: none;">
                                                                    <div class="col-12 text-right">
                                                                        <label class="form-label fw-bold text-secondary mb-1 small">
                                                                            <i class="bi bi-collection ms-1 text-primary"></i> اختر السور (تحديد متعدد)
                                                                        </label>
                                                                        <select name="surah_ids[]" class="form-select js-example-basic-multiple multi-surah-select" multiple="multiple" style="width: 100%;">
                                                                            @foreach ($surahs as $surah)
                                                                                <option value="{{ $surah->name_ar }}" data-verses="{{ $surah->verses_count }}">
                                                                                    {{ $surah->number }}. {{ $surah->name_ar }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-12 text-right">
                                                                        <div class="form-check form-switch p-0 d-flex align-items-center justify-content-between" style="direction: ltr; background: #fff9e6; padding: 6px 12px !important; border-radius: 8px; border: 1px dashed #ffeba8;">
                                                                            <input class="form-check-input last-surah-toggle m-0" type="checkbox" name="record_last_verses" value="1" role="switch" id="lastSurahToggle{{ $student->id }}">
                                                                            <label class="form-check-label fw-bold text-dark small m-0" for="lastSurahToggle{{ $student->id }}" style="direction: rtl; width: 100%; text-align: right;">
                                                                                <i class="bi bi-pencil-square text-warning ms-1"></i> تسجيل آيات السورة الأخيرة؟
                                                                            </label>
                                                                        </div>
                                                                    </div>

                                                                    {{-- حقول آيات السورة الأخيرة --}}
                                                                    <div class="col-12 p-0 row m-0 g-2 last-surah-verses-container" style="display: none;">
                                                                        <div class="col-6" style="text-align: right;">
                                                                            <label class="form-label fw-bold text-secondary mb-1 small">
                                                                                <i class="bi bi-hash ms-1 text-warning"></i> من آية
                                                                            </label>
                                                                            <input type="number" name="last_sura_from" class="form-control form-control-sm border-2" min="1" value="1" style="border-radius: 8px; text-align: right; direction: rtl;">
                                                                        </div>

                                                                        <div class="col-6" style="text-align: right;">
                                                                            <label class="form-label fw-bold text-secondary mb-1 small">
                                                                                <i class="bi bi-hash ms-1 text-warning"></i> إلى آية
                                                                            </label>
                                                                            <input type="number" name="last_sura_to" class="form-control form-control-sm border-2" min="1" style="border-radius: 8px; text-align: right; direction: rtl;">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <div class="col-12" style="text-align: right;">
                                                                <label class="form-label fw-bold text-secondary mb-1 small">
                                                                    <i class="bi bi-chat-left-text ms-1 text-primary"></i> ملاحظات المعلم
                                                                </label>
                                                                <textarea name="note" class="form-control border-2" rows="2" placeholder="أضف ملاحظاتك هنا..." style="border-radius: 8px; text-align: right; direction: rtl; font-size: 0.9rem;"></textarea>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="modal-footer border-0 bg-light p-2 d-flex" style="border-radius: 0 0 15px 15px; justify-content: flex-start; flex-direction: row-reverse;">
                                                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm small" style="border-radius: 8px; font-size: 0.9rem;">
                                                            حفظ البيانات
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary px-3 py-2 fw-bold ms-2 small" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.9rem;">
                                                            إلغاء
                                                        </button>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
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
    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap4.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

    $('.multi-surah-select').select2({
        placeholder: "اختر السور المراد تسميعها",
        allowClear: true,
        dir: "rtl",
        width: '100%'
    });
    $(document).ready(function() {
        // تهيئة الـ DataTable
        if ($.fn.DataTable.isDataTable('#studentsTable')) {
            $('#studentsTable').DataTable().destroy();
        }

        let table = $('#studentsTable').DataTable({
            "autoWidth": false,
            "responsive": true,
            "searching": true,
            "ordering": true,
            "language": {
                "sProcessing": "جاري التحميل...",
                "sLengthMenu": "عرض _MENU_ طلاب",
                "sZeroRecords": "لم يعثر على أية سجلات",
                "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ طالب",
                "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
                "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
                "sSearch": "بحث سريع:",
                "sEmptyTable": "لا يوجد طلاب في هذه المجموعة حالياً.",
                "oPaginate": {
                    "sFirst": "الأول",
                    "sPrevious": "السابق",
                    "sNext": "التالي",
                    "sLast": "الأخير"
                }
            },
            "dom": "<'row mb-3 align-items-center'<'col-md-6 text-right'l><'col-md-6 text-left'f>>" +
                "<'row'<'col-12'tr>>" +
                "<'row mt-3 align-items-center'<'col-md-6 text-right'i><'col-md-6 d-flex justify-content-end'p>>",
            "columnDefs": [{
                    "orderable": false,
                    "targets": 6
                },
                {
                    "searchable": false,
                    "targets": 6
                },
                {
                    "className": "text-center",
                    "targets": "_all"
                }
            ]
        });

        // عند فتح المودال - تفعيل حقول الاختيار
        $('.modal').on('shown.bs.modal', function() {
            let modal = $(this);
            let surahSelect = modal.find('.surah-select');
            let multiSurahSelect = modal.find('.multi-surah-select');
            let vFromInput = modal.find('input[name="verses_from"]');
            let vToInput = modal.find('input[name="verses_to"]');

            modal.find('form').attr('novalidate', true);

            // تفعيل سلكت 2 للسورة الواحدة
            if (!surahSelect.hasClass("select2-hidden-accessible")) {
                surahSelect.select2({
                    dropdownParent: modal,
                    dir: "rtl",
                    width: '100%',
                    language: { noResults: function() { return "لا توجد نتائج"; } }
                });
            }

            // تفعيل سلكت 2 للسور المتعددة (تحديد متعدد)
            if (!multiSurahSelect.hasClass("select2-hidden-accessible")) {
                multiSurahSelect.select2({
                    dropdownParent: modal,
                    dir: "rtl",
                    width: '100%',
                    placeholder: "اختر السور المراد تسميعها...",
                    language: { noResults: function() { return "لا توجد نتائج"; } }
                });
            }

            // مراقبة تغيير السورة الواحدة لجلب أقصى عدد آيات
            surahSelect.on('change', function() {
                let selectedOption = $(this).find(':selected');
                let maxVerses = selectedOption.data('verses');

                if (maxVerses) {
                    [vFromInput, vToInput].forEach(input => {
                        input.attr('max', maxVerses);
                        input.attr('placeholder', 'أقصى آية: ' + maxVerses);

                        if (parseInt(input.val()) > maxVerses) {
                            input.val('');
                            input.addClass('is-invalid');
                        } else {
                            input.removeClass('is-invalid');
                            $('#' + input.attr('name') + '_error').remove();
                        }
                    });
                }
            });

            surahSelect.trigger('change');
        });

        // 1. مراقبة أزرار: سَمَّعَ / لَمْ يُسَمِّعْ
        $(document).on('change', '.status-radio', function() {
            let modal = $(this).closest('.modal');
            let fieldsContainer = modal.find('.memorization-fields');
            let allInputs = fieldsContainer.find('input, select, textarea');

            if ($(this).val() == "1") {
                fieldsContainer.slideDown(300);
                modal.find('.multi-surah-toggle').trigger('change');
            } else {
                fieldsContainer.slideUp(300);
                allInputs.removeClass('is-invalid');
                modal.find('.invalid-feedback').remove();
            }
        });

        // 2. مراقبة زر تبديل: تسميع أكثر من سورة؟
        $(document).on('change', '.multi-surah-toggle', function() {
            let modal = $(this).closest('.modal');
            let singleContainer = modal.find('.single-surah-container');
            let multiContainer = modal.find('.multi-surah-container');

            if ($(this).is(':checked')) {
                singleContainer.hide();
                singleContainer.find('select, input').prop('disabled', true);

                multiContainer.show();
                multiContainer.find('select, input').prop('disabled', false);
                modal.find('.multi-surah-select').prop('disabled', false);

                modal.find('.multi-surah-select').trigger('change');
            } else {
                multiContainer.hide();
                multiContainer.find('select, input').prop('disabled', true);

                singleContainer.show();
                singleContainer.find('select, input').prop('disabled', false);
            }
        });

        // 3. مراقبة زر تبديل: تسجيل آيات السورة الأخيرة؟
        $(document).on('change', '.last-surah-toggle', function() {
            let modal = $(this).closest('.modal');
            let lastVersesContainer = modal.find('.last-surah-verses-container');
            let lastInputs = lastVersesContainer.find('input');

            if ($(this).is(':checked') && modal.find('.multi-surah-toggle').is(':checked')) {
                lastVersesContainer.slideDown(200);
                lastInputs.prop('required', true).prop('disabled', false);
                modal.find('.multi-surah-select').trigger('change');
            } else {
                lastVersesContainer.slideUp(200);
                lastInputs.prop('required', false).prop('disabled', true).removeClass('is-invalid');
                lastVersesContainer.find('.invalid-feedback').remove();
            }
        });

        $(document).on('change', '.multi-surah-select', function() {
            let modal = $(this).closest('.modal');
            let selectedOptions = $(this).find('option:selected');

            if (selectedOptions.length > 0) {
                let lastOption = selectedOptions.last();
                let maxVerses = parseInt(lastOption.data('verses'));

                if (maxVerses) {
                    let fromInput = modal.find('input[name="last_sura_from"]');
                    let toInput = modal.find('input[name="last_sura_to"]');

                    fromInput.attr('max', maxVerses).attr('placeholder', '1');
                    toInput.attr('max', maxVerses).attr('placeholder', 'أقصى آية: ' + maxVerses);

                    toInput.trigger('input');
                }
            }
        });

        $(document).on('input change', 'input[name="last_sura_to"], input[name="last_sura_from"]', function() {
            let input = $(this);
            let max = parseInt(input.attr('max'));
            let val = parseInt(input.val());

            input.removeClass('is-invalid');
            input.closest('.text-right, .col-6, div').find('.invalid-feedback').remove();

            if (max && val > max) {
                input.addClass('is-invalid');
                input.after(`<div class="invalid-feedback d-block fw-bold text-danger mt-1">يجب أن يكون ${max} أو أقل</div>`);
            }
        });

        // التحقق الفوري من تخطي الحد الأقصى للآيات عند الإدخال اليدوي (السورة الواحدة)
        $(document).on('input', 'input[name="verses_to"], input[name="verses_from"]', function() {
            let max = parseInt($(this).attr('max'));
            let val = parseInt($(this).val());
            let errorId = $(this).attr('name') + '_error';

            $('#' + errorId).remove();

            if (max && val > max) {
                $(this).addClass('is-invalid');
                $(this).after(`<div id="${errorId}" class="text-danger small mt-1 fw-bold" style="display:block; width:100%;">يجب أن يكون ${max} أو أقل</div>`);
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // 4. معالجة إرسال نموذج الحفظ عبر AJAX
        $(document).on('submit', '.memorizationForm', function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = form.find('button[type="submit"]');
            let modal = form.closest('.modal');
            let isMemorized = form.find('.status-radio:checked').val();
            let isMulti = form.find('.multi-surah-toggle').is(':checked');

            let studentId = form.find('input[name="student_id"]').val();
            let row = $('#row-student-' + studentId);

            let surahSelect = form.find('.surah-select');
            let vFromInput = form.find('input[name="verses_from"]');
            let vToInput = form.find('input[name="verses_to"]');
            let multiSurahSelect = form.find('.multi-surah-select');
            let typeSelect = form.find('.type-select');

            // التحقق في حال سورة واحدة فقط
            if (isMemorized == "1" && !isMulti) {
                let max = parseInt(vToInput.attr('max'));
                let vFromVal = parseInt(vFromInput.val());
                let currentTo = parseInt(vToInput.val());

                if (max) {
                    if (vFromVal > max || currentTo > max) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'خطأ في عدد الآيات',
                            text: 'السورة المختارة تحتوي على ' + max + ' آية فقط.',
                            confirmButtonText: 'حسناً'
                        });
                        return false;
                    }
                }
            }

            // التحقق النهائي لآيات السورة الأخيرة
            let isLastPartial = form.find('.last-surah-toggle').is(':checked');
            if (isMemorized == "1" && isMulti && isLastPartial) {
                let lastFromInput = form.find('input[name="last_sura_from"]');
                let lastToInput = form.find('input[name="last_sura_to"]');

                let max = parseInt(lastToInput.attr('max'));
                let vFromVal = parseInt(lastFromInput.val()) || 0;
                let currentTo = parseInt(lastToInput.val()) || 0;

                if (max) {
                    if (vFromVal > max || currentTo > max) {
                        lastToInput.trigger('input');
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ في الآيات!',
                            text: 'لا يمكن الحفظ، بعض الحقول تحتوي على قيم أعلى من الحد الأقصى للسورة.',
                            confirmButtonText: 'حسناً'
                        });
                        return false;
                    }
                    if (vFromVal > currentTo) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'خطأ في الترتيب',
                            text: 'بداية الآيات لا يمكن أن تكون أكبر من نهايتها.',
                            confirmButtonText: 'حسناً'
                        });
                        return false;
                    }
                }
            }

            // تجهيز البيانات  للإرسال (بما في ذلك مصفوفة السور المتعددة)
            let formData = form.serializeArray();

            // التحقق من إضافة المتغيرات  بشكل صحيح
            let hasMultiSurah = formData.some(item => item.name === 'is_multi_surah');
            if(!hasMultiSurah) {
                formData.push({ name: 'is_multi_surah', value: isMulti ? '1' : '0' });
            }
            formData.push({ name: 'is_last_surah_partial', value: isLastPartial ? '1' : '0' });

            let serializedData = $.param(formData);
            let typeText = typeSelect.find('option:selected').text() || 'تسميع';
            let originalText = btn.text();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...');

            $.ajax({
                url: form.attr('action') || '/memorization/store',
                method: 'POST',
                data: serializedData,
                dataType: 'json',
                success: function(response) {
                    btn.prop('disabled', false).text(originalText);

                    if(response.success) {
                        modal.modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحفظ',
                            text: 'تم تسجيل البيانات بنجاح',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        row.find('.sura-cell').html(response.sura_text);
                        row.find('.verse-cell').html(response.verse_text);

                        if(response.is_memorized == "1") {
                            // تحديث قيم الـ Input للتسميع الفردي القادم
                            if (!isMulti) {
                                let vToVal = response.verses_to;
                                let nextVerse = parseInt(vToVal) ? parseInt(vToVal) + 1 : '';
                                let max = parseInt(vToInput.attr('max'));
                                if (max && nextVerse > max) { nextVerse = max; }
                                vFromInput.val(nextVerse);
                            }

                            let badgeClasses = 'bg-primary-subtle text-primary';
                            if (typeText.includes('مراجعة')) {
                                badgeClasses = 'bg-warning-subtle text-warning-emphasis';
                            } else if (typeText.includes('سرد') || typeText.includes('اختبار')) {
                                badgeClasses = 'bg-success-subtle text-success';
                            }
                            row.find('td').eq(4).html(`<span class="badge ${badgeClasses} border px-2">${typeText}</span>`);
                        } else {
                            row.find('td').eq(4).html('<span class="badge bg-danger-subtle text-danger border px-2">لَمْ يُسَمِّعْ</span>');
                        }

                        // إخطار الـ DataTable بالتحديث وإعادة الرسم الفورية
                        if (typeof table !== 'undefined' && table) {
                            table.row(row).invalidate().draw(false);
                        }
                    } else {
                        Swal.fire({ icon: 'error', title: 'خطأ!', text: response.message || 'حدث خطأ أثناء الحفظ.' });
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text(originalText);
                    let errorMsg = 'حدث خطأ غير متوقع أثناء الحفظ، يرجى المحاولة مرة أخرى.';
                    if (xhr.responseJSON && xhr.responseJSON.message) { errorMsg = xhr.responseJSON.message; }
                    Swal.fire({ icon: 'error', title: 'فشل الحفظ', html: errorMsg });
                }
            });
        });

        // عند إغلاق المودال - تصفير المدخلات وإعادتها للوضع الافتراضي
        $('.modal').on('hide.bs.modal', function() {
            let modal = $(this);
            modal.find('textarea[name="note"]').val('');
            modal.find('input[name="verses_to"]').val('');
            modal.find('input[name="last_sura_to"]').val('');
            modal.find('input[name="last_sura_from"]').val('1');
            modal.find('.type-select').val('').trigger('change');
            modal.find('.multi-surah-select').val([]).trigger('change');
            modal.find('input').removeClass('is-invalid');
            modal.find('.text-danger.small, .invalid-feedback').remove();

            // إرجاع التسميع المتعدد لوضع الإطفاء
            modal.find('.multi-surah-toggle').prop('checked', false).trigger('change');
            modal.find('.last-surah-toggle').prop('checked', false).trigger('change');

            // إرجاع وضع "سمع" كوضع افتراضي
            modal.find('.status-radio[value="1"]').prop('checked', true).trigger('change');
            modal.find('.status-radio[value="1"]').closest('label').addClass('active').siblings().removeClass('active');
        });
    });
</script>
@endpush
