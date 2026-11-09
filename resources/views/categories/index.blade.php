@extends('layouts.app')
@section('title', 'إدارة التصنيفات')

@section('content')
<div class="container-fluid py-4" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-9 col-md-11">

            <div class="d-flex align-items-center mb-3 text-dark ps-2">
                <i class="bi bi-tag-fill fs-4 me-2 text-primary"></i>
                <h5 class="fw-bold mb-0">إدارة التصنيفات</h5>
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

            <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px;">
                <div class="card-body p-2">
                    <form action="{{ route('category.store') }}" method="POST" class="row g-2 align-items-center">
                        @csrf

                        <div class="col-md-5">
                            <input type="text" name="name" class="form-control form-control-sm border-0 bg-light"
                                   placeholder="أدخل تصنيف جديد " required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل')" oninput="this.setCustomValidity('')" style="border-radius: 8px;">
                        </div>

                        <div class="col-md-5">
                            <select name="center_id" class="form-select form-select-sm border-0 bg-light fw-bold text-secondary" style="border-radius: 8px;">
                                <option value="" {{ empty($selectedCenterId) ? 'selected' : '' }}>عام (متاح لجميع المراكز)</option>
                                @foreach($centers as $center)
                                    <option value="{{ $center->id }}" {{ (isset($selectedCenterId) && $selectedCenterId == $center->id) ? 'selected' : '' }}>
                                        {{ $center->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-auto me-auto">
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm" style="border-radius: 8px;">
                                <i class="bi bi-plus-lg ms-1"></i> إضافة
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- جدول عرض التصنيفات --}}
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0 text-center">
                        <thead class="bg-light">
                            <tr class="text-secondary small">
                                <th class="text-start ps-4 py-3" style="width: 25%;">اسم التصنيف</th>
                                <th class="py-3" style="width: 45%;">المركز التابع له</th>
                                <th class="py-3" style="width: 25%;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($categories as $category)
                            <tr>
                                <td class="text-start ps-4">
                                    <div id="view-name-{{ $category->id }}" class="fw-bold text-dark d-flex align-items-center">
                                        <span class="text-primary me-2" style="font-size: 1.5rem; line-height: 0;">•</span>
                                        {{ $category->name }}
                                    </div>
                                </td>

                               <td class="text-center category-view-{{ $category->id }}">
                                <div id="view-center-{{ $category->id }}">
                                    @if($category->center)
                                        <span class="badge bg-light text-primary border border-primary-subtle fs-7 px-3 py-2 fw-bold shadow-sm">
                                            <i class="bi bi-building me-1"></i> {{ $category->center->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle fs-7 px-3 py-2 fw-bold shadow-sm">
                                            <i class="bi bi-globe me-1"></i> عام (جميع المراكز)
                                        </span>
                                    @endif
                                </div>
                            </td>

                                <td id="edit-cell-{{ $category->id }}" colspan="2" class="d-none text-start ps-4">
                                    <form id="edit-form-{{ $category->id }}" action="{{ route('category.update', $category->id) }}" method="POST" class="w-100">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-6">
                                                <input type="text" name="name" class="form-control form-control-sm border-warning shadow-none"
                                                       value="{{ $category->name }}" required oninvalid="this.setCustomValidity('يرجى ملء هذا الحقل')" oninput="this.setCustomValidity('')">
                                            </div>
                                            <div class="col-md-4">
                                                <select name="center_id" class="form-select form-select-sm border-warning shadow-none">
                                                    <option value="" {{ is_null($category->center_id) ? 'selected' : '' }}>عام (جميع المراكز)</option>
                                                    @foreach($centers as $center)
                                                        <option value="{{ $center->id }}" {{ $category->center_id == $center->id ? 'selected' : '' }}>
                                                            {{ $center->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2 d-flex gap-1">
                                                <button type="submit" class="btn btn-warning btn-sm text-white px-2"><i class="bi bi-check-lg"></i></button>
                                                <button type="button" class="btn btn-light border btn-sm px-2" onclick="toggleEdit({{ $category->id }})"><i class="bi bi-x"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </td>

                                {{-- عمود الإجراءات --}}
                                <td>
                                    <div class="btn-group gap-2 justify-content-center">
                                        <button class="btn btn-sm btn-outline-warning rounded-circle edit-action-btn"
                                                onclick="toggleEdit({{ $category->id }})"
                                                id="edit-btn-{{ $category->id }}"
                                                style="width: 32px; height: 32px; padding: 0;">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('category.destroy', $category->id) }}" method="POST"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"
                                                    style="width: 32px; height: 32px; padding: 0;">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-4 text-muted small">قائمة التصنيفات فارغة لهذا المركز.</td>
                            </tr>
                            @endforelse
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
        const viewCenter = document.getElementById(`view-center-${id}`);
        const editCell = document.getElementById(`edit-cell-${id}`);
        const editBtn = document.getElementById(`edit-btn-${id}`);

        if (editCell.classList.contains('d-none')) {
            viewName.parentElement.classList.add('d-none');
            viewCenter.parentElement.classList.add('d-none');
            editCell.classList.remove('d-none');
            editBtn.style.visibility = 'hidden';
        } else {
            viewName.parentElement.classList.remove('d-none');
            viewCenter.parentElement.classList.remove('d-none');
            editCell.classList.add('d-none');
            editBtn.style.visibility = 'visible';
        }
    }
</script>
@endpush

<style>
    .table td, .table th { padding: 0.6rem !important; }
    .fw-bold { font-weight: 600 !important; }

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

    .btn:focus, .form-control:focus, .form-select:focus {
        box-shadow: none !important;
    }

    .bg-light:focus {
        background-color: #fff !important;
        border: 1px solid #0d6efd !important;
    }
</style>
@endsection
