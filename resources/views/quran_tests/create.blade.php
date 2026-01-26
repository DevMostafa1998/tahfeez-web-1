@extends('layouts.app')

@section('content')

    <div class="container mt-5" dir="rtl">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">إضافة اختبار تسميع جديد</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('quran_tests.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3 text-right" dir="rtl">
                            <label class="form-label">اختر الطالب</label>

                            <div class="dropdown custom-search-select">
                                <button class="form-select text-start" type="button" id="studentDropdownBtn"
                                    data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                    اختر من القائمة...
                                </button>

                                <div class="dropdown-menu w-100 shadow dropdown-menu-start"
                                    aria-labelledby="studentDropdownBtn">
                                    <div class="p-2">
                                        <input type="text" id="studentSearchInput" class="form-control text-right"
                                            placeholder="ابحث عن اسم الطالب..." dir="rtl" autocomplete="off">
                                    </div>
                                    <ul class="list-unstyled mb-0" id="studentsList"
                                        style="max-height: 200px; overflow-y: auto; padding-right: 0;">
                                        @isset($students)
                                            @foreach ($students as $student)
                                                <li>
                                                    <button type="button" class="dropdown-item text-start student-option"
                                                        data-id="{{ $student->id }}" data-name="{{ $student->full_name }}">
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ الاختبار</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">عدد الأجزاء المختبرة</label>
                            <input type="number" name="juz_count" class="form-control" min="1" max="30"
                                placeholder="مثلاً: 5" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">نوع الاختبار</label>
                            <select name="examType" class="form-select">
                                <option value="سرد">سرد</option>
                                <option value="اجزاء مجتمعه">أجزاء مجتمعة</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">النتيجة</label>
                            <select name="result_status" class="form-select text-center">
                                <option value="ناجح" class="text-success fw-bold">ناجح</option>
                                <option value="راسب" class="text-danger fw-bold">راسب</option>
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">ملاحظات إضافية</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="اكتب أي ملاحظات عن أداء الطالب هنا..."></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-5">حفظ البيانات</button>
                        <a href="{{ route('quran_tests.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // المعرفات الأساسية
            const searchInput = document.getElementById('studentSearchInput');
            const studentOptions = document.querySelectorAll('.student-option');
            const dropdownBtn = document.getElementById('studentDropdownBtn');
            const hiddenInput = document.getElementById('selectedStudentId');
            const testForm = document.querySelector('form');

            // 1. وظيفة البحث والتصفية
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

            // 2. وظيفة اختيار الطالب من القائمة
            studentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const studentId = this.getAttribute('data-id');
                    const studentName = this.getAttribute('data-name');

                    dropdownBtn.innerText = studentName;
                    hiddenInput.value = studentId;
                });
            });

            // 3. مسح البحث وإعادة العرض عند إغلاق القائمة
            const dropdownParent = dropdownBtn.parentElement;
            dropdownParent.addEventListener('hidden.bs.dropdown', function() {
                searchInput.value = '';
                studentOptions.forEach(option => {
                    option.parentElement.style.display = "";
                });
            });

            // 4. إرسال النموذج باستخدام AJAX مع رسائل SweetAlert2
            testForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const actionUrl = this.getAttribute('action');

                // إظهار مؤشر تحميل (اختياري)
                Swal.fire({
                    title: 'جاري الحفظ...',
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
                            // رسالة نجاح احترافية
                            Swal.fire({
                                icon: 'success',
                                title: 'تم بنجاح!',
                                text: data.message,
                                confirmButtonText: 'حسناً',
                                timer: 3000, // تختفي تلقائياً بعد 3 ثوانٍ
                                timerProgressBar: true
                            });

                            // إعادة ضبط النموذج
                            testForm.reset();
                            dropdownBtn.innerText = 'اختر من القائمة...';
                            hiddenInput.value = '';
                        } else {
                            throw new Error('بيانات غير صحيحة');
                        }
                    })
                    .catch(error => {
                        // رسالة خطأ احترافية
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: 'حدث خطأ أثناء حفظ البيانات، يرجى التأكد من تعبئة جميع الحقول.',
                            confirmButtonText: 'إغلاق'
                        });
                    });
            });
        });
    </script>
@endpush
