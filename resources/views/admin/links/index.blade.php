@extends('layouts.admin')

@section('title', '友链管理')
@section('page-title', '友链管理')

@section('breadcrumb')
    <li class="breadcrumb-item active">Friend Links</li>
@endsection

@section('content')
    @if(session('success'))
        <x-alert variant="success" dismissible="true" class="mb-3">{{ session('success') }}</x-alert>
    @endif
    @if(session('error'))
        <x-alert variant="danger" dismissible="true" class="mb-3">{{ session('error') }}</x-alert>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('admin.links.index') }}" class="row g-2 align-items-end flex-grow-1 me-3">
            <div class="col-md-3">
                <label class="form-label">状态</label>
                <select name="status" class="form-select">
                    <option value="pending" @selected($status === 'pending')>待审核</option>
                    <option value="approved" @selected($status === 'approved')>已批准</option>
                    <option value="rejected" @selected($status === 'rejected')>已拒绝</option>
                    <option value="all" @selected($status === 'all')>全部</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">分类</label>
                <select name="category" class="form-select">
                    <option value="">全部</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" @selected($category === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i> 筛选</button>
            </div>
            <div class="col-md-2 d-grid">
                <a href="{{ route('admin.links.create') }}" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i> 新建</a>
            </div>
        </form>
    </div>

    @if($status === 'approved')
        <div class="alert alert-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">拖拽排序</div>
                    <div class="small text-muted">拖拽表格行可调整展示顺序（仅对已批准友链生效）。</div>
                </div>
                <button class="btn btn-sm btn-outline-primary" id="save-order-btn" type="button" disabled>
                    <i class="bi bi-save"></i> 保存排序
                </button>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="links-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>名称</th>
                            <th style="width: 160px;">分类</th>
                            <th style="width: 120px;">状态</th>
                            <th style="width: 100px;">点击</th>
                            <th style="width: 90px;">排序</th>
                            <th style="width: 220px;" class="text-end">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($links as $link)
                            <tr data-link-id="{{ $link->id }}" @if($status === 'approved') draggable="true" class="link-row-draggable" @endif>
                                <td>{{ $link->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $link->title }}</div>
                                    <div class="text-muted small">{{ $link->url }}</div>
                                </td>
                                <td>{{ $link->category }}</td>
                                <td>
                                    @if($link->status === 'approved')
                                        <span class="badge text-bg-success">已批准</span>
                                    @elseif($link->status === 'rejected')
                                        <span class="badge text-bg-danger">已拒绝</span>
                                    @else
                                        <span class="badge text-bg-warning">待审核</span>
                                    @endif
                                </td>
                                <td>{{ $link->clicks }}</td>
                                <td>{{ $link->order }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.links.edit', $link->id) }}" title="编辑">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        @if($link->status !== 'approved')
                                            <form method="POST" action="{{ route('admin.links.approve', $link->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-success" type="submit" title="批准">
                                                    <i class="bi bi-check2"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($link->status !== 'rejected')
                                            <form method="POST" action="{{ route('admin.links.reject', $link->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-warning" type="submit" title="拒绝">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.links.destroy', $link->id) }}" onsubmit="return confirm('确定要删除该友链吗？');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit" title="删除">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">暂无数据。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $links->links() }}
    </div>
@endsection

@if($status === 'approved')
    @push('scripts')
        <script>
            (function () {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const table = document.getElementById('links-table');
                const saveBtn = document.getElementById('save-order-btn');

                if (!token || !table || !saveBtn) return;

                let dragging = null;
                let dirty = false;

                function setDirty(v) {
                    dirty = v;
                    saveBtn.disabled = !dirty;
                }

                table.querySelectorAll('tbody tr.link-row-draggable').forEach((row) => {
                    row.addEventListener('dragstart', (e) => {
                        dragging = row;
                        e.dataTransfer.effectAllowed = 'move';
                        row.classList.add('table-primary');
                    });

                    row.addEventListener('dragend', () => {
                        row.classList.remove('table-primary');
                        dragging = null;
                    });

                    row.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        if (!dragging || dragging === row) return;

                        const rect = row.getBoundingClientRect();
                        const isAfter = (e.clientY - rect.top) > (rect.height / 2);
                        const parent = row.parentNode;

                        if (isAfter) {
                            parent.insertBefore(dragging, row.nextSibling);
                        } else {
                            parent.insertBefore(dragging, row);
                        }

                        setDirty(true);
                    });
                });

                saveBtn.addEventListener('click', async () => {
                    const orderedIds = Array.from(table.querySelectorAll('tbody tr[data-link-id]')).map((tr) => Number(tr.getAttribute('data-link-id')));

                    const res = await fetch(`{{ route('admin.links.reorder') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ ordered_ids: orderedIds }),
                    });

                    if (res.ok) {
                        setDirty(false);
                        window.location.reload();
                        return;
                    }

                    alert('保存失败，请稍后重试。');
                });
            })();
        </script>
    @endpush
@endif
