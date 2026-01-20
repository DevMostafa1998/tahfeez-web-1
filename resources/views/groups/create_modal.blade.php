{{-- مودال إضافة مجموعة جديدة --}}
<div class="modal fade" id="createGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">إضافة مجموعة جديدة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('group.store') }}" method="POST" id="createGroupForm">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">اسم المجموعة</label>
                        <input type="text" name="GroupName" class="form-control" required
                            placeholder="مثال: مجموعة التميز">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">اختر المحفظ</label>
                        <select name="UserId" class="form-select" required>
                            <option value="" selected disabled>اختر...</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary px-4">حفظ</button>
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
