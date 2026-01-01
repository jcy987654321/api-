@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users')

@section('breadcrumb')
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2 align-items-end flex-grow-1 me-3">
            <div class="flex-grow-1">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Name / Email">
            </div>
            <div>
                <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i> Search</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th style="width: 180px;">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td class="fw-semibold">{{ $user->name }}</td>
                                <td class="text-muted">{{ $user->email }}</td>
                                <td class="text-muted">{{ $user->created_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
@endsection
