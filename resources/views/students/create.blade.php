@extends('layouts.app')

@section('title', 'إضافة طالب جديد')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold">إضافة طالب جديد</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
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
                    <div class="card card-outline card-primary shadow-sm"
                        style="border-radius: 15px; border-top: 4px solid #007bff;">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title fw-bold text-secondary mb-0">بيانات الطالب الأساسية</h5>
                        </div>

                        <div class="card-body p-4">
                            <form action="{{ route('student.store') }}" method="POST" id="createStudentForm">
                                @csrf

                                @if ($errors->any())
                                    <div class="alert alert-danger shadow-sm mb-4"
                                        style="border-radius: 12px; border-right: 5px solid #dc3545;">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            <strong>يرجى تصحيح الأخطاء التالية:</strong>
                                        </div>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">الاسم رباعي</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-person text-primary"></i></span>
                                            <input type="text" name="full_name" class="form-control"
                                                placeholder="أدخل الاسم رباعي" value="{{ old('full_name') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">رقم الهوية</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-card-heading text-primary"></i></span>
                                            <input type="text" name="id_number" class="form-control"
                                                placeholder="أدخل رقم الهوية" maxlength="9"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                value="{{ old('id_number') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">تاريخ الميلاد</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-calendar3 text-primary"></i></span>
                                            <input type="date" name="date_of_birth" class="form-control"
                                                value="{{ old('date_of_birth') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">رقم الهاتف</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-telephone text-primary"></i></span>
                                            <input type="tel" name="phone_number" class="form-control"
                                                placeholder="05XXXXXXXX" maxlength="10"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                value="{{ old('phone_number') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">العنوان</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-geo-alt text-primary"></i></span>
                                            <input type="text" name="address" class="form-control"
                                                placeholder="المدينة، الحي" value="{{ old('address') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">حالة السكن</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-house-door text-primary"></i></span>
                                            <select name="is_displaced" class="form-select" required>
                                                <option value="" selected disabled>اختر الحالة...</option>
                                                <option value="0" {{ old('is_displaced') == '0' ? 'selected' : '' }}>
                                                    مقيم</option>
                                                <option value="1" {{ old('is_displaced') == '1' ? 'selected' : '' }}>
                                                    نازح</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer bg-white border-0 mt-4 p-0">
                                    <div class="d-flex justify-content-start gap-2">
                                        <button type="submit" class="btn btn-success px-5 fw-bold"
                                            style="background-color: #28a745; border:none;">
                                            <i class="bi bi-check-circle me-1"></i> حفظ البيانات
                                        </button>
                                        <a href="{{ route('student.index') }}" class="btn btn-light px-4 border">إلغاء</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#createStudentForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let submitBtn = form.find('button[type="submit"]');
                let formData = form.serialize();

                // تعطيل الزر وإظهار حالة التحميل
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تمت العملية',
                                text: response.message,
                                confirmButtonText: 'حسناً'
                            }).then(() => {
                                window.location.href =
                                    "{{ route('student.index') }}"; // التوجه لصفحة العرض
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(
                            '<i class="bi bi-check-circle me-1"></i> حفظ البيانات');

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorHtml = '<ul class="text-start">';
                            $.each(errors, function(key, value) {
                                errorHtml += '<li>' + value[0] + '</li>';
                            });
                            errorHtml += '</ul>';

                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ في البيانات',
                                html: errorHtml,
                            });
                        } else {
                            Swal.fire('خطأ!', 'حدث خطأ غير متوقع، يرجى المحاولة لاحقاً',
                                'error');
                        }
                    }
                });
            });
        });
    </script>
@endpush
