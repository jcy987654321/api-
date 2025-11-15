@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container" style="padding: 2rem 0;">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="color: #1f2937; margin-bottom: 0.5rem;">User Management</h2>
            <p style="color: #6b7280;">Manage system users and their roles</p>
        </div>
        <button onclick="showCreateUserModal()" style="
            padding: 0.75rem 1.5rem;
            background-color: #10b981;
            color: white;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-weight: 500;
        ">
            Add New User
        </button>
    </div>

    <div style="
        padding: 1.5rem;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    ">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: #374151;">Name</th>
                        <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: #374151;">Email</th>
                        <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: #374151;">Role</th>
                        <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: #374151;">Created</th>
                        <th style="text-align: center; padding: 0.75rem; font-weight: 600; color: #374151;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 0.75rem;">{{ $user->name }}</td>
                            <td style="padding: 0.75rem;">{{ $user->email }}</td>
                            <td style="padding: 0.75rem;">
                                <span style="
                                    padding: 0.25rem 0.75rem;
                                    border-radius: 9999px;
                                    font-size: 0.75rem;
                                    font-weight: 500;
                                    {{ $user->role === 'admin' ? 'background-color: #fef2f2; color: #dc2626;' : '' }}
                                    {{ $user->role === 'manager' ? 'background-color: #fef3c7; color: #d97706;' : '' }}
                                    {{ $user->role === 'user' ? 'background-color: #f0f9ff; color: #0284c7;' : '' }}
                                ">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem; color: #6b7280; font-size: 0.875rem;">
                                {{ $user->created_at->format('M j, Y') }}
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                <button onclick="editUser({{ $user->id }})" style="
                                    padding: 0.25rem 0.5rem;
                                    background-color: #3b82f6;
                                    color: white;
                                    border: none;
                                    border-radius: 0.25rem;
                                    margin-right: 0.5rem;
                                    cursor: pointer;
                                    font-size: 0.75rem;
                                ">
                                    Edit
                                </button>
                                @if ($user->id !== auth()->id())
                                    <button onclick="deleteUser({{ $user->id }})" style="
                                        padding: 0.25rem 0.5rem;
                                        background-color: #ef4444;
                                        color: white;
                                        border: none;
                                        border-radius: 0.25rem;
                                        cursor: pointer;
                                        font-size: 0.75rem;
                                    ">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 2rem; text-align: center; color: #6b7280;">
                                No users found. Create your first user to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <div style="margin-top: 2rem;">
        <a href="{{ route('admin.dashboard') }}" data-pjax style="
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #e5e7eb;
            color: #1f2937;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        ">
            ← Back to Dashboard
        </a>
    </div>
</div>

<!-- Create User Modal -->
<div id="createUserModal" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1000;
">
    <div style="
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 2rem;
        border-radius: 0.5rem;
        width: 90%;
        max-width: 400px;
    ">
        <h3 style="margin-bottom: 1.5rem;">Create New User</h3>
        <form id="createUserForm">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Name</label>
                <input type="text" name="name" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Role</label>
                <select name="role" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                    <option value="user">User</option>
                    <option value="manager">Manager</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="hideCreateUserModal()" style="
                    padding: 0.75rem 1.5rem;
                    background-color: #e5e7eb;
                    color: #1f2937;
                    border: none;
                    border-radius: 0.375rem;
                    cursor: pointer;
                ">
                    Cancel
                </button>
                <button type="submit" style="
                    padding: 0.75rem 1.5rem;
                    background-color: #10b981;
                    color: white;
                    border: none;
                    border-radius: 0.375rem;
                    cursor: pointer;
                ">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showCreateUserModal() {
    document.getElementById('createUserModal').style.display = 'block';
}

function hideCreateUserModal() {
    document.getElementById('createUserModal').style.display = 'none';
    document.getElementById('createUserForm').reset();
}

document.getElementById('createUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('{{ route("admin.users.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (response.ok) {
            hideCreateUserModal();
            location.reload();
        } else {
            alert(result.message || 'Error creating user');
        }
    } catch (error) {
        alert('Error creating user');
    }
});

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(`/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.message) {
                location.reload();
            }
        })
        .catch(error => {
            alert('Error deleting user');
        });
    }
}

function editUser(userId) {
    // TODO: Implement edit user functionality
    alert('Edit functionality coming soon!');
}
</script>
@endsection