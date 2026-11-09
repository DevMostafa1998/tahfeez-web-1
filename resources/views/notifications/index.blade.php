@extends('layouts.app')

@section('content')
    <div class="container p-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow border-0" style="border-radius: 15px;">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3" style="border-radius: 15px 15px 0 0;">
                        <h5 class="mb-0"><i class="bi bi-send-fill me-2"></i> إرسال تنبيه جديد</h5>

                        {{-- إظهار اسم المركز المحدد --}}
                        @if(isset($currentCenter) && $currentCenter)
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                <i class="bi bi-building me-1"></i> المركز المستهدف: {{ $currentCenter->name }}
                            </span>
                        @else
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                <i class="bi bi-globe me-1"></i> النطاق: جميع المراكز
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('notifications.send') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-bold d-block">إرسال إلى:</label>
                                <div class="btn-group w-100" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" name="target" id="target_mobile" value="mobile" checked>
                                    <label class="btn btn-outline-primary" for="target_mobile">
                                        <i class="bi bi-phone me-1"></i> تطبيق الجوال
                                    </label>

                                    <input type="radio" class="btn-check" name="target" id="target_web" value="web">
                                    <label class="btn btn-outline-primary" for="target_web">
                                        <i class="bi bi-browser-chrome me-1"></i> موقع الويب
                                    </label>

                                    <input type="radio" class="btn-check" name="target" id="target_all" value="all">
                                    <label class="btn btn-outline-primary" for="target_all">
                                        <i class="bi bi-megaphone me-1"></i> الكل معاً
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">عنوان التنبيه</label>
                                <input type="text" name="title" class="form-control rounded-pill"
                                    placeholder="مثلاً: تنبيه إداري للمحفظين" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">محتوى التنبيه</label>
                                <textarea name="body" class="form-control"
                                    style="border-radius: 15px; min-height: 120px; resize: none; overflow: hidden;"
                                    placeholder="اكتب نص التنبيه هنا..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">رابط التوجيه (اختياري)</label>
                                <input type="url" name="click_url" class="form-control" style="border-radius: 15px;"
                                    placeholder="https://ahlalrahman.com/public/apps/tahfeez-app.apk">
                                <small class="text-muted">إذا تركته فارغاً، سيفتح التطبيق بشكل افتراضي.</small>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow">
                                    <i class="bi bi-send-check-fill ms-1"></i> إرسال التنبيه الآن
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="mt-3 text-center text-muted">
                    <small><i class="bi bi-info-circle me-1"></i> سيصل التنبيه فقط للفيئات المرتبطة بالمركز المحدد أعلاه.</small>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.querySelector('textarea[name="body"]');
            if (textarea) {
                textarea.addEventListener('input', autoResize, false);
                function autoResize() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                }
                autoResize.call(textarea);
            }
        });
    </script>
@endsection
