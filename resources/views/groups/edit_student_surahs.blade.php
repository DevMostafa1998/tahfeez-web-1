@extends('layouts.app')

@section('title', 'سجل تسميع الطالب')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    .dataTables_wrapper .row:first-child {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between !important;
        align-items: center;
        width: 100%;
        margin: 0 0 1rem 0;
        padding: 0 15px;
    }

    .custom-edit-btn {
        background-color: #fff !important;
        color: #ffc107 !important;
        border: 2px solid #ffc107 !important;
        width: 38px;
        height: 38px;
        border-radius: 50% !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease-in-out;
        padding: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .custom-edit-btn:hover {
        background-color: #ffc107 !important;
        color: #fff !important;
        transform: scale(1.05);
    }

    .custom-edit-btn i {
        font-size: 0.95rem;
    }

    .btn-pill-custom {
        border-radius: 50px !important;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-pill-custom:hover {
        transform: scale(1.02);
    }

    .badge-custom {
        padding: 6px 16px !important;
        border-radius: 50px !important;
        font-weight: bold !important;
        font-size: 0.85rem !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start !important;
        }
        .dataTables_wrapper .row:first-child {
            flex-direction: column !important;
            gap: 10px;
        }
    }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    <div class="container-fluid p-3" dir="rtl">
        <div class="page-header d-flex justify-content-between align-items-center mb-3 px-3 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-white p-1 rounded-2 shadow-sm d-flex align-items-center justify-content-center border"
                    style="width: 40px; height: 40px;">
                    <i class="bi bi-journal-text fs-4 text-primary"></i>
                </div>
                <h1 class="page-title m-0 h4 fw-bold text-primary">
                    سجل تسميع الطالب: <span class="text-secondary">{{ $student->full_name }}</span>
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ url('group/' . $student->group_id) }}" class="btn btn-secondary btn-pill-custom shadow-sm px-4 py-2">
                    <i class="bi bi-arrow-right ms-1"></i> العودة لجدول الطلاب
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
            <div class="card-body p-0">
                <div class="p-3">
                    <div class="table-responsive">
                        <table id="surahRecordsTable" class="table table-striped table-bordered align-middle mb-0 text-center w-100">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th>التاريخ</th>
                                    <th>السورة</th>
                                    <th>من آية</th>
                                    <th>إلى آية</th>
                                    <th>النوع</th>
                                    <th style="width: 150px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $record)
                                <tr id="row-{{ $record->id }}">
                                    <td>{{ $record->date }}</td>
                                    <td class="sura-text">{{ $record->sura_name }}</td>
                                    <td class="from-text">{{ $record->verses_from }}</td>
                                    <td class="to-text">{{ $record->verses_to }}</td>

                                    <td class="type-zone">
                                        @if($record->examType_id == 1)
                                            <span class="badge bg-primary text-white badge-custom">تسميع</span>
                                        @elseif($record->examType_id == 2)
                                            <span class="badge bg-warning text-dark badge-custom">مراجعة</span>
                                        @elseif($record->examType_id == 3)
                                            <span class="badge bg-success text-white badge-custom">سرد سورة</span>
                                        @else
                                            <span class="badge bg-secondary text-white badge-custom">غير محدد</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn custom-edit-btn edit-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editRecordModal"
                                                    data-id="{{ $record->id }}"
                                                    data-sura="{{ $record->sura_name }}"
                                                    data-from="{{ $record->verses_from }}"
                                                    data-to="{{ $record->verses_to }}"
                                                    data-type="{{ $record->examType_id }}"
                                                    title="تعديل">
                                                <i class="fa-solid fa-pencil"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-muted py-4">لا توجد سجلات تسميع مسجلة لهذا الطالب حتى الآن.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editRecordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="editRecordForm" novalidate>
                @csrf
                <input type="hidden" id="record_id">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-info text-white border-0 py-3">
                        <h5 class="modal-title fw-bold">تعديل بيانات السورة</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-start" style="direction: rtl;">
                        <div class="row g-3 text-right">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">اسم السورة</label>
                                <select id="modal_sura_name" name="sura_name" class="form-control form-select" style="width: 100%;">
                                    @foreach($surahs as $surah)
                                        <option value="{{ $surah->name_ar }}" data-total-verses="{{ $surah->verses_count ?? $surah->verses ?? 7 }}">
                                            {{ $surah->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">نوع التسميع</label>
                                <select id="modal_examType_id" name="examType_id" class="form-control form-select" style="width: 100%;">
                                    <option value="1">تسميع</option>
                                    <option value="2">مراجعة</option>
                                    <option value="3">سرد سورة</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 text-right">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">من آية</label>
                                <input type="number" id="modal_verses_from" name="verses_from" class="form-control text-right" style="text-align: right;" min="1">
                                <div class="invalid-feedback fw-bold" id="error_verses_from"></div>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">إلى آية</label>
                                <input type="number" id="modal_verses_to" name="verses_to" class="form-control text-right" style="text-align: right;" min="1">
                                <div class="invalid-feedback fw-bold" id="error_verses_to"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-info text-white px-5 rounded-pill fw-bold">حفظ التغييرات</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap4.js"></script>

    <script>
    $(document).ready(function() {
        var table = $('#surahRecordsTable').DataTable({
            processing: true,
            language: {
                "sProcessing": "جاري التحميل...",
                "sLengthMenu": "أظهر _MENU_ سجلات",
                "sSearch": "بحث سريع:",
                "sInfo": "عرض _START_ إلى _END_ من أصل _TOTAL_ سجل",
                "paginate": { "next": "›", "previous": "‹" }
            },
            dom: "<'row mb-3 align-items-center'<'col-md-6 text-right'l><'col-md-6 text-left'f>>" +
                 "<'row'<'col-sm-12' tr >>" +
                 "<'row mt-3'<'col-sm-12'p>>",
        });

        if ($.fn.select2) {
            $('#modal_sura_name, #modal_examType_id').select2({
                dropdownParent: $('#editRecordModal'),
                dir: "rtl",
                width: '100%'
            });
        }

        function validateVersesLive() {
            var fromInput = $('#modal_verses_from');
            var toInput = $('#modal_verses_to');
            var fromVal = parseInt(fromInput.val()) || 0;
            var toVal = parseInt(toInput.val()) || 0;

            var selectedOption = $('#modal_sura_name').find(':selected');
            var maxVerses = parseInt(selectedOption.data('total-verses')) || 7;
            var suraName = $('#modal_sura_name').val();

            var isValid = true;

            fromInput.removeClass('is-invalid');
            toInput.removeClass('is-invalid');
            $('#error_verses_from').text('');
            $('#error_verses_to').text('');

            if (fromVal > maxVerses) {
                fromInput.addClass('is-invalid');
                $('#error_verses_from').text(`سورة ${suraName} بها ${maxVerses} آية فقط!`);
                isValid = false;
            }

            if (toVal > maxVerses) {
                toInput.addClass('is-invalid');
                $('#error_verses_to').text(`سورة ${suraName} بها ${maxVerses} آية فقط!`);
                isValid = false;
            }

            if (fromVal > 0 && toVal > 0 && fromVal > toVal) {
                fromInput.addClass('is-invalid');
                $('#error_verses_from').text('آية البداية لا يمكن أن تكون أكبر من آية النهاية.');
                isValid = false;
            }

            return isValid;
        }

        $('#modal_verses_from, #modal_verses_to').on('input', function() {
            validateVersesLive();
        });
        $('#modal_sura_name').on('change', function() {
            validateVersesLive();
        });

        $(document).on('click', '.edit-btn', function() {
            var id   = $(this).attr('data-id');
            var sura = $(this).attr('data-sura');
            var from = $(this).attr('data-from');
            var to   = $(this).attr('data-to');
            var type = $(this).attr('data-type');

            $('#record_id').val(id);
            $('#modal_sura_name').val(sura).trigger('change');
            $('#modal_examType_id').val(type).trigger('change');
            $('#modal_verses_from').val(from);
            $('#modal_verses_to').val(to);

            $('#modal_verses_from, #modal_verses_to').removeClass('is-invalid');
        });

        $('#editRecordForm').on('submit', function(e) {
            e.preventDefault();

            if (!validateVersesLive()) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في المدخلات',
                    text: 'يرجى تصحيح أرقام الآيات الخاطئة الموضحة باللون الأحمر قبل الحفظ.'
                });
                return false;
            }

            var id = $('#record_id').val();
            var formData = $(this).serialize();

            $.ajax({
                url: "/memorization/update-record/" + id,
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#editRecordModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'تم بنجاح',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(function(){
                            window.location.reload();
                        }, 1200);
                    } else {
                        Swal.fire({ icon: 'error', title: 'خطأ', text: response.message });
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'فشل التعديل', text: 'حدث خطأ، يرجى المحاولة لاحقاً.' });
                }
            });
        });
    });
    </script>
@endpush
