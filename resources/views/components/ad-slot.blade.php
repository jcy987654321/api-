@if($advertisement)
    <div class="ad-slot ad-slot-{{ $slot->identifier }}" data-ad-id="{{ $advertisement->id }}">
        @if($advertisement->type === 'image')
            @if($advertisement->link_url)
                <a href="{{ $advertisement->link_url }}" 
                   @if($advertisement->open_new_tab) target="_blank" rel="noopener" @endif
                   onclick="recordAdClick({{ $advertisement->id }})">
                    <img src="{{ $advertisement->content }}" alt="{{ $advertisement->title }}" 
                         style="max-width: 100%; height: auto;">
                </a>
            @else
                <img src="{{ $advertisement->content }}" alt="{{ $advertisement->title }}" 
                     style="max-width: 100%; height: auto;">
            @endif
        @elseif($advertisement->type === 'html')
            {!! $advertisement->content !!}
        @elseif($advertisement->type === 'script')
            {!! $advertisement->content !!}
        @endif
    </div>

    <script>
        function recordAdClick(adId) {
            fetch('/api/ads/' + adId + '/click', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        }
    </script>
@endif
