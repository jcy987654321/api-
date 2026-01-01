@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">友链</h1>
            <div class="text-muted small">本站合作/推荐站点列表</div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-pills flex-wrap gap-2 mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $currentCategory === null ? 'active' : '' }}" href="{{ route('links.index') }}">全部</a>
        </li>
        @foreach($categories as $cat)
            <li class="nav-item">
                <a class="nav-link {{ $currentCategory === $cat ? 'active' : '' }}" href="{{ route('links.category', $cat) }}">{{ $cat }}</a>
            </li>
        @endforeach
    </ul>

    @forelse($groupedLinks as $cat => $links)
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h2 class="h5 mb-0">{{ $cat }}</h2>
            <span class="text-muted small">{{ is_countable($links) ? count($links) : 0 }} 个</span>
        </div>

        <div class="row g-3 mb-4">
            @forelse($links as $link)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex gap-3">
                                <div class="flex-shrink-0">
                                    @if(!empty($link['logo_url']))
                                        <img src="{{ $link['logo_url'] }}" alt="{{ $link['title'] }}" class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                            <span class="text-muted fw-semibold">{{ mb_substr($link['title'], 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <div class="fw-semibold">{{ $link['title'] }}</div>
                                            @if(!empty($link['description']))
                                                <div class="text-muted small">{{ $link['description'] }}</div>
                                            @endif
                                        </div>
                                        <span class="badge text-bg-light">{{ $link['clicks'] ?? 0 }} clicks</span>
                                    </div>

                                    <div class="mt-3">
                                        <a href="{{ $link['url'] }}"
                                           class="btn btn-sm btn-primary link-out"
                                           data-link-id="{{ $link['id'] }}"
                                           target="_blank" rel="noopener">
                                            访问
                                        </a>
                                        <a href="{{ $link['url'] }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener">
                                            {{ \Illuminate\Support\Str::limit($link['url'], 26) }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-muted">该分类暂无友链。</div>
                </div>
            @endforelse
        </div>
    @empty
        <div class="text-center py-5 text-muted">暂无友链。</div>
    @endforelse

    <hr class="my-5">

    <h2 class="h5">提交友链</h2>
    <p class="text-muted small">提交后需要管理员审核通过后展示。</p>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('links.store') }}" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">名称</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">URL</label>
                    <input type="url" name="url" value="{{ old('url') }}" class="form-control @error('url') is-invalid @enderror" required>
                    @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Logo URL（可选）</label>
                    <input type="url" name="logo_url" value="{{ old('logo_url') }}" class="form-control @error('logo_url') is-invalid @enderror">
                    @error('logo_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">分类</label>
                    <input type="text" name="category" value="{{ old('category') }}" class="form-control @error('category') is-invalid @enderror" required>
                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">描述（可选）</label>
                    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <button class="btn btn-success" type="submit">提交</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            document.querySelectorAll('.link-out').forEach((el) => {
                el.addEventListener('click', function () {
                    const id = this.getAttribute('data-link-id');
                    if (!id || !token) return;

                    fetch(`/links/${id}/click`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        },
                    }).catch(() => {});
                });
            });
        })();
    </script>
@endpush
