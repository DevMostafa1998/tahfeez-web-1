{{-- مودال تعديل المجموعة --}}
<div class="modal fade" id="editGroup{{ $group->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">تعديل مجموعة: {{ $group->GroupName }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('group.update', $group->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">اسم المجموعة</label>
                        <input type="text" name="GroupName" class="form-control" value="{{ $group->GroupName }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">اسم المحفظ</label>
                        <select name="UserId" class="form-select" required>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}"
                                    {{ $group->UserId == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary px-4">تحديث البيانات</button>
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
