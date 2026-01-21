@extends('layouts.app')

@section('title', 'تفاصيل المجموعة')

@push('css')
    {{-- استخدام أيقونات Bootstrap و SweetAlert2 للتنبيهات --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                            عدد الطلاب: {{ $group->students->count() }}
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
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="text-center">اسم الطالب</th>
                                <th class="text-center">العمر</th>
                                <th class="text-center">آخر سورة</th> {{-- عمود جديد --}}
                                <th class="text-center">آخر آية</th> {{-- عمود جديد --}}
                                <th class="text-center">رقم الهوية</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- تكرار الطلاب المرتبطين بالمجموعة --}}
                            @forelse($group->students as $student)
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
                                                        action="{{ route('memorization.store') }}" method="POST">
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
                                                                    <label class="form-label fw-bold text-secondary mb-2"
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
                                                                    <label class="form-label fw-bold text-secondary mb-2"
                                                                        style="display: block; width: 100%;">
                                                                        <i class="bi bi-book ms-1 text-primary"></i> اسم
                                                                        السورة
                                                                    </label>
                                                                    <input type="text" name="sura_name"
                                                                        value="{{ $student->latestMemorization->sura_name ?? '' }}"
                                                                        class="form-control form-control-lg border-2"
                                                                        placeholder="أدخل اسم السورة هنا" required
                                                                        style="border-radius: 10px; text-align: right; direction: rtl;">
                                                                </div>

                                                                {{-- الحقول الجانبية --}}
                                                                <div class="col-6" style="text-align: right;">
                                                                    <label class="form-label fw-bold text-secondary mb-2"
                                                                        style="display: block; width: 100%;">
                                                                        <i class="bi bi-hash ms-1 text-primary"></i> من آية
                                                                    </label>
                                                                    <input type="number" name="verses_from"
                                                                        value="{{ isset($student->latestMemorization->verses_from) ? $student->latestMemorization->verses_from : '' }}"
                                                                        class="form-control form-control-lg border-2"
                                                                        min="1" required
                                                                        style="border-radius: 10px; text-align: right; direction: rtl;">
                                                                </div>
                                                                <div class="col-6" style="text-align: right;">
                                                                    <label class="form-label fw-bold text-secondary mb-2"
                                                                        style="display: block; width: 100%;">
                                                                        <i class="bi bi-hash ms-1 text-primary"></i> إلى
                                                                        آية
                                                                    </label>
                                                                    <input type="number" name="verses_to"
                                                                        value="{{ isset($student->latestMemorization->verses_to) ? $student->latestMemorization->verses_to : '' }}"
                                                                        class="form-control form-control-lg border-2"
                                                                        min="1" required
                                                                        style="border-radius: 10px; text-align: right; direction: rtl;">
                                                                </div>

                                                                {{-- الملاحظات --}}
                                                                <div class="col-12" style="text-align: right;">
                                                                    <label class="form-label fw-bold text-secondary mb-2"
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

                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-info-circle d-block fs-2 mb-2"></i>
                                        لا يوجد طلاب في هذه المجموعة حالياً.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- استخدام jQuery و SweetAlert لإرسال البيانات دون تحديث الصفحة --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // نستخدم document.on للتعامل مع العناصر المنشأة ديناميكياً أو المتأثرة بالمودال
            $(document).on('submit', '.memorizationForm', function(e) {
                e.preventDefault();
                let form = $(this);
                let btn = form.find('button[type="submit"]');
                let modalId = form.closest('.modal').attr('id'); // نأخذ آيدي المودال لإغلاقه بدقة

                // جلب البيانات
                let studentId = form.find('input[name="student_id"]').val();
                let newSura = form.find('input[name="sura_name"]').val();
                let newVerseTo = form.find('input[name="verses_to"]').val();

                // زر التحميل
                let originalText = btn.text();
                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {

                        // 1. إغلاق المودال
                        $('#' + modalId).modal('hide');

                        // 2. رسالة النجاح
                        Swal.fire({
                            icon: 'success',
                            title: 'تم بنجاح',
                            text: 'تم تحديث البيانات في الجدول',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // 3. تحديث الجدول
                        let row = $('#row-student-' + studentId);

                        // فحص في الكونسول للتأكد من العثور على الصف
                        console.log('Target Row ID:', '#row-student-' + studentId);
                        console.log('Row Found:', row.length);

                        if (row.length > 0) {
                            // تحديث القيم
                            row.find('.sura-cell').text(newSura);
                            row.find('.verse-cell span').text(newVerseTo);

                            // تأثير وميض أصفر لتأكيد التحديث للمستخدم
                            row.css('background-color', '#fff3cd');
                            setTimeout(() => {
                                row.css('background-color', '');
                            }, 1000);

                            // تجهيز المودال للمرة القادمة
                            let nextStart = parseInt(newVerseTo) + 1;
                            form.find('input[name="verses_from"]').val(nextStart);
                            form.find('input[name="verses_to"]').val('');
                        } else {
                            console.error('لم يتم العثور على صف الطالب في الجدول');
                        }

                        btn.prop('disabled', false).text(originalText);
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text(originalText);
                        let errorMsg = 'حدث خطأ ما';
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
