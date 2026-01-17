@extends('layouts.app')

@section('title', 'إضافة طالب جديد')

@section('content')
    <div class="app-content p-4">
        <div class="card card-outline card-primary shadow-sm" style="border-radius: 15px; border-top: 4px solid #007bff;">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold text-secondary mb-0">إضافة طالب جديد</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('student.store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <x-form-input name="full_name" label="الاسم رباعي" icon="bi-person" placeholder="أدخل الاسم رباعي"
                            required />

                        <x-form-input name="id_number" label="رقم الهوية" icon="bi-card-heading" maxlength="9"
                            inputmode="numeric" placeholder="أدخل رقم الهوية" required />

                        <x-form-input name="date_of_birth" type="date" label="تاريخ الميلاد" icon="bi-calendar3"
                            required />

                        <x-form-input name="phone_number" type="tel" label="رقم الهاتف" icon="bi-telephone"
                            placeholder="05XXXXXXXX" maxlength="10" required />

                        <x-form-input name="address" label="العنوان" icon="bi-geo-alt" placeholder="المدينة، الحي، الشارع"
                            required />

                        <x-form-select name="is_displaced" label="حالة السكن" icon="bi-house-door" required>
                            <option value="" selected disabled>اختر الحالة...</option>
                            <option value="0">مقيم</option>
                            <option value="1">نازح</option>
                        </x-form-select>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn btn-success px-5 fw-bold">إضافة الطالب</button>
                        <a href="{{ route('student.index') }}" class="btn btn-light px-4 border">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
