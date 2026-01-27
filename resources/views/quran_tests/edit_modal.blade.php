{{-- resources/views/quran_tests/_edit_modal.blade.php --}}
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
                        {{-- اسم الطالب --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary">اسم الطالب</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-primary-subtle">
                                    <i class="bi bi-person-fill"></i>
                                </span>
                                {{-- حقل نصي للعرض فقط --}}
                                <input type="text" id="edit_student_name_display"
                                    class="form-control border-primary-subtle bg-light" readonly>

                                {{-- حقل مخفي لإرسال معرف الطالب (studentId) الفعلي للكنترولر --}}
                                <input type="hidden" name="studentId" id="edit_studentId">
                            </div>
                        </div>

                        {{-- تاريخ الاختبار --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary">تاريخ الاختبار</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary border-primary-subtle"><i
                                        class="bi bi-calendar-event"></i></span>
                                <input type="date" name="date" id="edit_date"
                                    class="form-control border-primary-subtle" required>
                            </div>
                        </div>

                        {{-- عدد الأجزاء --}}
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

                        {{-- نوع الاختبار --}}
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

                        {{-- النتيجة --}}
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

                        {{-- ملاحظات --}}
                        <div class="col-12 mt-3">
                            <label class="form-label fw-bold text-secondary">ملاحظات المعلم</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary border-primary-subtle"><i
                                        class="bi bi-chat-dots"></i></span>
                                <textarea name="note" id="edit_note" class="form-control border-primary-subtle" autocomplete="off" rows="3"
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
