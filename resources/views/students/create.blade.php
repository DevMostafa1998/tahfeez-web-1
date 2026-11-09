@extends('layouts.app')

@section('title', 'إضافة طالب جديد')

@section('content')
    <div class="app-content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6 text-start">
                    <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-person-plus-fill text-primary me-2"></i>إضافة طالب جديد
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end bg-transparent p-0 m-0 small">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">الرئيسية</a></li>
                        <li class="breadcrumb-item active" aria-current="page">إضافة طالب</li>
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

                        <form action="{{ route('student.store') }}" method="POST" id="createStudentForm" autocomplete="off"
                            novalidate>
                            @csrf
                            <div class="card-body p-4">

                                {{-- القسم الأول: المعلومات الأساسية --}}
                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-3 text-primary">
                                        <span
                                            class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 fw-bold">
                                            <i class="bi bi-person-badge-fill me-1"></i> 1. البيانات الشخصية
                                        </span>
                                        <hr class="flex-grow-1 ms-3 my-0 opacity-10">
                                    </div>

                                    <div class="row g-3">
                                        {{-- الاسم رباعي --}}
                                        <div class="col-md-3 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-person-bounding-box text-primary me-1"></i>الاسم رباعي
                                            </label>
                                            <input type="text" name="full_name"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="الاسم الكامل" value="{{ old('full_name') }}" required>
                                        </div>

                                        {{-- رقم هوية الطالب --}}
                                        <div class="col-md-3 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-person-vcard text-primary me-1"></i>هوية الطالب
                                            </label>
                                            <input type="text" name="id_number"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="9 أرقام" maxlength="9"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                value="{{ old('id_number', $student->id_number ?? '') }}" required>
                                        </div>

                                        {{-- رقم هوية الأب --}}
                                        <div class="col-md-3 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-person-vcard-fill text-primary me-1"></i>هوية الأب
                                            </label>
                                            <input type="text" name="father_id"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="9 أرقام" maxlength="9"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                value="{{ old('father_id', $student->father_id ?? '') }}" required>
                                        </div>

                                        {{-- تاريخ الميلاد --}}
                                        <div class="col-md-3 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-calendar-event text-primary me-1"></i>تاريخ الميلاد
                                            </label>
                                            <input type="date" name="date_of_birth"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                value="{{ old('date_of_birth') }}" required>
                                        </div>

                                        {{-- مكان الميلاد --}}
                                        <div class="col-md-3 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-geo text-primary me-1"></i>مكان الميلاد
                                            </label>
                                            <input type="text" name="birth_place"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="المدينة" value="{{ old('birth_place') }}" required>
                                        </div>

                                        {{-- الجنس --}}
                                        <div class="col-md-3 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-gender-ambiguous text-primary me-1"></i>الجنس
                                            </label>
                                            <select name="gender"
                                                class="form-select form-select-sm border-0 bg-light px-3 py-2 fw-bold"
                                                required>
                                                <option value="" selected disabled>اختر</option>
                                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر
                                                </option>
                                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>
                                                    أنثى</option>
                                            </select>
                                        </div>

                                        {{-- نوع التصنيف (الحقل الجديد) --}}
                                        <div class="col-md-3 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-tags-fill text-primary me-1"></i>نوع التصنيف
                                            </label>
                                            <select name="categorie_id"
                                                class="form-select form-select-sm border-0 bg-light px-3 py-2 fw-bold"
                                                required>
                                                <option value="" selected disabled>اختر التصنيف</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('categorie_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- القسم الثاني: التواصل والعنوان --}}
                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-3 text-success">
                                        <span
                                            class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 fw-bold">
                                            <i class="bi bi-telephone-fill me-1"></i> 2. التواصل والعنوان
                                        </span>
                                        <hr class="flex-grow-1 ms-3 my-0 opacity-10">
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-2 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-phone-fill text-success me-1"></i>الجوال
                                            </label>
                                            <input type="tel" name="phone_number"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="05XXXXXXXX" maxlength="10"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                value="{{ old('phone_number') }}" required>
                                        </div>
                                        <div class="col-md-2 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-whatsapp text-success me-1"></i>واتساب
                                            </label>
                                            <input type="tel" name="whatsapp_number"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="05XXXXXXXX" maxlength="15"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                value="{{ old('whatsapp_number') }}">
                                        </div>
                                        <div class="col-md-5 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-pin-map-fill text-success me-1"></i>عنوان السكن الحالي
                                            </label>
                                            <input type="text" name="address"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="المدينة، الحي، رقم المنزل" value="{{ old('address') }}"
                                                required>
                                        </div>
                                        <div class="col-md-3 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-house-door-fill text-success me-1"></i>حالة السكن
                                            </label>
                                            <select name="is_displaced"
                                                class="form-select form-select-sm border-0 bg-light px-3 py-2 fw-bold"
                                                required>
                                                <option value="" selected disabled>اختر...</option>
                                                <option value="0" {{ old('is_displaced') == '0' ? 'selected' : '' }}>
                                                    مقيم</option>
                                                <option value="1" {{ old('is_displaced') == '1' ? 'selected' : '' }}>
                                                    نازح</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                               {{-- القسم الثالث: المسجد والمركز --}}
                                <div class="row g-3">
                                    <div class="col-md-3 text-start">
                                        <label class="form-label small fw-bold text-secondary mb-1">اسم المركز</label>
                                    <select name="center_id" class="form-select form-select-sm border-0 bg-light px-3 py-2" style="border-radius: 8px;">
                                    <option value="" disabled selected>اختر المركز...</option>
                                    @foreach ($centers as $center)
                                        <option value="{{ $center->id }}" {{ old('center_id') == $center->id ? 'selected' : ($center->id == 1 ? 'selected' : '') }}>
                                            {{ $center->name }}
                                        </option>
                                    @endforeach
                                </select>
                                    </div>

                                <div class="col-md-3 text-start">
                                    <label class="form-label small fw-bold text-secondary mb-1">اسم المسجد</label>
                                    <select name="mosque_name" id="mosque_name" class="form-select form-select-sm border-0 bg-light px-3 py-2" required>
                                        <option value="">اختر المسجد...</option>
                                        @foreach ($mosques as $mosque)
                                            <option value="{{ $mosque->description }}">{{ $mosque->description }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 text-start">
                                    <label class="form-label small fw-bold text-secondary mb-1">عنوان المسجد بالتفصيل</label>
                                    <input type="text" name="mosque_address" id="mosque_address"
                                        class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                        placeholder="مثال: دير البلح - حي بشارة" required>
                                </div>
                            </div>
                            <div class="card-footer bg-light border-0 py-3 rounded-bottom-4">
                                <div class="d-flex justify-content-end gap-2 ps-2">
                                    <button type="submit" id="submitBtn"
                                        class="btn btn-primary px-5 fw-bold rounded-pill shadow-sm">
                                        <i class="bi bi-check-circle-fill me-2"></i>حفظ بيانات الطالب
                                    </button>
                                    <a href="{{ route('student.index') }}"
                                        class="btn btn-outline-secondary px-4 rounded-pill">إلغاء</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const form = document.getElementById('createStudentForm');
        const submitBtn = document.getElementById('submitBtn');

        if (!form) return;

        const fieldLabels = {
            'full_name': 'حقل الاسم رباعي مطلوب.',
            'id_number': 'حقل هوية الطالب مطلوب.',
            'father_id': 'حقل هوية الأب مطلوب.',
            'date_of_birth': 'حقل تاريخ الميلاد مطلوب.',
            'birth_place': 'حقل مكان الميلاد مطلوب.',
            'gender': 'يرجى اختيار الجنس.',
            'categorie_id': 'يرجى اختيار نوع التصنيف.',
            'phone_number': 'حقل رقم الجوال مطلوب.',
            'whatsapp_number': 'حقل رقم الواتساب مطلوب.',
            'address': 'حقل عنوان السكن الحالي مطلوب.',
            'is_displaced': 'يرجى تحديد حالة السكن.'
        };

        function clearError(target) {
            target.classList.remove('is-invalid');
            target.style.border = "none";
            let errorMsg = target.nextElementSibling;
            if (errorMsg && errorMsg.classList.contains('custom-error-msg')) {
                errorMsg.remove();
            }
        }

        form.addEventListener('input', function(e) {
            clearError(e.target);
        });
        form.addEventListener('change', function(e) {
            clearError(e.target);
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
                el.style.border = "none";
            });
            document.querySelectorAll('.custom-error-msg').forEach(el => el.remove());

            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span> جاري الحفظ...';

            let formData = new FormData(form);

            if (formData.has('full_name') && formData.get('full_name')) {
                let rawName = formData.get('full_name').replace(/\s+/g, ' ').trim();

                let encodedName = btoa(encodeURIComponent(rawName).replace(/%([0-9A-F]{2})/g, function(match, p1) {
                    return String.fromCharCode('0x' + p1);
                }));

                formData.set('full_name', encodedName);
            }

            fetch(form.getAttribute('action'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    return response.json().then(data => ({
                        status: response.status,
                        body: data
                    }));
                })
                .then(res => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML =
                        '<i class="bi bi-check-circle-fill me-2"></i> حفظ بيانات الطالب';

                    if (res.status === 422) {
                        Object.keys(res.body.errors).forEach(key => {
                            let inputField = document.querySelector(`[name="${key}"]`);
                            if (inputField) {
                                inputField.classList.add('is-invalid');
                                inputField.style.border = "1px solid #dc3545";
                                let arabicMessage = fieldLabels[key] || res.body.errors[key][0];

                                let nextEl = inputField.nextElementSibling;
                                if (!nextEl || !nextEl.classList.contains('custom-error-msg')) {
                                    let errorDiv = document.createElement('div');
                                    errorDiv.className =
                                        'custom-error-msg d-block text-danger small mt-1 fw-bold';
                                    errorDiv.innerText = arabicMessage;
                                    inputField.after(errorDiv);
                                }
                            }
                        });

                    } else if (res.body.success) {
                        // نافذة النجاح تظهر لتأكيد الحفظ فقط
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحفظ بنجاح!',
                            text: res.body.message,
                            confirmButtonText: 'حسناً'
                        }).then(() => {
                            form.reset();
                            document.querySelector('[name="center_name"]').value = "اهل الرحمن";
                            document.querySelector('[name="mosque_name"]').value = "يافا";
                            document.querySelector('[name="mosque_address"]').value = "دير البلح - حي بشارة";
                        });
                    } else {
                        Swal.fire('خطأ!', 'حدث خطأ غير متوقع، يرجى المحاولة لاحقاً', 'error');
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML =
                        '<i class="bi bi-check-circle-fill me-2"></i> حفظ بيانات الطالب';
                    Swal.fire('خطأ!', 'فشل الاتصال بالسيرفر، تأكد من اتصالك بالشبكة', 'error');
                });
        });
    });
</script>

