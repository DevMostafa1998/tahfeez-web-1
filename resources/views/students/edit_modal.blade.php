<div class="modal fade" id="editStudentModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;" dir="rtl">

            <div
                class="modal-header bg-warning text-dark border-0 py-3 d-flex flex-row-reverse justify-content-between align-items-center">
                <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title fw-bold m-0">
                    <i class="bi bi-person-gear me-2"></i>تعديل بيانات الطالب
                </h5>
            </div>

            <form action="{{ route('student.update', $student->id) }}" method="POST"
                id="editStudentForm{{ $student->id }}"> @csrf
                @method('PUT')

                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- الاسم رباعي --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small d-block">
                                <i class="bi bi-person-fill text-warning me-2"></i>الاسم رباعي
                            </label>
                            <input type="text" name="full_name" class="form-control bg-light border-0"
                                value="{{ $student->full_name }}" required>
                        </div>

                        {{-- رقم الهوية --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small d-block">
                                <i class="bi bi-card-heading text-warning me-2"></i>رقم الهوية
                            </label>
                            <input type="text" name="id_number" class="form-control bg-light border-0"
                                value="{{ $student->id_number }}" required>
                        </div>

                        {{-- تاريخ الميلاد --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small d-block">
                                <i class="bi bi-calendar-event text-warning me-2"></i>تاريخ الميلاد
                            </label>
                            <input type="date" name="date_of_birth" class="form-control bg-light border-0"
                                value="{{ \Carbon\Carbon::parse($student->date_of_birth)->format('Y-m-d') }}" required>
                        </div>

                        {{-- مكان الميلاد --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small d-block">
                                <i class="bi bi-geo text-warning me-2"></i>مكان الميلاد
                            </label>
                            <input type="text" name="birth_place" class="form-control bg-light border-0"
                                value="{{ $student->birth_place }}">
                        </div>

                        {{-- رقم الجوال --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small d-block">
                                <i class="bi bi-telephone-fill text-warning me-2"></i>رقم الجوال
                            </label>
                            <input type="text" name="phone_number" class="form-control bg-light border-0"
                                value="{{ $student->phone_number }}" required>
                        </div>

                        {{-- رقم الواتساب --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small d-block">
                                <i class="bi bi-whatsapp text-warning me-2"></i>رقم الواتساب
                            </label>
                            <input type="text" name="whatsapp_number" maxlength="15"
                                class="form-control bg-light border-0"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                value="{{ $student->whatsapp_number }}">
                        </div>

                        {{-- العنوان --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small d-block">
                                <i class="bi bi-geo-alt-fill text-warning me-2"></i>العنوان
                            </label>
                            <input type="text" name="address" class="form-control bg-light border-0"
                                value="{{ $student->address }}" required>
                        </div>

                        {{-- حالة السكن --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small d-block">
                                <i class="bi bi-house-door-fill text-warning me-2"></i>حالة السكن
                            </label>
                            <select name="is_displaced" class="form-select bg-light border-0">
                                <option value="0" {{ !$student->is_displaced ? 'selected' : '' }}>مقيم</option>
                                <option value="1" {{ $student->is_displaced ? 'selected' : '' }}>نازح</option>
                            </select>
                        </div>

                        {{-- اسم المركز --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small d-block">
                                <i class="bi bi-building text-warning me-2"></i>اسم المركز
                            </label>
                            <input type="text" name="center_name" class="form-control bg-light border-0"
                                value="{{ $student->center_name }}">
                        </div>

                        {{-- اسم المسجد --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small d-block">
                                <i class="bi bi-moon-stars-fill text-warning me-2"></i>اسم المسجد
                            </label>
                            <input type="text" name="mosque_name" class="form-control bg-light border-0"
                                value="{{ $student->mosque_name }}">
                        </div>

                        {{-- عنوان المسجد --}}
                        <div class="col-12 text-start">
                            <label class="form-label fw-bold small d-block">
                                <i class="bi bi-map-fill text-warning me-2"></i>عنوان المسجد
                            </label>
                            <input type="text" name="mosque_address" class="form-control bg-light border-0"
                                value="{{ $student->mosque_address }}">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-3 bg-light d-flex justify-content-end">
                    <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm rounded-pill">
                        <i class="bi bi-check-lg me-2"></i>حفظ التغييرات
                    </button>
                    <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm"
                        data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
