@extends('layouts.app') {{-- استدعاء القالب الأساسي الذي يحتوي على الهيدر والسايدبار --}}

@section('title', 'إضافة مستخدم جديد')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold">إضافة مستخدم جديد</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
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
                    <div class="card card-outline card-primary shadow-sm"
                        style="border-radius: 15px; border-top: 4px solid #007bff;">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title fw-bold text-secondary mb-0">بيانات المستخدم الأساسية</h5>
                        </div>

                        <div class="card-body p-4">
                            <form action="{{ route('user.store') }}" method="POST">
                                @if ($errors->any())
                                    <div class="alert alert-danger shadow-sm mb-4" style="border-radius: 12px; border-right: 5px solid #dc3545;">
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
                                @csrf
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">الاسم رباعي</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-person text-primary"></i></span>
                                            <input type="text" name="full_name" class="form-control"
                                                placeholder="أدخل الاسم رباعي" required>
                                        </div>
                                    </div>
                                <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">كلمة المرور</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-lock text-primary"></i></span>
                                            <input type="password" name="password" id="password" class="form-control" placeholder="أدخل كلمة المرور" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'eyeIcon1')">
                                                <i class="bi bi-eye" id="eyeIcon1"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">تأكيد كلمة المرور</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-shield-lock text-primary"></i></span>
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="أعد إدخال كلمة المرور" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', 'eyeIcon2')">
                                                <i class="bi bi-eye" id="eyeIcon2"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">رقم الهوية</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-card-heading text-primary"></i></span>
                                            <input type="text" name="id_number" class="form-control"
                                                placeholder="أدخل رقم الهوية" inputmode="numeric"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">تاريخ الميلاد</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-calendar3 text-primary"></i></span>
                                            <input type="date" name="date_of_birth" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">رقم الهاتف</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-telephone text-primary"></i></span>
                                            <input type="tel" name="phone_number" class="form-control"
                                                placeholder="05XXXXXXXX" inputmode="numeric"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">العنوان</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-geo-alt text-primary"></i></span>
                                            <input type="text" name="address" class="form-control"
                                                placeholder="المدينة، الحي" required>
                                        </div>
                                    </div>


                                            <div class="col-md-4">
                                                <label class="form-label fw-bold small text-muted">تصنيف المستخدم</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i
                                                            class="bi bi-layers text-primary"></i></span>
                                                    <select name="is_admin" class="form-select" required>
                                                        <option value="" selected disabled>اختر التصنيف...</option>
                                                        <option value="محفظ">محفظ</option>
                                                        <option value="مسؤول">مسؤول</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-bold small text-muted">نوع التصنيف</label>
                                                <select name="category_id" class="form-select" required>
                                                    <option value="" selected disabled>اختر التصنيف ...</option>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                    @endforeach
                                            </select>
                                            </div>



                                </div>

                                <div class="card-footer bg-white border-0 mt-4 p-0">
                                    <div class="d-flex justify-content-start gap-2">
                                        <button type="submit" class="btn btn-success px-5 fw-bold"
                                            style="background-color: #28a745; border:none;">
                                            <i class="bi bi-check-circle me-1"></i> حفظ البيانات
                                        </button>
                                        <button type="reset" class="btn btn-light px-4 border">إعادة تعيين</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
function togglePassword(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.getElementById(iconId);

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.classList.remove("bi-eye");
        eyeIcon.classList.add("bi-eye-slash");
    } else {
        passwordInput.type = "password";
        eyeIcon.classList.remove("bi-eye-slash");
        eyeIcon.classList.add("bi-eye");
    }
}
</script>
@endsection
