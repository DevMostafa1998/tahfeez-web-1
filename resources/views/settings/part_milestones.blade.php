@extends('layouts.app')
@section('title', 'مراحل مراجعة الأجزاء')

@section('content')
<div class="container-fluid py-3" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-primary text-white py-3" style="border-radius: 15px 15px 0 0;">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-list-check me-2"></i> مراحل مراجعة الأجزاء</h5>
                    <small class="opacity-75">حدد أرقام الأجزاء التي عند وصولها يرسل رسالة وإشعار للمحفظ </small>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('settings.part_milestones.update') }}">
                        @csrf
                        <div class="row g-2">
                            @for($i = 1; $i <= 30; $i++)
                                <div class="col-3 col-md-2">
                                    <div class="form-check text-center border rounded py-2 {{ in_array($i, $milestones) ? 'bg-primary-subtle border-primary' : '' }}">
                                        <input class="form-check-input ms-0 me-1" type="checkbox"
                                               name="milestones[]" value="{{ $i }}" id="ms{{ $i }}"
                                               {{ in_array($i, $milestones) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="ms{{ $i }}">{{ $i }}</label>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <div class="alert alert-info mt-3 mb-0 small">
                            <i class="bi bi-info-circle me-1"></i>
عند بلوغ الطالب لأي جزء محدد تنشأ مراجعة تلقائيا ويصل رسالة وإشعار للمحفظ من الادمن . وعند المراجعة يرسل رسالة للمسؤول بان الطالب جاهز للختبار بالأجزاء المنجزة.                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-check-lg me-1"></i> حفظ المراحل
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
