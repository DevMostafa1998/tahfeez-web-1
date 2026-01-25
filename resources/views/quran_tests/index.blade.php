@extends('layouts.app') {{-- افترضنا وجود قالب أساسي --}}

@section('content')
    <div class="container mt-5" dir="rtl">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">سجل اختبارات تسميع القرآن</h2>
            <a href="{{ route('quran_tests.create') }}" class="btn btn-success">إضافة اختبار جديد +</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>الطالب</th>
                            <th>التاريخ</th>
                            <th>عدد الأجزاء</th>
                            <th>نوع الاختبار</th>
                            <th>النتيجة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tests as $test)
                            <tr>
                                <td>{{ $test->student->full_name }}</td> {{-- بفرض وجود علاقة في المودل --}}
                                <td>{{ $test->date->format('Y-m-d') }}</td>
                                <td>{{ $test->juz_count }}</td>
                                <td><span class="badge bg-info text-dark">{{ $test->examType }}</span></td>
                                <td>
                                    <span class="badge {{ $test->result_status == 'ناجح' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $test->result_status }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary edit-test-btn"
                                        data-bs-toggle="modal" data-bs-target="#editTestModal" data-id="{{ $test->id }}"
                                        data-student="{{ $test->studentId }}"
                                        data-date="{{ $test->date->format('Y-m-d') }}" data-juz="{{ $test->juz_count }}"
                                        data-type="{{ $test->examType }}" data-status="{{ $test->result_status }}"
                                        data-note="{{ $test->note }}">
                                        تعديل
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editTestModal" tabindex="-1" aria-labelledby="editTestModalLabel" aria-hidden="true"
        dir="rtl">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning bg-gradient text-white">
                    <h5 class="modal-title fw-bold" id="editTestModalLabel">
                        <i class="bi bi-pencil-square me-2"></i> تعديل بيانات الاختبار
                    </h5>
                    <button type="button" class="btn-close btn-close-white ms-0" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form id="editTestForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4 text-start">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">اسم الطالب</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-primary border-primary-subtle"><i
                                            class="bi bi-person-fill"></i></span>
                                    <select name="studentId" id="edit_studentId" class="form-select border-primary-subtle"
                                        required>
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">تاريخ الاختبار</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-primary border-primary-subtle"><i
                                            class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="date" id="edit_date"
                                        class="form-control border-primary-subtle" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary">عدد الأجزاء</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-primary border-primary-subtle"><i
                                            class="bi bi-layers-half"></i></span>
                                    <input type="number" name="juz_count" id="edit_juz_count"
                                        class="form-control border-primary-subtle text-center" min="1" max="30"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary">نوع الاختبار</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-primary border-primary-subtle"><i
                                            class="bi bi-journal-check"></i></span>
                                    <select name="examType" id="edit_examType" class="form-select border-primary-subtle">
                                        <option value="سرد">سرد</option>
                                        <option value="اجزاء مجتمعه">أجزاء مجتمعة</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary">النتيجة النهائية</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-primary border-primary-subtle"><i
                                            class="bi bi-trophy"></i></span>
                                    <select name="result_status" id="edit_result_status"
                                        class="form-select border-primary-subtle fw-bold">
                                        <option value="ناجح" class="text-success">ناجح ✅</option>
                                        <option value="راسب" class="text-danger">راسب ❌</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <label class="form-label fw-bold text-secondary">ملاحظات المعلم</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-primary border-primary-subtle"><i
                                            class="bi bi-chat-dots"></i></span>
                                    <textarea name="note" id="edit_note" class="form-control border-primary-subtle" rows="3"
                                        placeholder="أضف أي تفاصيل إضافية هنا..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm">
                            <i class="bi bi-save me-1"></i> تحديث البيانات
                        </button>
                        <button type="button" class="btn btn-outline-secondary px-4 fw-bold shadow-sm"
                            data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JavaScript لتعبئة البيانات --}}
    <script>
        document.querySelectorAll('.edit-test-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                // تحديث رابط الـ Action الخاص بالفورم
                document.getElementById('editTestForm').action = `/quran_tests/${id}`;

                // تعبئة الحقول
                document.getElementById('edit_studentId').value = this.getAttribute('data-student');
                document.getElementById('edit_date').value = this.getAttribute('data-date');
                document.getElementById('edit_juz_count').value = this.getAttribute('data-juz');
                document.getElementById('edit_examType').value = this.getAttribute('data-type');
                document.getElementById('edit_result_status').value = this.getAttribute('data-status');
                document.getElementById('edit_note').value = this.getAttribute('data-note');
            });
        });
    </script>
@endsection
