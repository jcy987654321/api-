@props([
    'type' => 'button', // 'button', 'submit', 'reset'
    'variant' => 'primary', // 'primary', 'success', 'danger', 'warning', 'info', 'secondary', 'light', 'dark'
    'size' => null, // 'sm', 'lg'
    'outline' => false,
    'icon' => null,
    'loading' => false,
    'disabled' => false,
])

@php
$baseClass = 'btn btn-admin';
$variantClass = $outline ? "btn-outline-{$variant}" : "btn-admin-{$variant}";
$sizeClass = $size ? "btn-{$size}" : '';
$loadingAttr = $loading ? 'disabled' : '';
@endphp

<{{ $type }}
    {{ $attributes->merge([
        'class' => trim(implode(' ', array_filter([$baseClass, $variantClass, $sizeClass]))),
        $loading => $loading,
        'disabled' => $disabled || $loading
    ]) }}
>
    @if($loading)
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        <span>Loading...</span>
    @else
        @if($icon)
            <i class="bi {{ $icon }} @if(!empty(trim($slot ?? ''))) me-2 @endif"></i>
        @endif
        {{ $slot }}
    @endif
</{{ $type }}>
