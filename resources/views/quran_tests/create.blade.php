@extends('layouts.app')

@push('styles')
    <style>
        /* تحسينات بصرية إضافية للحصول على مظهر لوحة تحكم عصرية */
        .custom-search-select .dropdown-toggle::after {
            float: left;
            margin-top: 10px;
        }

        .custom-search-select .dropdown-menu {
            border: 1px solid rgba(0, 0, 0, .08) !important;
            border-radius: 12px;
        }

        .input-group-text {
            border-color: rgba(0, 0, 0, 0.08) !important;
        }

        .form-control,
        .form-select {
            border-color: rgba(0, 0, 0, 0.08) !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15) !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%) !important;
        }

        .fs-7 {
            font-size: 0.85rem;
        }
    </style>
@endpush

@section('content')
    <div class="container mt-5 mb-5" dir="rtl">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

            <div class="card-body p-4 bg-light-subtle">
                <form action="{{ route('quran_tests.store') }}" method="POST" id="createTestForm">
                    @csrf
                    <div class="row g-4">

                        {{-- القسم الأول: البيانات الأساسية للطلب --}}
                        <div class="col-12">
                            <div class="p-3 bg-white border border-light-subtle rounded-3 shadow-sm">
                                <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2 fs-7 text-uppercase tracking-wider">
                                    <i class="bi bi-person-lines-fill me-1 text-primary"></i> البيانات الأساسية
                                </h6>
                                <div class="row g-3">
                                    {{-- اختيار الطالب --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small mb-1">اختر الطالب <span class="text-danger">*</span></label>
                                        <div class="input-group shadow-sm rounded-3 custom-search-select dropdown">
                                            <span class="input-group-text bg-white text-primary border-end-0"><i class="bi bi-person-fill"></i></span>
                                            <button class="form-select border-start-0 text-start bg-white fw-bold"
                                                type="button" id="studentDropdownBtn" data-bs-toggle="dropdown"
                                                data-bs-display="static" aria-expanded="false">
                                                اختر من القائمة...
                                            </button>
                                            <div class="dropdown-menu w-100 shadow dropdown-menu-start" aria-labelledby="studentDropdownBtn">
                                                <div class="p-2">
                                                    <input type="text" id="studentSearchInput" class="form-control text-right" placeholder="ابحث عن اسم الطالب..." dir="rtl" autocomplete="off">
                                                </div>
                                                <ul class="list-unstyled mb-0" id="studentsList" style="max-height: 200px; overflow-y: auto; padding-right: 0;">
                                                    @isset($students)
                                                        @foreach ($students as $student)
                                                            <li>
                                                                <button type="button" class="dropdown-item text-start student-option"
                                                                    data-id="{{ $student->id }}"
                                                                    data-name="{{ $student->full_name }}"
                                                                    data-category="{{ $student->categorie->name ?? 'عام' }}"
                                                                    data-teacher-id="{{ $student->user_id ?? '' }}">
                                                                    {{ $student->full_name }}
                                                                </button>
                                                            </li>
                                                        @endforeach
                                                    @endisset
                                                </ul>
                                            </div>
                                        </div>
                                        <input type="hidden" name="studentId" id="selectedStudentId" required>
                                        @error('studentId')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- تصنيف الطالب --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small mb-1">تصنيف الطالب</label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-tags-fill"></i></span>
                                            <input type="text" id="studentCategoryLabel" class="form-control border-start-0 bg-light fw-bold" readonly placeholder="يظهر تلقائياً عند اختيار الطالب">
                                        </div>
                                    </div>

                                    {{-- الشيخ المحفظ --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small mb-1">الشيخ المحفظ</label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-person-badge"></i></span>
                                            <input type="text" name="teacher_name" id="teacherInput" class="form-control border-start-0 bg-light fw-bold" readonly placeholder="يظهر تلقائياً عند اختيار الطالب" required>
                                        </div>
                                    </div>

                                    {{-- تاريخ الاختبار --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small mb-1">تاريخ الاختبار <span class="text-danger">*</span></label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-white text-primary border-end-0"><i class="bi bi-calendar-event"></i></span>
                                            <input type="date" name="date" class="form-control border-start-0" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- القسم الثاني: تفاصيل ومحتوى الاختبار والأجزاء --}}
                        <div class="col-12">
                            <div class="p-3 bg-white border border-light-subtle rounded-3 shadow-sm">
                                <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2 fs-7 text-uppercase tracking-wider">
                                    <i class="bi bi-journal-bookmark-fill me-1 text-success"></i> تفاصيل ومحتوى الاختبار
                                </h6>
                                <div class="row g-3">
                                    {{-- نوع الاختبار --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small mb-1">نوع الاختبار</label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-white text-success border-end-0"><i class="bi bi-journal-check"></i></span>
                                            <select name="examType" class="form-select border-start-0">
                                                <option value="سرد">سرد</option>
                                                <option value="اجزاء مجتمعه">أجزاء مجتمعة</option>
                                            </select>
                                        </div>
                                    </div>

                                       {{-- الشيخ المختبر  --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small mb-1">الشيخ المختبر <span class="text-danger">*</span></label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-white text-primary border-end-0"><i class="bi bi-person-video3"></i></span>
                                            <input type="text" name="tester_name" id="testerInput" class="form-control border-start-0"
                                                placeholder="اكتب اسم الشيخ المختبر..."
                                                value="" required>
                                        </div>
                                        @error('tester_name')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- من جزء --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark small mb-1">من الجزء <span class="text-danger">*</span></label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-white text-dark border-end-0 fw-bold"><i class="bi bi-arrow-right-short text-primary"></i></span>
                                            <select name="from_juz" id="fromJuz" class="form-select border-start-0" required>
                                                <option value="">اختر...</option>
                                                @for ($i = 1; $i <= 30; $i++)
                                                    <option value="{{ $i }}">الجزء {{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    {{-- إلى جزء --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark small mb-1">إلى الجزء <span class="text-danger">*</span></label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-white text-dark border-end-0 fw-bold"><i class="bi bi-arrow-left-short text-primary"></i></span>
                                            <select name="to_juz" id="toJuz" class="form-select border-start-0" required>
                                                <option value="">اختر...</option>
                                                @for ($i = 1; $i <= 30; $i++)
                                                    <option value="{{ $i }}">الجزء {{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    {{-- عدد الأجزاء المختبرة --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-muted small mb-1">عدد الأجزاء المختبرة</label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-hash"></i></span>
                                            <input type="number" name="juz_count" id="juzCount" class="form-control border-start-0 bg-light text-center fw-bold" readonly placeholder="يحسب تلقائياً" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- القسم الثالث: النتيجة والتقييم النهائي --}}
                        <div class="col-12">
                            <div class="p-3 bg-white border border-light-subtle rounded-3 shadow-sm">
                                <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2 fs-7 text-uppercase tracking-wider">
                                    <i class="bi bi-trophy-fill me-1 text-warning"></i> النتيجة والتقييم النهائي
                                </h6>
                                <div class="row g-3">
                                    {{-- النسبة المئوية --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-success small mb-1">النسبة المئوية (%) <span class="text-danger">*</span></label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-white text-success border-end-0 fw-bold">%</span>
                                            <input type="number" name="score_percentage" id="scorePercentage" class="form-control border-start-0 text-center fw-bold" min="0" max="100" placeholder="مثال: 95" required>
                                        </div>
                                    </div>

                                    {{-- التقدير اللفظي --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-muted small mb-1">التقدير</label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-award-fill"></i></span>
                                            <input type="text" id="gradeLabel" class="form-control border-start-0 bg-light text-center fw-bold text-info" readonly placeholder="التقدير اللفظي">
                                        </div>
                                    </div>

                                    {{-- الحالة الإجمالية --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-muted small mb-1">الحالة الإجمالية</label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-shield-check"></i></span>
                                            <input type="text" id="resultStatusLabel" class="form-control border-start-0 text-center fw-bold bg-light" readonly placeholder="حالة الطالب">
                                        </div>
                                        <input type="hidden" name="result_status" id="resultStatusHidden">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ملاحظات إضافية --}}
                        <div class="col-12">
                            <div class="p-3 bg-white border border-light-subtle rounded-3 shadow-sm">
                                <label class="form-label fw-bold text-dark small mb-1">ملاحظات إضافية</label>
                                <div class="input-group shadow-sm rounded-3">
                                    <span class="input-group-text bg-white text-secondary border-end-0 align-items-start pt-2"><i class="bi bi-chat-right-text"></i></span>
                                    <textarea name="note" class="form-control border-start-0" rows="3" placeholder="اكتب أي ملاحظات عن أداء الطالب هنا..."></textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- أزرار التحكم والعمليات --}}
                    <div class="mt-4 d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('quran_tests.index') }}" class="btn btn-link text-secondary text-decoration-none fw-bold px-3">إلغاء</a>
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow rounded-3 d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill"></i> حفظ البيانات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('studentSearchInput');
            const studentOptions = document.querySelectorAll('.student-option');
            const dropdownBtn = document.getElementById('studentDropdownBtn');
            const hiddenInput = document.getElementById('selectedStudentId');
            const studentCategoryLabel = document.getElementById('studentCategoryLabel');
            const testForm = document.getElementById('createTestForm');

            const fromJuz = document.getElementById('fromJuz');
            const toJuz = document.getElementById('toJuz');
            const juzCount = document.getElementById('juzCount');

            const scorePercentage = document.getElementById('scorePercentage');
            const gradeLabel = document.getElementById('gradeLabel');
            const resultStatusLabel = document.getElementById('resultStatusLabel');
            const resultStatusHidden = document.getElementById('resultStatusHidden');

            const dbSettings = @json($settings);
            const settings = [];

            Object.values(dbSettings).forEach(item => {
                settings.push({
                    name: item.grade_name,
                    min: parseInt(item.min_score),
                    max: parseInt(item.max_score),
                    status: item.status,
                    textClass: item.text_class
                });
            });

            searchInput.addEventListener('input', function() {
                const filter = searchInput.value.toLowerCase();
                studentOptions.forEach(option => {
                    const name = option.getAttribute('data-name').toLowerCase();
                    if (name.includes(filter)) {
                        option.parentElement.style.display = "";
                    } else {
                        option.parentElement.style.display = "none";
                    }
                });
            });

            studentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const studentId = this.getAttribute('data-id');
                    const studentName = this.getAttribute('data-name');
                    const studentCat = this.getAttribute('data-category');

                    // تحديث الحقول الأساسية
                    hiddenInput.value = studentId;
                    dropdownBtn.innerText = studentName;
                    studentCategoryLabel.value = studentCat ?? '';

                    // جلب معلم الطالب عبر AJAX
                    fetch(`{{ route('quran_tests.student_teacher') }}?student_id=${studentId}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            const teacherInput = document.getElementById('teacherInput');

                            // وضع اسم المعلم في الحقل المخصص للشيخ المحفظ (قراءة فقط)
                            if (data.teacher_name) {
                                teacherInput.value = data.teacher_name;
                            } else {
                                teacherInput.value = 'لا يوجد محفظ';
                            }
                        })
                        .catch(err => console.error('خطأ في جلب معلم الطالب:', err));
                });
            });

            dropdownBtn.parentElement.addEventListener('hidden.bs.dropdown', function() {
                searchInput.value = '';
                studentOptions.forEach(option => option.parentElement.style.display = "");
            });

            function calculateJuzCount() {
                const from = parseInt(fromJuz.value);
                const to = parseInt(toJuz.value);
                if (!isNaN(from) && !isNaN(to)) {
                    juzCount.value = Math.abs(to - from) + 1;
                } else {
                    juzCount.value = '';
                }
            }
            fromJuz.addEventListener('change', calculateJuzCount);
            toJuz.addEventListener('change', calculateJuzCount);

            scorePercentage.addEventListener('input', function() {
                let score = parseInt(this.value);

                if (score > 100) {
                    this.value = 100;
                    score = 100;
                }
                if (score < 0) {
                    this.value = 0;
                    score = 0;
                }

                if (!isNaN(score)) {
                    let matched = false;

                    for (let i = 0; i < settings.length; i++) {
                        let range = settings[i];
                        if (score >= range.min && score <= range.max) {
                            gradeLabel.value = range.name;
                            gradeLabel.className = `form-control border-start-0 text-center fw-bold bg-light ${range.textClass}`;

                            resultStatusLabel.value = range.status;
                            resultStatusHidden.value = range.status;
                            resultStatusLabel.className = `form-control border-start-0 text-center fw-bold bg-light ${range.status === 'ناجح' ? 'text-success' : 'text-danger'}`;

                            matched = true;
                            break;
                        }
                    }

                    if (!matched) {
                        clearLabels();
                    }
                } else {
                    clearLabels();
                }
            });

            function clearLabels() {
                gradeLabel.value = '';
                gradeLabel.className = "form-control border-start-0 text-center fw-bold bg-light text-info";
                resultStatusLabel.value = '';
                resultStatusHidden.value = '';
                resultStatusLabel.className = "form-control border-start-0 text-center fw-bold bg-light";
            }

            testForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!fromJuz.value || !toJuz.value) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'الرجاء اختيار النطاق الصحيح للأجزاء المختبرة.',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                }

                const formData = new FormData(this);
                const actionUrl = this.getAttribute('action');

                Swal.fire({
                    title: 'جاري حفظ الاختبار...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(actionUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحفظ بنجاح!',
                                text: data.message,
                                confirmButtonText: 'حسناً',
                                timer: 2500,
                                timerProgressBar: true
                            });

                            testForm.reset();
                            dropdownBtn.innerText = 'اختر من القائمة...';
                            hiddenInput.value = '';
                            studentCategoryLabel.value = '';
                            clearLabels();

                        } else {
                            throw new Error(data.message || 'بيانات غير صحيحة');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ أثناء الحفظ',
                            text: error.message || 'تأكد من تعبئة كافة الحقول بشكل صحيح والاتصال بالشبكة.',
                            confirmButtonText: 'إغلاق'
                        });
                    });
            });
        });
    </script>
@endpush
