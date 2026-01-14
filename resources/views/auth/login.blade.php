<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | نظام التحفيظ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
</head>

<body>
    <div class="container p-2">
        <div class="login-card mx-auto">
            <div class="color-bar">
                <div class="cb-blue"></div>
                <div class="cb-green"></div>
            </div>

            <div class="card-header-custom">
                <div class="logo-wrapper">
                    <img src="{{ asset('assets/img/quran.png') }}" alt="الشعار">
                </div>
                <h5 class="fw-bold mb-0 text-dark">تسجيل الدخول</h5>
                <p class="text-muted small mt-1">مرحباً بك، يرجى تسجيل الدخول للمتابعة</p>
            </div>

            <div class="card-body p-4 pt-2">
                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small mb-1">رقم الهوية</label>
                        <input type="text" name="id_number" class="form-control" placeholder="أدخل رقم الهوية"
                            inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            value="{{ old('id_number') }}" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-secondary small mb-1">كلمة المرور</label>
                        <div class="password-wrapper" style="position: relative;">
                            <input type="password" id="passwordField" name="password" class="form-control"
                                placeholder="••••••••" required>
                            </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 extra-links">
                        <div class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label text-muted" for="remember">تذكرني</label>
                        </div>
                        <a href="#" class="text-decoration-none fw-bold" style="color: #007bff;">نسيت السر؟</a>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-login text-white" style="background-color: #28a745;">
                            تسجيل الدخول
                        </button>
                    </div>
                </form>
            </div>

            <div class="card-footer bg-white border-0 text-center pb-3 pt-0">
                <p class="text-muted" style="font-size: 0.75rem;">جميع الحقوق محفوظة &copy; 2026</p>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/login.js') }}"></script>
</body>
</html>
