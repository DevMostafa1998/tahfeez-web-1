@extends('layouts.app')

@section('title', 'تعديل بيانات الطالب')

@section('content')
    <div class="container-fluid p-4">
        <div class="card card-outline card-primary shadow-sm" style="border-radius: 15px; border-top: 4px solid #007bff;">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold text-secondary mb-0">تعديل بيانات الطالب: {{ $student->full_name }}</h5>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('student.update', $student->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        {{-- استخدام المكونات الجديدة --}}
                        <x-form-input name="full_name" label="الاسم رباعي" icon="bi-person" :value="$student->full_name" required />

                        <x-form-input name="id_number" label="رقم الهوية" icon="bi-card-heading" :value="$student->id_number"
                            maxlength="9" required />

                        <x-form-input type="date" name="date_of_birth" label="تاريخ الميلاد" icon="bi-calendar3"
                            :value="$student->date_of_birth->format('Y-m-d')" required />

                        <x-form-input type="tel" name="phone_number" label="رقم الهاتف" icon="bi-telephone"
                            :value="$student->phone_number" maxlength="10" required />

                        <x-form-input name="address" label="العنوان" icon="bi-geo-alt" :value="$student->address" required />

                        <x-form-select name="is_displaced" label="حالة السكن" icon="bi-house-door" required>
                            <option value="0" {{ $student->is_displaced == 0 ? 'selected' : '' }}>مقيم</option>
                            <option value="1" {{ $student->is_displaced == 1 ? 'selected' : '' }}>نازح</option>
                        </x-form-select>
                    </div>

                    <div class="card-footer bg-white border-0 mt-5 p-0 text-start">
                        <button type="submit" class="btn btn-primary px-5 fw-bold">
                            <i class="bi bi-save me-1"></i> حفظ التعديلات
                        </button>
                        <a href="{{ route('student.index') }}" class="btn btn-light px-4 border">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
