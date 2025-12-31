@props([
    'title' => 'Stat',
    'value' => '--',
    'icon' => 'bi-graph-up',
    'iconColor' => 'primary',
    'change' => null,
    'changePercent' => null,
    'trend' => null, // 'up', 'down', 'neutral'
    'showTrend' => true,
])

<div class="stat-card {{ $attributes->class }}">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <div class="stat-label">{{ $title }}</div>
            <div class="stat-value mt-2">{{ $value }}</div>
            @if($showTrend && ($change || $changePercent))
                <div class="stat-change @if($trend === 'up') up @elseif($trend === 'down') down @endif">
                    @if($trend === 'up')
                        <i class="bi bi-arrow-up-circle"></i>
                    @elseif($trend === 'down')
                        <i class="bi bi-arrow-down-circle"></i>
                    @else
                        <i class="bi bi-dash-circle"></i>
                    @endif
                    @if($change)
                        <span>{{ $change }}</span>
                    @endif
                    @if($changePercent)
                        <span>({{ $changePercent }})</span>
                    @endif
                </div>
            @endif
        </div>
        <div class="stat-icon {{ $iconColor }}">
            <i class="bi {{ $icon }}"></i>
        </div>
    </div>
</div>
