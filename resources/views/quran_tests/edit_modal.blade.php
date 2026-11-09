<div class="modal fade" id="editTestModal" tabindex="-1" aria-labelledby="editTestModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-xl rounded-4 overflow-hidden">

            {{-- رأس المودال بتصميم جذاب --}}
            <div class="modal-header bg-gradient bg-warning text-dark py-3 px-4 border-0 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-white bg-opacity-50 rounded-3 d-flex align-items-center justify-content-center shadow-sm">
                        <i class="bi bi-pencil-square fs-5 text-dark"></i>
                    </div>
                    <h5 class="modal-title fw-extrabold m-0 text-dark fs-5" id="editTestModalLabel">تعديل بيانات الاختبار</h5>
                </div>
                <button type="button" class="btn-close btn-close-dark m-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editTestForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-light-subtle text-start">
                    <div class="row g-4">

                        {{-- القسم الأول: بيانات الطالب والمعلم --}}
                        <div class="col-12">
                            <div class="p-3 bg-white border border-light-subtle rounded-3 shadow-sm">
                                <h6 class="text-secondary fw-bold mb-3 border-bottom pb-2 fs-7 text-uppercase tracking-wider">
                                    <i class="bi bi-person-lines-fill me-1 text-primary"></i> البيانات الأساسية
                                </h6>
                                <div class="row g-3">
                                    {{-- اسم الطالب --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small mb-1">اسم الطالب</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-secondary border-0 rounded-start-3"><i class="bi bi-person-fill"></i></span>
                                            <input type="text" id="edit_student_name_display" class="form-control border-0 bg-light fw-bold rounded-end-3" readonly>
                                            <input type="hidden" name="studentId" id="edit_studentId">
                                        </div>
                                    </div>

                                    {{-- تصنيف الطالب --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small mb-1">تصنيف الطالب</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-secondary border-0 rounded-start-3"><i class="bi bi-tags-fill"></i></span>
                                            <input type="text" id="edit_student_category" class="form-control border-0 bg-light rounded-end-3" readonly>
                                        </div>
                                    </div>
                                    {{-- الشيخ المحفظ --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small mb-1">الشيخ المحفظ</label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-person-badge"></i></span>
                                            <input type="text" name="teacher_name" id="edit_teacher_name" class="form-control border-start-0 bg-light" readonly required>
                                        </div>
                                    </div>

                                    {{-- تاريخ الاختبار --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small mb-1">تاريخ الاختبار <span class="text-danger">*</span></label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-white text-primary border-end-0"><i class="bi bi-calendar-event"></i></span>
                                            <input type="date" name="date" id="edit_date" class="form-control border-start-0" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- القسم الثاني: تفاصيل الاختبار والأجزاء --}}
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
                                            <select name="examType" id="edit_examType" class="form-select border-start-0">
                                                <option value="سرد">سرد</option>
                                                <option value="اجزاء مجتمعه">أجزاء مجتمعة</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- الشيخ المختبر) --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small mb-1">الشيخ المختبر <span class="text-danger">*</span></label>
                                        <div class="input-group shadow-sm rounded-3">
                                            <span class="input-group-text bg-white text-primary border-end-0"><i class="bi bi-person-video3"></i></span>
                                            <input type="text" name="tester_name" id="edit_tester_name" class="form-control border-start-0"
                                                placeholder="اكتب اسم الشيخ المختبر هنا..." required>
                                        </div>
                                    </div>

                                    {{-- من جزء --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark small mb-1">من الجزء <span class="text-danger">*</span></label>
                                        <select name="from_juz" id="edit_from_juz" class="form-select shadow-sm rounded-3" required>
                                            <option value="">اختر...</option>
                                            @for ($i = 1; $i <= 30; $i++)
                                                <option value="{{ $i }}">الجزء {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    {{-- إلى جزء --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark small mb-1">إلى الجزء <span class="text-danger">*</span></label>
                                        <select name="to_juz" id="edit_to_juz" class="form-select shadow-sm rounded-3" required>
                                            <option value="">اختر...</option>
                                            @for ($i = 1; $i <= 30; $i++)
                                                <option value="{{ $i }}">الجزء {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    {{-- عدد الأجزاء --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-muted small mb-1">عدد الأجزاء</label>
                                        <input type="number" name="juz_count" id="edit_juz_count" class="form-control border-0 bg-light text-center fw-bold rounded-3" readonly required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- القسم الثالث: النتيجة والتقييم --}}
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
                                            <input type="number" name="score_percentage" id="edit_score_percentage" class="form-control border-start-0 text-center fw-bold" min="0" max="100" required>
                                        </div>
                                    </div>

                                    {{-- التقدير اللفظي --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-muted small mb-1">التقدير</label>
                                        <input type="text" id="edit_grade_label" class="form-control border-0 bg-light text-center fw-bold rounded-3 text-info" readonly>
                                    </div>

                                    {{-- الحالة --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-muted small mb-1">الحالة الإجمالية</label>
                                        <input type="text" id="edit_result_status_label" class="form-control border-0 bg-light text-center fw-bold rounded-3" readonly>
                                        <input type="hidden" name="result_status" id="edit_result_status_hidden">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ملاحظات --}}
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small mb-1">ملاحظات إضافية</label>
                            <textarea name="note" id="edit_note" class="form-control shadow-sm rounded-3" rows="3" placeholder="اكتب أي ملاحظات أو توجيهات للطالب هنا..."></textarea>
                        </div>

                    </div>
                </div>

                {{-- أزرار التحكم السفلى --}}
                <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-end gap-2 border-0">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none fw-bold px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning px-4 py-2 fw-bold shadow rounded-3 d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
