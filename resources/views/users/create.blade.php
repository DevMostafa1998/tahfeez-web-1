@extends('layouts.app')

@section('title', 'إضافة مستخدم جديد')

@section('content')
    {{-- ... (الجزء العلوي من الصفحة يبقى كما هو) ... --}}

    <div class="card-body p-4">
        <form action="{{ route('user.store') }}" method="POST">
            @if ($errors->any())
                {{-- ... (قسم عرض الأخطاء يبقى كما هو) ... --}}
            @endif
            @csrf
            <div class="row g-4">
                {{-- السطر الأول --}}
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">الاسم رباعي</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person text-primary"></i></span>
                        <input type="text" name="full_name" class="form-control" placeholder="أدخل الاسم رباعي"
                            value="{{ old('full_name') }}" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">كلمة المرور</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock text-primary"></i></span>
                        <input type="password" name="password" id="password" class="form-control"
                            placeholder="أدخل كلمة المرور" required>
                        <button class="btn btn-outline-secondary" type="button"
                            onclick="togglePassword('password', 'eyeIcon1')">
                            <i class="bi bi-eye" id="eyeIcon1"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">تأكيد كلمة المرور</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-shield-lock text-primary"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                            placeholder="أعد إدخال كلمة المرور" required>
                        <button class="btn btn-outline-secondary" type="button"
                            onclick="togglePassword('password_confirmation', 'eyeIcon2')">
                            <i class="bi bi-eye" id="eyeIcon2"></i>
                        </button>
                    </div>
                </div>

                {{-- السطر الثاني --}}
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">رقم الهوية</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-card-heading text-primary"></i></span>
                        <input type="text" name="id_number" class="form-control" placeholder="أدخل رقم الهوية"
                            inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            value="{{ old('id_number') }}" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">تاريخ الميلاد</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-calendar3 text-primary"></i></span>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}"
                            required>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">مكان الميلاد</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-geo text-primary"></i></span>
                        <input type="text" name="birth_place" class="form-control" placeholder="أدخل مدينة الميلاد"
                            value="{{ old('birth_place') }}">
                    </div>
                </div>

                {{-- السطر الثالث --}}
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">رقم الهاتف</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-telephone text-primary"></i></span>
                        <input type="tel" name="phone_number" class="form-control" placeholder="05XXXXXXXX"
                            inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            value="{{ old('phone_number') }}" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">رقم الواتساب</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-whatsapp text-success"></i></span>
                        <input type="tel" name="whatsapp_number" class="form-control" placeholder="05XXXXXXXX"
                            value="{{ old('whatsapp_number') }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">رقم المحفظة</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-wallet2 text-primary"></i></span>
                        <input type="text" name="wallet_number" class="form-control"
                            placeholder="أدخل رقم المحفظة الإلكترونية" value="{{ old('wallet_number') }}">
                    </div>
                </div>

                {{-- السطر الرابع --}}
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">المؤهل العلمي</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-mortarboard text-primary"></i></span>
                        <input type="text" name="qualification" class="form-control" placeholder="مثلاً: بكالوريوس"
                            value="{{ old('qualification') }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">التخصص</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-book text-primary"></i></span>
                        <input type="text" name="specialization" class="form-control"
                            placeholder="أدخل التخصص الجامعي" value="{{ old('specialization') }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">عدد الأجزاء المحفوظة</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-journal-bookmark text-primary"></i></span>
                        <input type="number" name="parts_memorized" class="form-control" placeholder="0"
                            min="0" max="30" value="{{ old('parts_memorized', 0) }}">
                    </div>
                </div>

                {{-- السطر الخامس --}}
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">العنوان</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-geo-alt text-primary"></i></span>
                        <input type="text" name="address" class="form-control" placeholder="المدينة، الحي"
                            value="{{ old('address') }}" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">حالة السكن</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-house-door text-primary"></i></span>
                        <select name="is_displaced" class="form-select" required>
                            <option value="0" {{ old('is_displaced') == '0' ? 'selected' : '' }}>مقيم</option>
                            <option value="1" {{ old('is_displaced') == '1' ? 'selected' : '' }}>نازح</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">اسم المسجد</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-mosque text-primary"></i></span>
                        <input type="text" name="mosque_name" class="form-control" placeholder="اسم المسجد التابع له"
                            value="{{ old('mosque_name') }}">
                    </div>
                </div>

                {{-- السطر السادس (التصنيفات) --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">تصنيف المستخدم</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-layers text-primary"></i></span>
                        <select name="is_admin" class="form-select" required>
                            <option value="" selected disabled>اختر التصنيف...</option>
                            <option value="محفظ" {{ old('is_admin') == 'محفظ' ? 'selected' : '' }}>محفظ</option>
                            <option value="مسؤول" {{ old('is_admin') == 'مسؤول' ? 'selected' : '' }}>مسؤول</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">نوع التصنيف</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-tags text-primary"></i></span>
                        <select name="category_id" class="form-select" required>
                            <option value="" selected disabled>اختر النوع ...</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
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
