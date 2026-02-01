@extends('layouts.app')

@section('title', 'إضافة مستخدم جديد')

@section('content')
    <div class="app-content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6 text-start">
                    <h3 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-person-plus-fill text-primary me-2"></i>إضافة مستخدم جديد للنظام
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end bg-transparent p-0 m-0 small">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">الرئيسية</a></li>
                        <li class="breadcrumb-item active" aria-current="page">إضافة مستخدم</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="border-radius: 20px; border-top: 5px solid #0d6efd !important;">
                        <form action="{{ route('user.store') }}" method="POST" id="createUserForm" autocomplete="off">
                            @csrf
                            <input type="password" style="display:none">
                            <div class="card-body p-4">

                                @if ($errors->any())
                                    <div class="alert alert-danger border-0 shadow-sm mb-4"
                                        style="border-radius: 12px; border-right: 5px solid #dc3545;">
                                        <div class="d-flex align-items-center mb-1 fw-bold">
                                            <i class="bi bi-exclamation-octagon-fill me-2"></i>يرجى تصحيح الأخطاء
                                        </div>
                                        <ul class="mb-0 small">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- القسم الأول: إعدادات الحساب والصلاحيات (تم نقله للأعلى) --}}
                                <div class="mb-5 p-3 rounded-4"
                                    style="background-color: #f8f9fa; border: 1px dashed #dee2e6;">
                                    <div class="d-flex align-items-center mb-3 text-danger">
                                        <span
                                            class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2 fw-bold">
                                            <i class="bi bi-shield-lock-fill me-1"></i> 1. إعدادات الحساب والصلاحيات
                                        </span>
                                        <hr class="flex-grow-1 ms-3 my-0 opacity-10">
                                    </div>
                                    <div class="row g-3 text-start">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-tags text-danger me-1"></i>تصنيف المستخدم
                                            </label>
                                            <select name="is_admin" id="userRoleSelect"
                                                class="form-select form-select-sm border-0 bg-white px-3 py-2 fw-bold shadow-sm"
                                                required>
                                                {{-- تم جعل "محفظ" هو الخيار الافتراضي --}}
                                                <option value="محفظ" {{ old('is_admin') == 'محفظ' || !old('is_admin') ? 'selected' : '' }}>
                                                    محفظ</option>
                                                <option value="مسؤول" {{ old('is_admin') == 'مسؤول' ? 'selected' : '' }}>
                                                    مسؤول</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-grid-3x3-gap text-danger me-1"></i>نوع التصنيف
                                            </label>
                                            <select name="category_id"
                                                class="form-select form-select-sm border-0 bg-white px-3 py-2 fw-bold shadow-sm"
                                                required>
                                                <option value="" selected disabled>اختر النوع ...</option>
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat->id }}"
                                                        {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- كلمة المرور --}}
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-key text-danger me-1"></i>كلمة المرور
                                            </label>
                                            <div class="input-group input-group-sm shadow-sm has-validation">
                                                <input type="password" id="password" name="password"
                                                    class="form-control border-0 bg-white px-3 py-2" required
                                                    autocomplete="new-password">

                                            </div>
                                        </div>
                                        {{-- تأكيد كلمة المرور --}}
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-check2-circle text-danger me-1"></i>تأكيد كلمة المرور
                                            </label>
                                            <div class="input-group input-group-sm shadow-sm has-validation">
                                                <input type="password" id="password_confirmation"
                                                    name="password_confirmation"
                                                    class="form-control border-0 bg-white px-3 py-2" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- القسم الثاني: المعلومات الشخصية --}}
                                <div class="mb-5">
                                    <div class="d-flex align-items-center mb-3 text-primary">
                                        <span
                                            class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 fw-bold">
                                            <i class="bi bi-info-circle-fill me-1"></i> 2. المعلومات الشخصية
                                        </span>
                                        <hr class="flex-grow-1 ms-3 my-0 opacity-10">
                                    </div>
                                    <div class="row g-3 text-start">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-person text-primary me-1"></i>الاسم رباعي
                                            </label>
                                            <input type="text" name="full_name"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2 only-text"
                                                placeholder="أدخل الاسم رباعي" value="{{ old('full_name') }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-card-heading text-primary me-1"></i>رقم الهوية
                                            </label>
                                            <input type="text" name="id_number"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2 only-numbers"
                                                placeholder="رقم الهوية" value="{{ old('id_number') }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-calendar3 text-primary me-1"></i>تاريخ الميلاد
                                            </label>
                                            <input type="date" name="date_of_birth"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                value="{{ old('date_of_birth') }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-geo text-primary me-1"></i>مكان الميلاد
                                            </label>
                                            <input type="text" name="birth_place"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="مدينة الميلاد" value="{{ old('birth_place') }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- القسم الثالث: التواصل والعنوان --}}
                                <div class="mb-5">
                                    <div class="d-flex align-items-center mb-3 text-success">
                                        <span
                                            class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 fw-bold">
                                            <i class="bi bi-telephone-fill me-1"></i> 3. بيانات التواصل والعنوان
                                        </span>
                                        <hr class="flex-grow-1 ms-3 my-0 opacity-10">
                                    </div>
                                    <div class="row g-3 text-start">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-telephone text-success me-1"></i>رقم الهاتف
                                            </label>
                                            <input type="tel" name="phone_number"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2 only-numbers"
                                                placeholder="05XXXXXXXX" value="{{ old('phone_number') }}" required>
                                        </div>

                                        {{-- حقول سيتم إخفاؤها للمسؤول --}}
                                        <div class="col-md-3 toggle-for-admin">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-whatsapp text-success me-1 "></i>رقم الواتساب
                                            </label>
                                            <input type="tel" name="whatsapp_number"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2 only-numbers"
                                                placeholder="05XXXXXXXX" value="{{ old('whatsapp_number') }}">
                                        </div>
                                        <div class="col-md-3 toggle-for-admin">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-wallet2 text-success me-1"></i>رقم المحفظة
                                            </label>
                                            <input type="text" name="wallet_number"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2 only-numbers"
                                                placeholder="رقم المحفظة الإلكترونية" value="{{ old('wallet_number') }}">
                                        </div>
                                        <div class="col-md-3 toggle-for-admin">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-mosque text-success me-1"></i>اسم المسجد
                                            </label>
                                            <input type="text" name="mosque_name"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2 only-text"
                                                placeholder="اسم المسجد" value="{{ old('mosque_name') }}">
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-house-door-fill text-success me-1"></i>حالة السكن
                                            </label>
                                            <select name="is_displaced"
                                                class="form-select form-select-sm border-0 bg-light px-3 py-2 fw-bold"
                                                required>
                                                <option value="0" {{ old('is_displaced') == '0' ? 'selected' : '' }}>
                                                    مقيم</option>
                                                <option value="1" {{ old('is_displaced') == '1' ? 'selected' : '' }}>
                                                    نازح</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-geo-alt text-success me-1"></i>العنوان الحالي
                                            </label>
                                            <input type="text" name="address"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="المدينة، الحي" value="{{ old('address') }}" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- القسم الرابع: البيانات العلمية (يختفي بالكامل عند اختيار مسؤول) --}}
                                <div class="mb-5" id="scientificSection">
                                    <div class="d-flex align-items-center mb-3 text-dark">
                                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2 fw-bold shadow-sm">
                                            <i class="bi bi-mortarboard-fill me-1"></i> 4. البيانات العلمية والقرآنية
                                        </span>
                                        <hr class="flex-grow-1 ms-3 my-0 opacity-25" style="border-top: 2px solid #ffc107;">
                                    </div>
                                    <div class="row g-3 text-start">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-award text-dark me-1"></i>المؤهل العلمي
                                            </label>
                                            <input type="text" name="qualification"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2 only-text"
                                                placeholder="مثلاً: بكالوريوس" value="{{ old('qualification') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-book text-dark me-1"></i>التخصص
                                            </label>
                                            <input type="text" name="specialization"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2 only-text"
                                                placeholder="التخصص الجامعي" value="{{ old('specialization') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-journal-check text-dark me-1"></i>عدد الأجزاء المحفوظة
                                            </label>
                                            <input type="number" name="parts_memorized"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="0" min="0" max="30"
                                                value="{{ old('parts_memorized', 0) }}">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer bg-light border-0 py-3 rounded-bottom-4">
                                <div class="d-flex justify-content-end gap-2 ps-2">
                                    <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill shadow-sm">
                                        <i class="bi bi-check-circle-fill me-2"></i>حفظ بيانات المستخدم
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary px-4 rounded-pill">إعادة
                                        تعيين</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. التحكم في إخفاء الحقول بناءً على الصلاحية
            const roleSelect = document.getElementById('userRoleSelect');
            const scientificSection = document.getElementById('scientificSection');
            const fieldsToToggle = document.querySelectorAll('.toggle-for-admin');

            function toggleFields() {
                if (roleSelect.value === 'مسؤول') {
                    scientificSection.style.display = 'none';

                    fieldsToToggle.forEach(el => el.style.display = 'none');
                } else {
                    // إظهار القسم العلمي
                    scientificSection.style.display = 'block';
                    // إظهار الحقول المخفية
                    fieldsToToggle.forEach(el => el.style.display = 'block');
                }
            }


            if (roleSelect) {
                toggleFields();
                roleSelect.addEventListener('change', toggleFields);
            }

     
            const form = document.getElementById('createUserForm');
            const requiredInputs = document.querySelectorAll('[required]');
            requiredInputs.forEach(input => {
                input.addEventListener('invalid', function() {
                    if (this.validity.valueMissing) {
                        this.setCustomValidity('هذا الحقل مطلوب، يرجى ملؤه');
                    } else if (this.validity.typeMismatch) {
                        this.setCustomValidity('تنسيق الإدخال غير صحيح');
                    } else {
                        this.setCustomValidity('يرجى التحقق من القيمة المدخلة');
                    }
                });
            });

            document.querySelectorAll('.only-text').forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^a-zA-Z\u0600-\u06FF\s]/g, '');
                });
            });

            document.querySelectorAll('.only-numbers').forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });

            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');
            if (password && confirmPassword) {
                confirmPassword.addEventListener('input', function() {
                    if (this.value !== password.value) {
                        this.setCustomValidity('كلمات المرور غير متطابقة');
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }

            if (document.activeElement) {
                document.activeElement.blur();
            }
        });
    </script>
@endsection
