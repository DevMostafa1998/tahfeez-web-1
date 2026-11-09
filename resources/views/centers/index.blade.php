@extends('layouts.app')
@section('title', 'إدارة المراكز')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid py-4" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8 col-md-10">

            <div class="d-flex align-items-center mb-3 text-dark ps-2">
                <i class="bi bi-building fs-4 me-2 text-primary"></i>
                <h5 class="fw-bold mb-0">إدارة المراكز</h5>
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm py-2 small mb-3 text-end">
                    <i class="bi bi-check-lg me-1"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm py-2 small mb-3 text-end">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                </div>
            @endif

            {{-- فورم إضافة مركز جديد --}}
            <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px;">
                <div class="card-body p-2">
                    <form action="{{ route('centers.store') }}" method="POST" class="row g-2 align-items-center">
                        @csrf
                        <div class="col">
                            <input type="text" name="name" class="form-control form-control-sm border-0 bg-light"
                                   placeholder="أدخل اسم المركز الجديد..." required
                                   oninvalid="this.setCustomValidity('يرجى ملء this الحقل')"
                                   oninput="this.setCustomValidity('')" style="border-radius: 8px;">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm" style="border-radius: 8px;">
                                <i class="bi bi-plus-lg ms-1"></i> إضافة
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- جدول عرض المراكز --}}
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0 text-center">
                        <thead class="bg-light">
                            <tr class="text-secondary small">
                                <th class="text-start ps-4 py-3" style="width: 65%;">اسم المركز</th>
                                <th class="py-3" style="width: 35%;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($centers as $center)
                            <tr>
                                <td class="text-start ps-4">
                                    <div id="view-name-{{ $center->id }}" class="fw-bold text-dark d-flex align-items-center">
                                        <span class="text-primary me-2" style="font-size: 1.5rem; line-height: 0;">•</span>
                                        {{ $center->name }}
                                    </div>

                                    <form id="edit-form-{{ $center->id }}" action="{{ route('centers.update', $center->id) }}" method="POST" class="d-none mt-1">
                                        @csrf
                                        @method('PUT')
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="name" class="form-control form-control-sm border-warning shadow-none" value="{{ $center->name }}" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل')" oninput="this.setCustomValidity('')">
                                            <button type="submit" class="btn btn-warning btn-sm text-white px-2"><i class="bi bi-check-lg"></i></button>
                                            <button type="button" class="btn btn-light border btn-sm px-2" onclick="toggleEdit({{ $center->id }})"><i class="bi bi-x"></i></button>
                                        </div>
                                    </form>
                                </td>

                                <td>
                                    <div class="btn-group gap-2 justify-content-center">
                                        <button class="btn btn-sm btn-outline-warning rounded-circle edit-action-btn"
                                                onclick="toggleEdit({{ $center->id }})"
                                                id="edit-btn-{{ $center->id }}"
                                                style="width: 32px; height: 32px; padding: 0;">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('centers.destroy', $center->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle btn-delete-submit"
                                                    style="width: 32px; height: 32px; padding: 0;">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="py-4 text-muted small">قائمة المراكز فارغة.</td>
                            </tr>
                            @endempty
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleEdit(id) {
        const viewName = document.getElementById(`view-name-${id}`);
        const editForm = document.getElementById(`edit-form-${id}`);
        const editBtn = document.getElementById(`edit-btn-${id}`);

        if (editForm.classList.contains('d-none')) {
            editForm.classList.remove('d-none');
            viewName.classList.add('d-none');
            editBtn.style.visibility = 'hidden';
        } else {
            editForm.classList.add('d-none');
            viewName.classList.remove('d-none');
            editBtn.style.visibility = 'visible';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.btn-delete-submit');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                const form = this.closest('.delete-form');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "لن تتمكن من التراجع عن عملية الحذف هذه!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، احذف المركز',
                    cancelButtonText: 'إلغاء',
                    customClass: {
                        popup: 'small-swal-font'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush

<style>
    .table td, .table th { padding: 0.6rem !important; }
    .fw-bold { font-weight: 600 !important; }

    .small-swal-font {
        font-family: inherit !important;
        font-size: 0.9rem !important;
    }

    .rounded-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border-width: 1px;
    }

    .btn-outline-warning:hover {
        background-color: #ffc107 !important;
        color: #fff !important;
    }

    .btn-outline-danger:hover {
        background-color: #dc3545 !important;
        color: #fff !important;
    }

    .btn:focus, .form-control:focus {
        box-shadow: none !important;
    }

    .bg-light:focus {
        background-color: #fff !important;
        border: 1px solid #0d6efd !important;
    }
</style>
@endsection
