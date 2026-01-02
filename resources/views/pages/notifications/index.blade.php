@extends('layouts.app')

@section('title', 'My Notifications')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="bi bi-bell me-2"></i>My Notifications</h3>
            <div>
                @if($unreadCount > 0)
                    <form action="{{ route('user.notifications.read-all') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-check-all me-1"></i>Mark All as Read
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('user.notifications.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">All Types</option>
                            <option value="feedback_reply" {{ request('type') === 'feedback_reply' ? 'selected' : '' }}>Feedback Reply</option>
                            <option value="system_message" {{ request('type') === 'system_message' ? 'selected' : '' }}>System Message</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="read" class="form-label">Status</label>
                        <select class="form-select" id="read" name="read">
                            <option value="">All</option>
                            <option value="unread" {{ request('read') === 'unread' ? 'selected' : '' }}>Unread</option>
                            <option value="read" {{ request('read') === 'read' ? 'selected' : '' }}>Read</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('user.notifications.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                @forelse($notifications as $notification)
                    <div class="notification-item p-3 border-bottom {{ $notification->is_unread ? 'unread' : '' }}" id="notification-{{ $notification->id }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex gap-3">
                                <div class="notification-icon">
                                    @if($notification->type === 'feedback_reply')
                                        <i class="bi bi-chat-dots text-primary fs-5"></i>
                                    @else
                                        <i class="bi bi-info-circle text-info fs-5"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1 {{ $notification->is_unread ? 'fw-bold' : '' }}">
                                        {{ $notification->title }}
                                    </h6>
                                    <p class="mb-1 text-muted small">{{ $notification->message }}</p>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                @if($notification->is_unread)
                                    <button type="button" class="btn btn-sm btn-outline-success mark-read-btn"
                                            data-id="{{ $notification->id }}" title="Mark as read">
                                        <i class="bi bi-check"></i>
                                    </button>
                                @endif
                                <form action="{{ route('user.notifications.delete', $notification->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if($notification->related_id && $notification->type === 'feedback_reply')
                            <div class="mt-2">
                                <a href="{{ route('feedbacks.show', $notification->related_id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-arrow-right me-1"></i>View Feedback
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-bell-slash fs-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">No notifications</h5>
                        <p class="text-muted">You don't have any notifications yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $notifications->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<style>
.notification-item.unread {
    background-color: #f8f9fa;
    border-left: 4px solid #0d6efd;
}
.notification-item {
    transition: background-color 0.2s;
}
.notification-item:hover {
    background-color: #f8f9fa;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark as read buttons
    const markReadBtns = document.querySelectorAll('.mark-read-btn');
    markReadBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`/user/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = document.getElementById(`notification-${id}`);
                    item.classList.remove('unread');
                    item.querySelector('h6').classList.remove('fw-bold');
                    this.remove();
                    updateUnreadCount(data.unread_count);
                }
            });
        });
    });

    function updateUnreadCount(count) {
        const badge = document.getElementById('nav-unread-count');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline' : 'none';
        }
    }
});
</script>
@endpush
@endsection
