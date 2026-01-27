<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form action="{{ route('user.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; text-align: right;" dir="rtl">

                <div
                    class="modal-header bg-warning text-dark border-0 py-3 d-flex flex-row-reverse justify-content-between align-items-center">
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title fw-bold m-0">
                        <i class="bi bi-person-gear me-2"></i>تعديل بيانات المستخدم
                    </h5>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-4">
                        {{-- الاسم رباعي --}}
                        <div class="col-md-4 text-start">
                            <label class="form-label fw-bold small d-block text-muted">
                                <i class="bi bi-person-fill text-warning me-2"></i>الاسم رباعي
                            </label>
                            <input type="text" name="full_name"
                                class="form-control bg-light border-0 shadow-none py-2" value="{{ $user->full_name }}"
                                required>
                        </div>

                        {{-- رقم الهوية --}}
                        <div class="col-md-4 text-start">
                            <label class="form-label fw-bold small d-block text-muted">
                                <i class="bi bi-card-heading text-warning me-2"></i>رقم الهوية
                            </label>
                            <input type="text" name="id_number"
                                class="form-control bg-light border-0 shadow-none py-2" value="{{ $user->id_number }}"
                                required>
                        </div>

                        {{-- تاريخ الميلاد --}}
                        <div class="col-md-4 text-start">
                            <label class="form-label fw-bold small d-block text-muted">
                                <i class="bi bi-calendar-event text-warning me-2"></i>تاريخ الميلاد
                            </label>
                            <input type="date" name="date_of_birth"
                                class="form-control bg-light border-0 shadow-none py-2"
                                value="{{ \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') }}" required>
                        </div>

                        @if ($user->is_admin == 0)
                            {{-- مكان الميلاد --}}
                            <div class="col-md-4 text-start">
                                <label class="form-label fw-bold small d-block text-muted">
                                    <i class="bi bi-geo text-warning me-2"></i>مكان الميلاد
                                </label>
                                <input type="text" name="birth_place"
                                    class="form-control bg-light border-0 shadow-none py-2"
                                    value="{{ $user->birth_place }}">
                            </div>
                        @endif

                        {{-- رقم الجوال --}}
                        <div class="col-md-4 text-start">
                            <label class="form-label fw-bold small d-block text-muted">
                                <i class="bi bi-telephone-fill text-warning me-2"></i>رقم الجوال
                            </label>
                            <input type="tel" name="phone_number"
                                class="form-control bg-light border-0 shadow-none py-2"
                                value="{{ $user->phone_number }}" required>
                        </div>

                        @if ($user->is_admin == 0)
                            {{-- رقم الواتساب --}}
                            <div class="col-md-4 text-start">
                                <label class="form-label fw-bold small d-block text-muted">
                                    <i class="bi bi-whatsapp text-warning me-2"></i>رقم الواتساب
                                </label>
                                <input type="tel" name="whatsapp_number"
                                    class="form-control bg-light border-0 shadow-none py-2"
                                    value="{{ $user->whatsapp_number }}">
                            </div>

                            {{-- المؤهل العلمي --}}
                            <div class="col-md-4 text-start">
                                <label class="form-label fw-bold small d-block text-muted">
                                    <i class="bi bi-mortarboard-fill text-warning me-2"></i>المؤهل العلمي
                                </label>
                                <input type="text" name="qualification"
                                    class="form-control bg-light border-0 shadow-none py-2"
                                    value="{{ $user->qualification }}">
                            </div>

                            {{-- التخصص --}}
                            <div class="col-md-4 text-start">
                                <label class="form-label fw-bold small d-block text-muted">
                                    <i class="bi bi-book-half text-warning me-2"></i>التخصص
                                </label>
                                <input type="text" name="specialization"
                                    class="form-control bg-light border-0 shadow-none py-2"
                                    value="{{ $user->specialization }}">
                            </div>

                            {{-- الأجزاء المحفوظة --}}
                            <div class="col-md-4 text-start">
                                <label class="form-label fw-bold small d-block text-muted">
                                    <i class="bi bi-journal-check text-warning me-2"></i>الأجزاء المحفوظة
                                </label>
                                <input type="number" name="parts_memorized"
                                    class="form-control bg-light border-0 shadow-none py-2"
                                    value="{{ $user->parts_memorized }}">
                            </div>

                            {{-- اسم المسجد --}}
                            <div class="col-md-4 text-start">
                                <label class="form-label fw-bold small d-block text-muted">
                                    <i class="bi bi-moon-stars-fill text-warning me-2"></i>اسم المسجد
                                </label>
                                <input type="text" name="mosque_name"
                                    class="form-control bg-light border-0 shadow-none py-2"
                                    value="{{ $user->mosque_name }}">
                            </div>

                            {{-- رقم المحفظة --}}
                            <div class="col-md-4 text-start">
                                <label class="form-label fw-bold small d-block text-muted">
                                    <i class="bi bi-wallet2 text-warning me-2"></i>رقم المحفظة
                                </label>
                                <input type="text" name="wallet_number"
                                    class="form-control bg-light border-0 shadow-none py-2"
                                    value="{{ $user->wallet_number }}">
                            </div>

                            {{-- حالة السكن --}}
                            <div class="col-md-4 text-start">
                                <label class="form-label fw-bold small d-block text-muted">
                                    <i class="bi bi-house-door-fill text-warning me-2"></i>حالة السكن
                                </label>
                                <select name="is_displaced" class="form-select bg-light border-0 shadow-none py-2">
                                    <option value="0" {{ !$user->is_displaced ? 'selected' : '' }}>مقيم</option>
                                    <option value="1" {{ $user->is_displaced ? 'selected' : '' }}>نازح</option>
                                </select>
                            </div>
                        @endif

                        {{-- العنوان --}}
                        <div class="col-md-4 text-start">
                            <label class="form-label fw-bold small d-block text-muted">
                                <i class="bi bi-geo-alt-fill text-warning me-2"></i>العنوان
                            </label>
                            <input type="text" name="address"
                                class="form-control bg-light border-0 shadow-none py-2" value="{{ $user->address }}">
                        </div>

                        {{-- الصلاحية --}}
                        <div class="col-md-4 text-start">
                            <label class="form-label fw-bold small d-block text-muted">
                                <i class="bi bi-shield-lock-fill text-warning me-2"></i>الصلاحية
                            </label>
                            <select name="is_admin" class="form-select bg-light border-0 shadow-none py-2">
                                <option value="1" {{ $user->is_admin ? 'selected' : '' }}>مسؤول</option>
                                <option value="0" {{ !$user->is_admin ? 'selected' : '' }}>محفظ</option>
                            </select>
                        </div>

                        {{-- نوع التصنيف --}}
                        <div class="col-md-4 text-start">
                            <label class="form-label fw-bold small d-block text-muted">
                                <i class="bi bi-tags-fill text-warning me-2"></i>نوع التصنيف
                            </label>
                            <select name="category_id" class="form-select bg-light border-0 shadow-none py-2">
                                @foreach (\DB::table('categorie')->get() as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ $user->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- كلمة المرور الجديدة --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small d-block text-muted">
                                <i class="bi bi-key-fill text-warning me-2"></i>كلمة المرور الجديدة
                            </label>
                            <input type="password" name="password"
                                class="form-control bg-light border-0 shadow-none py-2"
                                placeholder="اتركها فارغة لعدم التغيير">
                        </div>

                        {{-- تأكيد كلمة المرور --}}
                        <div class="col-md-6 text-start">
                            <label class="form-label fw-bold small d-block text-muted">
                                <i class="bi bi-check-circle-fill text-warning me-2"></i>تأكيد كلمة المرور
                            </label>
                            <input type="password" name="password_confirmation"
                                class="form-control bg-light border-0 shadow-none py-2"
                                placeholder="أعد كتابة كلمة المرور">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-3 bg-light d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm rounded-pill">
                        <i class="bi bi-save2 me-2"></i>حفظ التعديلات
                    </button>
                    <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm"
                        data-bs-dismiss="modal">إلغاء</button>
                </div>
            </div>
        </form>
    </div>
</div>
