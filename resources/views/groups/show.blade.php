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
                            {{-- اسم المجموعة من مودل Group --}}
                            <h2 class="fw-bold mb-1">{{ $group->GroupName }}</h2>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-person-badge me-1"></i>
                                {{-- اسم المحفظ المرتبط بالمجموعة --}}
                                المحفظ: <strong>{{ $group->teacher->full_name ?? 'غير محدد' }}</strong>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-white text-primary px-3 py-2 fs-6 shadow-sm">
                            عدد الطلاب: {{ $students->total() }}
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
                        {{-- إضافة كلاس table-bordered لرسم الخطوط --}}
                        <table id="studentsTable" class="table table-bordered table-hover align-middle mb-0 shadow-sm">
                            <thead class="bg-light"> {{-- ضروري جداً لتثبيت الأسهم --}}
                                <tr>
                                    <th class="text-center py-3">اسم الطالب/ة</th>
                                    <th class="text-center py-3">العمر</th>
                                    <th class="text-center py-3">آخر سورة</th>
                                    <th class="text-center py-3">آخر آية</th>
                                    <th class="text-center py-3">رقم الهوية</th>
                                    <th class="text-center py-3">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- تكرار الطلاب المرتبطين بالمجموعة --}}
                                @foreach ($students as $student)
                                    <tr id="row-student-{{ $student->id }}">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center fw-bold text-secondary shadow-sm"
                                                    style="width: 40px; height: 40px;">
                                                    {{ mb_substr($student->full_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    {{-- اسم الطالب من مودل Student --}}
                                                    <div class="fw-bold text-dark">{{ $student->full_name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            {{-- حساب العمر برمجياً من تاريخ الميلاد --}}
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
                                        <td class="text-center text-muted">
                                            {{ $student->id_number }}
                                        </td>
                                        <td class="text-center">
                                            {{-- زر فتح مودال تسجيل الحفظ --}}
                                            <button
                                                class="btn btn-success btn-sm px-3 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#memorizeModal{{ $student->id }}">
                                                <i class="bi bi-journal-plus"></i> تسجيل حفظ
                                            </button>
                                            <div class="modal fade" id="memorizeModal{{ $student->id }}" tabindex="-1"
                                                aria-hidden="true" dir="rtl">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg"
                                                        style="border-radius: 15px; text-align: right;">
                                                        <form class="memorizationForm"
                                                            action="{{ route('memorization.store') }}" method="POST"
                                                            novalidate>
                                                            @csrf
                                                            <input type="hidden" name="student_id"
                                                                value="{{ $student->id }}">

                                                            {{-- رأس المودال --}}
                                                            <div class="modal-header border-0 bg-primary text-white"
                                                                style="border-radius: 15px 15px 0 0; flex-direction: row-reverse;">
                                                                <h5 class="modal-title fw-bold m-0"
                                                                    style="text-align: right; width: 100%;">
                                                                    <i class="bi bi-journal-plus ms-2"></i>تسجيل حفظ:
                                                                    {{ $student->full_name }}
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white m-0"
                                                                    data-bs-dismiss="modal" aria-label="Close"
                                                                    style="margin-right: auto !important; margin-left: 0 !important;"></button>
                                                            </div>

                                                            <div class="modal-body p-4">
                                                                <div class="row g-4">

                                                                    {{-- تاريخ اليوم --}}
                                                                    <div class="col-12" style="text-align: right;">
                                                                        <label
                                                                            class="form-label fw-bold text-secondary mb-2"
                                                                            style="display: block; width: 100%;">
                                                                            <i
                                                                                class="bi bi-calendar-event ms-1 text-primary"></i>
                                                                            تاريخ
                                                                            اليوم
                                                                        </label>
                                                                        <input type="date" name="date"
                                                                            class="form-control form-control-lg border-2"
                                                                            value="{{ date('Y-m-d') }}" required
                                                                            style="border-radius: 10px; text-align: right; direction: rtl;">
                                                                    </div>

                                                                    {{-- اسم السورة --}}
                                                                    <div class="col-12" style="text-align: right;">
                                                                        <label
                                                                            class="form-label fw-bold text-secondary mb-2"
                                                                            style="display: block; width: 100%;">
                                                                            <i class="bi bi-book ms-1 text-primary"></i>
                                                                            اسم
                                                                            السورة
                                                                        </label>
                                                                        {{-- تحويل الحقل إلى select مع كلاس surah-select --}}
                                                                        <select name="sura_name"
                                                                            class="form-select surah-select" required
                                                                            style="width: 100%;">
                                                                            <option value="">ابحث عن اسم السورة...
                                                                            </option>
                                                                            @foreach ($surahs as $surah)
                                                                                <option value="{{ $surah->name_ar }}"
                                                                                    data-verses="{{ $surah->verses_count }}"
                                                                                    {{-- إضافة عدد الآيات هنا --}}
                                                                                    {{ isset($student->latestMemorization->sura_name) && $student->latestMemorization->sura_name == $surah->name_ar ? 'selected' : '' }}>
                                                                                    {{ $surah->number }}.
                                                                                    {{ $surah->name_ar }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    {{-- الحقول الجانبية --}}
                                                                    <div class="col-6" style="text-align: right;">
                                                                        <label
                                                                            class="form-label fw-bold text-secondary mb-2"
                                                                            style="display: block; width: 100%;">
                                                                            <i class="bi bi-hash ms-1 text-primary"></i> من
                                                                            آية
                                                                        </label>
                                                                        <input type="number" name="verses_from"
                                                                            {{-- المنطق: إذا وجد حفظ سابق، ابدأ من الآية التالية مباشرة --}}
                                                                            value="{{ isset($student->latestMemorization->verses_to) ? $student->latestMemorization->verses_to + 1 : 1 }}"
                                                                            class="form-control form-control-lg border-2"
                                                                            min="1" required
                                                                            style="border-radius: 10px; text-align: right; direction: rtl;">
                                                                    </div>

                                                                    <div class="col-6" style="text-align: right;">
                                                                        <label
                                                                            class="form-label fw-bold text-secondary mb-2"
                                                                            style="display: block; width: 100%;">
                                                                            <i class="bi bi-hash ms-1 text-primary"></i>
                                                                            إلى
                                                                            آية
                                                                        </label>
                                                                        {{-- حقل "إلى آية" نتركه فارغاً دائماً ليبدأ المعلم بالإدخال --}}
                                                                        <input type="number" name="verses_to"
                                                                            value=""
                                                                            class="form-control form-control-lg border-2"
                                                                            min="1" required
                                                                            style="border-radius: 10px; text-align: right; direction: rtl;">
                                                                    </div>

                                                                    {{-- الملاحظات --}}
                                                                    <div class="col-12" style="text-align: right;">
                                                                        <label
                                                                            class="form-label fw-bold text-secondary mb-2"
                                                                            style="display: block; width: 100%;">
                                                                            <i
                                                                                class="bi bi-chat-left-text ms-1 text-primary"></i>
                                                                            ملاحظات المعلم
                                                                        </label>
                                                                        <textarea name="note" class="form-control border-2" rows="3" placeholder="أضف ملاحظاتك هنا..."
                                                                            style="border-radius: 10px; text-align: right; direction: rtl;"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer border-0 bg-light p-3 d-flex"
                                                                style="border-radius: 0 0 15px 15px; justify-content: flex-start; flex-direction: row-reverse;">
                                                                <button type="submit"
                                                                    class="btn btn-primary px-5 py-2 fw-bold shadow-sm"
                                                                    style="border-radius: 10px;">
                                                                    حفظ البيانات
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary px-4 py-2 fw-bold ms-2"
                                                                    data-bs-dismiss="modal" style="border-radius: 10px;">
                                                                    إلغاء
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- مودال تسجيل الحفظ لكل طالب --}}
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- @if ($students->hasPages())
                    <div class="card-footer bg-white border-top-0 py-3">
                        <div class="d-flex justify-content-end">
                            {!! $students->links('pagination::bootstrap-5') !!}
                        </div>
                    </div>
                @endif --}}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- استخدام jQuery و SweetAlert لإرسال البيانات دون تحديث الصفحة --}}
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap4.js"></script>

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
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
                        "targets": 5
                    },
                    {
                        "searchable": false,
                        "targets": 5
                    },
                    {
                        "className": "text-center",
                        "targets": "_all"
                    }
                ]
            });

            $('.modal').on('shown.bs.modal', function() {
                let modal = $(this);
                let surahSelect = modal.find('.surah-select');
                let vFromInput = modal.find('input[name="verses_from"]');
                let vToInput = modal.find('input[name="verses_to"]');

                modal.find('form').attr('novalidate', true);

                surahSelect.select2({
                    dropdownParent: modal,
                    dir: "rtl",
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "لا توجد نتائج";
                        }
                    }
                });

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

            $(document).on('input', 'input[name="verses_to"], input[name="verses_from"]', function() {
                let max = parseInt($(this).attr('max'));
                let val = parseInt($(this).val());
                let errorId = $(this).attr('name') + '_error';

                $('#' + errorId).remove();

                if (max && val > max) {
                    $(this).addClass('is-invalid');
                    $(this).after(
                        `<div id="${errorId}" class="text-danger small mt-1 fw-bold" style="display:block; width:100%;">يجب أن يكون ${max} أو أقل</div>`
                    );
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            $('.modal').on('hide.bs.modal', function() {
                $(this).find('textarea[name="note"]').val('');
                $(this).find('input').removeClass('is-invalid');
                $('.text-danger.small').remove(); // حذف رسائل الخطأ النصية
            });

            //  معالجة إرسال نموذج الحفظ عبر AJAX
            $(document).on('submit', '.memorizationForm', function(e) {
                e.preventDefault();
                let form = $(this);
                let btn = form.find('button[type="submit"]');
                let modalId = form.closest('.modal').attr('id');

                // --- منطق التحقق من عدد الآيات قبل الإرسال ---
                let vFromInput = form.find('input[name="verses_from"]');
                let vToInput = form.find('input[name="verses_to"]');
                let max = parseInt(vToInput.attr('max'));

                let vFromVal = parseInt(vFromInput.val());
                let vToVal = parseInt(vToInput.val());

                if (max) {
                    if (vFromVal > max || vToVal > max) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'خطأ في عدد الآيات',
                            text: 'السورة المختارة تحتوي على ' + max + ' آية فقط.',
                            confirmButtonText: 'حسناً'
                        });
                        return false;
                    }
                }

                let studentId = form.find('input[name="student_id"]').val();
                let newSura = form.find('select[name="sura_name"] option:selected').val();
                let newVerseTo = vToInput.val();
                let originalText = btn.text();

                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#' + modalId).modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'تم بنجاح',
                            text: 'تم تسجيل الحفظ وتحديث السجل',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        let row = $('#row-student-' + studentId);
                        if (row.length > 0) {
                            row.find('.sura-cell').text(newSura);
                            row.find('.verse-cell span').text(newVerseTo);

                            row.addClass('table-warning');
                            setTimeout(() => {
                                row.removeClass('table-warning');
                            }, 2000);

                            let nextStart = parseInt(newVerseTo) + 1;
                            form.find('input[name="verses_from"]').val(nextStart);
                            form.find('input[name="verses_to"]').val('');
                            form.find('textarea[name="note"]').val('');
                        }

                        btn.prop('disabled', false).text(originalText);
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text(originalText);
                        let errorMsg = 'حدث خطأ أثناء الاتصال بالخادم';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMsg = Object.values(xhr.responseJSON.errors)[0][0];
                        }
                        Swal.fire('خطأ!', errorMsg, 'error');
                    }
                });
            });
        });
    </script>
@endpush
