@props([
    'id' => null,
    'title' => 'Modal Title',
    'size' => null, // 'sm', 'lg', 'xl'
    'centered' => false,
    'scrollable' => false,
    'backdrop' => true, // true, false, 'static'
    'keyboard' => true,
])

@php
$sizeClass = $size ? "modal-{$size}" : '';
$centeredClass = $centered ? 'modal-dialog-centered' : '';
$scrollableClass = $scrollable ? 'modal-dialog-scrollable' : '';

if (!$id) {
    $id = 'modal-' . md5($title);
}
@endphp

<!-- Modal -->
<div class="modal fade"
     id="{{ $id }}"
     tabindex="-1"
     aria-labelledby="{{ $id }}Label"
     aria-hidden="true"
     data-bs-backdrop="{{ $backdrop === true ? 'true' : ($backdrop === false ? 'false' : $backdrop) }}"
     data-bs-keyboard="{{ $keyboard ? 'true' : 'false' }}">
    <div class="modal-dialog {{ $sizeClass }} {{ $centeredClass }} {{ $scrollableClass }}">
        <div class="modal-content modal-content-admin">
            @if($title || isset($header))
                <div class="modal-header modal-header-admin">
                    @if($title)
                        <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                    @else
                        {{ $header }}
                    @endif
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
            @endif

            @if(isset($slot))
                <div class="modal-body modal-body-admin">
                    {{ $slot }}
                </div>
            @endif

            @if(isset($footer))
                <div class="modal-footer modal-footer-admin">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
