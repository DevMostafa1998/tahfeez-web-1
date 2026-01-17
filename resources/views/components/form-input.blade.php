@props(['name', 'label', 'type' => 'text', 'value' => '', 'icon' => '', 'required' => false])

<div class="col-md-6">
    <label class="form-label fw-bold small text-muted">{{ $label }}</label>
    <div class="input-group">
        @if ($icon)
            <span class="input-group-text bg-light"><i class="bi {{ $icon }} text-primary"></i></span>
        @endif
        <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}"
            class="form-control @error($name) is-invalid @enderror" {{ $required ? 'required' : '' }}
            {{ $attributes }}>
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
