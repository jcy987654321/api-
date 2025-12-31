@props([
    'variant' => 'primary', // 'primary', 'success', 'warning', 'danger', 'info', 'secondary', 'light', 'dark'
    'pill' => false,
    'icon' => null,
])

@php
$baseClass = 'badge badge-admin';
$variantClass = "badge-admin-{$variant}";
$pillClass = $pill ? 'rounded-pill' : '';
@endphp

<span {{ $attributes->merge(['class' => trim(implode(' ', array_filter([$baseClass, $variantClass, $pillClass])))]) }}>
    @if($icon)
        <i class="bi {{ $icon }} @if(!empty(trim($slot ?? ''))) me-1 @endif"></i>
    @endif
    {{ $slot }}
</span>
