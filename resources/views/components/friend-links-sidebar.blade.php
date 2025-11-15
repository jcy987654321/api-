<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Friend Links</h5>
    </div>
    <div class="card-body">
        @if($friendLinks->count() > 0)
            <ul class="list-unstyled mb-0">
                @foreach($friendLinks as $link)
                    <li class="mb-2">
                        <a href="{{ $link->url }}" target="_blank" rel="noopener" class="d-flex align-items-center text-decoration-none">
                            @if($link->logo)
                                <img src="{{ $link->logo }}" alt="{{ $link->name }}" style="width: 24px; height: 24px; margin-right: 8px; object-fit: contain;">
                            @endif
                            <span>{{ $link->name }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="mt-3">
                <a href="{{ route('friend-links.index') }}" class="btn btn-sm btn-outline-primary w-100" data-pjax>View All</a>
            </div>
        @else
            <p class="text-muted mb-0">No friend links available yet.</p>
        @endif
    </div>
</div>
