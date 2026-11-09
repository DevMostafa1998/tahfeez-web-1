@extends('layouts.app')

@section('title', 'إضافة طالب جديد')
@push('css')
@endpush
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
                        <form action="{{ route('student_public.store') }}" method="POST" id="createStudentForm"
                            autocomplete="off">
                            @csrf
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
                                        <div class="col-md-4 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-person-bounding-box text-primary me-1"></i>الاسم رباعي
                                            </label>
                                            <input type="text" name="full_name"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="الاسم الكامل" value="{{ old('full_name') }}" required>
                                        </div>
                                        <div class="col-md-2 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-person-vcard text-primary me-1"></i>رقم الهوية
                                            </label>
                                            <input type="text" name="id_number"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="9 أرقام" maxlength="9"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                value="{{ old('id_number') }}" required>
                                        </div>

                                        <div class="col-md-2 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-calendar-event text-primary me-1"></i>تاريخ الميلاد
                                            </label>
                                            <input type="date" name="date_of_birth"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                value="{{ old('date_of_birth') }}" required>
                                        </div>
                                        <div class="col-md-3 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-geo text-primary me-1"></i>مكان الميلاد
                                            </label>
                                            <input type="text" name="birth_place"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="المدينة/القرية" value="{{ old('birth_place') }}" required>
                                        </div>
                                        <div class="col-md-1 text-start">
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
                                        <div class="col-md-2 text-start">
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
                                                class="form-select form-select-sm border-0 bg-light px-3 py-2 fw-bold">
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
                                <div class="mb-2">
                                    <div class="d-flex align-items-center mb-3 text-indigo">
                                        <span class="badge rounded-pill bg-dark bg-opacity-10 text-dark px-3 py-2 fw-bold">
                                            <i class="bi bi-mosque me-1"></i> 3. البيانات الإدارية
                                        </span>
                                        <hr class="flex-grow-1 ms-3 my-0 opacity-10">
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-3 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-building-fill text-dark me-1"></i>اسم المركز
                                            </label>
                                            <input type="text" name="center_name"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="اسم المركز التعليمي"
                                                value="{{ old('center_name', 'أهل الرحمن') }}">
                                        </div>

                                        <div class="col-md-3 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-moon-stars-fill text-dark me-1"></i>اسم المسجد
                                            </label>
                                            <input type="text" name="mosque_name"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="اسم المسجد التابع له"
                                                value="{{ old('mosque_name', 'يافا') }}">
                                        </div>

                                        <div class="col-md-6 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-map-fill text-dark me-1"></i>عنوان المسجد بالتفصيل
                                            </label>
                                            <input type="text" name="mosque_address"
                                                class="form-control form-control-sm border-0 bg-light px-3 py-2"
                                                placeholder="وصف مكان المسجد"
                                                value="{{ old('mosque_address', 'دير البلح - حي بشارة') }}">
                                        </div>

                                        {{-- choose teachers & groups --}}
                                        <div class="col-md-6 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-person-fill text-dark me-1"></i>المعلم المسؤول
                                            </label>
                                            <select name="user_id" id="teacherSelect"
                                                class="form-select form-select-sm border-0 bg-light px-3 py-2 fw-bold"
                                                required>
                                                <option value="" selected disabled>اختر المعلم</option>
                                                @foreach ($teachers as $teacher)
                                                    <option value="{{ $teacher->id }}"
                                                        {{ old('user_id') == $teacher->id ? 'selected' : '' }}>
                                                        {{ $teacher->full_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 text-start">
                                            <label class="form-label small fw-bold text-secondary mb-1">
                                                <i class="bi bi-grid-fill text-dark me-1"></i>المجموعة (الحلقة)
                                            </label>
                                            <select name="group_id" id="groupSelect"
                                                class="form-select form-select-sm border-0 bg-light px-3 py-2 fw-bold"
                                                required>
                                                <option value="" selected disabled>اختر المجموعة</option>
                                                @foreach ($groups as $group)
                                                    <option value="{{ $group->id }}"
                                                        data-teacher-id="{{ $group->UserId }}"
                                                        {{ old('group_id') == $group->id ? 'selected' : '' }}
                                                        style="display: none;">
                                                        {{ $group->GroupName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <div class="card-footer bg-light border-0 py-3 rounded-bottom-4">
                                <div class="d-flex justify-content-end gap-2 ps-2">
                                    <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill shadow-sm">
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


@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#teacherSelect').on('change', function() {
                let teacherId = $(this).val();
                let groupSelect = $('#groupSelect');

                groupSelect.val('');

                groupSelect.find('option').each(function() {
                    let optionTeacherId = $(this).data('teacher-id');

                    if (optionTeacherId === undefined) {
                        $(this).show();
                    } else if (optionTeacherId == teacherId) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            if ($('#teacherSelect').val()) {
                $('#teacherSelect').trigger('change');
                $('#groupSelect').val("{{ old('group_id') }}");
            }
            $('#createStudentForm').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let submitBtn = form.find('button[type="submit"]');

                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span> جاري الحفظ...');

                let serializedData = form.serialize();

                let encodedPayload = btoa(serializedData);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: {
                        _token: form.find('input[name="_token"]').val(), // تمرير التوكن الأساسي
                        payload: encodedPayload
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                    icon: 'success',
                                    title: 'تم الحفظ!',
                                    text: response.message,
                                    confirmButtonText: 'حسناً'
                                })
                                .then(() => {
                                    window.location.href =
                                        "{{ route('student_public.index') }}";
                                });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(
                            '<i class="bi bi-check-circle-fill me-2"></i> حفظ بيانات الطالب'
                        );

                        if (xhr.status === 422) {
                            // أخطاء التحقق من البيانات (Validation)
                            let errorHtml = '<ul class="text-start small">';
                            $.each(xhr.responseJSON.errors, (key, value) => {
                                errorHtml += '<li>' + value[0] + '</li>';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ في البيانات',
                                html: errorHtml + '</ul>'
                            });
                        } else {
                            // إذا استمر خطأ 500، سنظهر رسالة الخطأ القادمة من السيرفر مباشرة لتشخيصها
                            let systemError =
                                'حدث خطأ غير متوقع في السيرفر (500)، يرجى مراجعة ملف لارافل log أو إعدادات ModSecurity.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                systemError = xhr.responseJSON.message;
                            }
                            Swal.fire('خطأ في السيرفر!', systemError, 'error');
                        }
                    }
                });
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#0dcaf0',
                    timer: 2500
                });
            @endif
        });
    </script>
@endpush
