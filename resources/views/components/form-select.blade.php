@props(['name', 'label', 'icon' => '', 'required' => false])

<div class="col-md-6">
    <label class="form-label fw-bold small text-muted">{{ $label }}</label>
    <div class="input-group">
        @if ($icon)
            <span class="input-group-text bg-light"><i class="bi {{ $icon }} text-primary"></i></span>
        @endif
        <select name="{{ $name }}" class="form-select @error($name) is-invalid @enderror"
            {{ $required ? 'required' : '' }}>
            {{ $slot }}
        </select>
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
