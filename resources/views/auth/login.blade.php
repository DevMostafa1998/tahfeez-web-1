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

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e5631 0%, #28a745 100%);
            --glass-bg: rgba(255, 255, 255, 0.9);
            --text-dark: #2d3436;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: #f0f2f5;
            background-image:
                radial-gradient(at 0% 0%, rgba(40, 167, 69, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(30, 86, 49, 0.15) 0px, transparent 50%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .card-header-custom {
            background: var(--primary-gradient);
            padding: 40px 20px;
            text-align: center;
            color: white;
            clip-path: ellipse(120% 100% at 50% 0%);
        }

        .logo-wrapper {
            width: 90px;
            height: 90px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border: 4px solid rgba(255, 255, 255, 0.2);
        }

        .logo-wrapper img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1.5px solid #eee;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.1);
            border-color: #28a745;
            background-color: #fff;
        }

        .input-group-text {
            background: none;
            border: none;
            color: #28a745;
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            cursor: pointer;
        }

        .btn-login {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(40, 167, 69, 0.4);
            filter: brightness(1.1);
        }

        .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }

        .animate-float {
            animation: fadeInUp 0.8s ease-out;
        }
    </style>
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
                </form>
            </div>

            <div class="text-center pb-4">
                <p class="text-muted mb-0" style="font-size: 0.7rem;">جميع الحقوق محفوظة &copy; 2026</p>
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
