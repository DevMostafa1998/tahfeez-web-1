@extends('layouts.app')

@section('content')
    <style>
        .gradient-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .grade-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        .grade-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
        }

        .input-custom {
            border-radius: 10px !important;
            border: 2px solid #e2e8f0;
            font-size: 1.1rem;
            transition: all 0.2s;
        }

        .input-custom:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        /* كلاسات الألوان المتطابقة مع الداتابيز عندك */
        .card-excellent {
            background-color: rgba(25, 135, 84, 0.06) !important;
            border-right: 5px solid #198754 !important;
        }
        .text-excellent { color: #198754 !important; }

        .card-very_good {
            background-color: rgba(13, 110, 253, 0.06) !important;
            border-right: 5px solid #0d6efd !important;
        }
        .text-very_good { color: #0d6efd !important; }

        .card-good {
            background-color: rgba(13, 202, 240, 0.06) !important;
            border-right: 5px solid #0dcaf0 !important;
        }
        .text-good { color: #0dcaf0 !important; }

        .card-acceptable {
            background-color: rgba(255, 193, 7, 0.06) !important;
            border-right: 5px solid #ffc107 !important;
        }
        .text-acceptable { color: #ffc107 !important; }

        .card-fail {
            background-color: rgba(220, 53, 69, 0.06) !important;
            border-right: 5px solid #dc3545 !important;
        }
        .text-fail { color: #dc3545 !important; }
    </style>

    <div class="container my-5" dir="rtl">
        <div class="card shadow-lg border-0" style="border-radius: 24px; overflow: hidden;">

            <div class="card-header gradient-header text-white p-4 d-flex align-items-center justify-content-between border-0">
                <div class="d-flex align-items-center">
                    <div class="bg-warning text-dark rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 45px; height: 45px;">
                        <i class="bi bi-sliders fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold">الإعدادات العامة</h4>
                        <p class="mb-0 text-muted small text-white-50">نظام إدارة التقديرات والنسب المئوية الذكي</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 bg-light bg-opacity-50">
                <div class="alert alert-white border-0 shadow-sm d-flex align-items-center p-3 mb-4" style="border-radius: 16px;">
                    <div class="text-primary fs-3 me-3">
                        <i class="bi bi-info-circle-fill"></i>
                    </div>
                    <div class="text-secondary small">
                        يمكنك من خلال هذه اللوحة ضبط المديات الدنيا والعليا لكل تقدير. سيعتمد النظام تلقائياً على هذه النسب
                        لاحتساب تقدير الطالب فور إدخال النسبة المئوية في شاشات اختبارات التسميع.
                    </div>
                </div>

                <form action="{{ route('settings.grades.update') }}" method="POST" id="settingsForm">
                    @csrf

                    <div class="row g-4">
                        @foreach ($settings as $setting)
                            @php
                                $currentClass = $setting->text_class;

                                // الربط البرمجي الذكي بكلاسات الداتابيز الأصلية عندك
                                if(str_contains($currentClass, 'success')) { $colorKey = 'excellent'; $icon = 'bi-emoji-laughing'; }
                                elseif(str_contains($currentClass, 'primary')) { $colorKey = 'very_good'; $icon = 'bi-emoji-smile'; }
                                elseif(str_contains($currentClass, 'info')) { $colorKey = 'good'; $icon = 'bi-emoji-expressionless'; }
                                elseif(str_contains($currentClass, 'warning')) { $colorKey = 'acceptable'; $icon = 'bi-emoji-neutral'; }
                                else { $colorKey = 'fail'; $icon = 'bi-emoji-frown'; }
                            @endphp

                            <div class="col-xl-4 col-md-6">
                                <div class="card grade-card h-100 shadow-sm border-0 p-3 card-{{ $colorKey }}" style="border-radius: 18px;">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi {{ $icon }} text-{{ $colorKey }} fs-4 me-2"></i>
                                            <h5 class="fw-bold mb-0 text-{{ $colorKey }}">{{ $setting->grade_name }}</h5>
                                        </div>
                                        <span class="badge bg-white text-{{ $colorKey }} shadow-sm px-2.5 py-1.5 rounded-pill border">
                                            {{ $setting->status }}
                                        </span>
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small text-muted mb-1 fw-bold">الحد الأدنى</label>
                                            <div class="input-group">
                                                <input type="number" name="grades[{{ $setting->id }}][min_score]"
                                                    class="form-control text-center fw-bold input-custom"
                                                    value="{{ $setting->min_score }}" min="0" max="100" required>
                                                <span class="input-group-text bg-white border-0 fw-bold text-muted">%</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small text-muted mb-1 fw-bold">الحد الأعلى</label>
                                            <div class="input-group">
                                                <input type="number" name="grades[{{ $setting->id }}][max_score]"
                                                    class="form-control text-center fw-bold input-custom"
                                                    value="{{ $setting->max_score }}" min="0" max="100" required>
                                                <span class="input-group-text bg-white border-0 fw-bold text-muted">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 pt-3 border-top d-flex gap-3 justify-content-start">
                        <button type="submit" class="btn btn-success px-5 py-2.5 rounded-pill shadow-sm fw-bold border-0"
                            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class="bi bi-check-circle-fill me-2"></i> حفظ إعدادات النظام الحالية
                        </button>
                        <a href="{{ route('quran_tests.index') }}" class="btn btn-outline-secondary px-4 py-2.5 rounded-pill fw-bold">
                            إلغاء التغييرات
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('settingsForm');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                let isValid = true;
                const cards = form.querySelectorAll('.grade-card');

                cards.forEach(card => {
                    const inputs = card.querySelectorAll('input[type="number"]');
                    const min = parseInt(inputs[0].value);
                    const max = parseInt(inputs[1].value);
                    const label = card.querySelector('h5').innerText;

                    if (min > max) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ في التنسيق المنطقي!',
                            text: `في تقدير (${label})، لا يمكن أن يكون الحد الأدنى أكبر من الحد الأعلى.`,
                            confirmButtonText: 'تعديل الخطأ',
                            customClass: { confirmButton: 'btn btn-danger px-4 rounded-pill' }
                        });
                        isValid = false;
                    }
                });

                if (!isValid) return;

                Swal.fire({
                    title: 'جاري تحديث الإعدادات...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                const formData = new FormData(this);
                const actionUrl = this.getAttribute('action');

                fetch(actionUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم التحديث بنجاح!',
                                text: data.message,
                                confirmButtonText: 'حسناً',
                                timer: 2500,
                                timerProgressBar: true,
                                customClass: { confirmButton: 'btn btn-success px-4 rounded-pill' }
                            });
                        } else {
                            throw new Error();
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'فشل التحديث!',
                            text: 'حدث خطأ غير متوقع أثناء حفظ البيانات، يرجى التحقق من القيم المدخلة.',
                            confirmButtonText: 'إغلاق',
                            customClass: { confirmButton: 'btn btn-secondary px-4 rounded-pill' }
                        });
                    });
            });
        });
    </script>
@endpush
