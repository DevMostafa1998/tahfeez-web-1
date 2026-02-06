<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | نظام التحفيظ الذكي</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">

</head>

<body>
    <div class="login-container animate-float">
        <div class="login-card">
            <div class="card-header-custom">
                <div class="logo-wrapper animate__animated animate__zoomIn">
                    <img src="{{ asset('assets/img/logo.jpeg') }}" alt="الشعار">
                </div>
                <h4 class="fw-800 mb-1">نظام التحفيظ</h4>
                <p class="small opacity-75 mb-0">أهلاً بك مجدداً، سجل دخولك للبدء</p>
            </div>

            <div class="card-body p-4 pt-4">
                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 small animate__animated animate__shakeX">
                        @foreach ($errors->all() as $error)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">رقم الهوية</label>
                        <div class="position-relative">
                            <input type="text" name="id_number" class="form-control"
                                placeholder="أدخل رقم الهوية المكون من 9 أرقام" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="{{ old('id_number') }}"
                                required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">كلمة المرور</label>
                        <div class="position-relative">
                            <input type="password" id="passwordField" name="password" class="form-control"
                                placeholder="••••••••" required>
                            <span class="input-group-text" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label text-muted small" for="remember">تذكرني</label>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-login text-white">
                            دخول آمن <i class="bi bi-box-arrow-in-left ms-2"></i>
                        </button>
                    </div>
                    <div class="text-center my-3 position-relative">
                        <hr class="text-muted">
                    </div>

                    <div class="d-grid">
                        <button type="button" onclick="window.location.href='/parent-login'"
                            class="btn btn-outline-success border-2 fw-bold"
                            style="border-radius: 12px; padding: 10px;">
                            <i class="bi bi-person-heart me-2"></i> المتابعة الأبوية
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('passwordField');
            const toggleIcon = document.getElementById('toggleIcon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
    </script>
</body>

</html>
