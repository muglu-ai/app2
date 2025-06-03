@props(['label', 'value', 'link' => false])

<div class="col-md-4">
    <label class="form-label fw-bold text-nowrap">{{ $label }}:</label>
    <p class="form-control-plaintext mb-0">
        @if($link && $value)
            <a href="{{ $value }}" target="_blank" class="text-primary text-decoration-underline">{{ $value }}</a>
        @else
            {{ $value ?? '-' }}
        @endif
    </p>
</div>
