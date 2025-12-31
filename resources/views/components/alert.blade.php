@props([
    'variant' => 'info', // 'success', 'warning', 'danger', 'info'
    'dismissible' => false,
    'icon' => null,
])

@php
$baseClass = 'alert alert-admin';
$variantClass = "alert-admin-{$variant}";
$dismissibleClass = $dismissible ? 'alert-dismissible' : '';

$defaultIcons = [
    'success' => 'bi-check-circle-fill',
    'warning' => 'bi-exclamation-triangle-fill',
    'danger' => 'bi-x-circle-fill',
    'info' => 'bi-info-circle-fill'
];
$iconClass = $icon ?? ($defaultIcons[$variant] ?? 'bi-info-circle-fill');
@endphp

<div {{ $attributes->merge(['class' => trim(implode(' ', array_filter([$baseClass, $variantClass, $dismissibleClass])))]) }}>
    @if($icon)
        <i class="bi {{ $iconClass }} me-2"></i>
    @endif
    {{ $slot }}
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
