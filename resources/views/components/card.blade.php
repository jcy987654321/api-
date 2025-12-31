@props([
    'title' => null,
    'icon' => null,
    'headerActions' => null,
    'borderless' => false,
    'shadow' => null, // 'sm', 'md', 'lg', 'none'
])

@php
$shadowClass = $shadow ? "shadow-{$shadow}" : ($shadow === null ? '' : 'shadow');
$borderClass = $borderless ? 'border-0' : '';
@endphp

<div {{ $attributes->merge(['class' => "card admin-card {$shadowClass} {$borderClass}"]) }}>
    @if($title || $icon || $headerActions)
        <div class="card-header d-flex justify-content-between align-items-center">
            @if($title || $icon)
                <h5 class="card-title mb-0">
                    @if($icon)
                        <i class="bi {{ $icon }} me-2"></i>
                    @endif
                    {{ $title }}
                </h5>
            @endif

            @if($headerActions)
                <div>{{ $headerActions }}</div>
            @endif
        </div>
    @endif

    @if(isset($slot))
        <div class="card-body">
            {{ $slot }}
        </div>
    @endif

    @if(isset($footer))
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
