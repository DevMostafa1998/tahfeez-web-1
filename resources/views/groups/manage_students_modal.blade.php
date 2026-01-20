{{-- مودال إدارة الطلاب --}}
<div class="modal fade" id="manageStudents{{ $group->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title fw-bold">إدارة طلاب: {{ $group->GroupName }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('studentgroup.store') }}" method="POST">
                @csrf
                <input type="hidden" name="group_id" value="{{ $group->id }}">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="بحث عن طالب..."
                            onkeyup="filterStudents(this, 'list{{ $group->id }}')">
                    </div>
                    <div class="border rounded p-3 bg-light" style="max-height: 350px; overflow-y: auto;"
                        id="list{{ $group->id }}">
                        <div class="row g-2">
                            @foreach ($group->students as $st)
                                <div class="col-md-6 student-item">
                                    <div class="form-check card p-2 border-success border-opacity-25 shadow-sm">
                                        <input class="form-check-input ms-2" type="checkbox" name="student_ids[]"
                                            value="{{ $st->id }}" checked
                                            id="st{{ $group->id }}{{ $st->id }}">
                                        <label class="form-check-label" for="st{{ $group->id }}{{ $st->id }}">
                                            <span class="fw-bold d-block">{{ $st->full_name }}</span>
                                            <small class="text-success">مسجل حالياً</small>
                                            <span class="badge bg-success-subtle text-success border-0 fw-normal">
                                                {{ \Carbon\Carbon::parse($st->date_of_birth)->age }} سنة
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            @foreach ($availableStudents as $st)
                                <div class="col-md-6 student-item">
                                    <div class="form-check card p-2 border shadow-sm">
                                        <input class="form-check-input ms-2" type="checkbox" name="student_ids[]"
                                            value="{{ $st->id }}"
                                            id="st_av{{ $group->id }}{{ $st->id }}">
                                        <label class="form-check-label"
                                            for="st_av{{ $group->id }}{{ $st->id }}">
                                            <span class="fw-bold d-block">{{ $st->full_name }}</span>
                                            <small class="text-muted">غير مسجل</small>
                                            <span class="badge bg-success-subtle text-success border-0 fw-normal">
                                                {{ \Carbon\Carbon::parse($st->date_of_birth)->age }} سنة
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-warning px-4">حفظ</button>
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
