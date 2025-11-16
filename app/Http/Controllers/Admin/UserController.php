<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AdminUserCreated;
use App\Notifications\PasswordResetEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('is_admin', true)
            ->orWhereHas('roles')
            ->with('roles')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
            'is_admin' => $validated['is_admin'] ?? false,
        ]);

        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        $user->notify(new AdminUserCreated($user));

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $this->authorize('viewAny', User::class);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $validated = $request->validated();

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'] ?? $user->status,
        ]);

        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function sendPasswordReset(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $resetToken = Str::random(64);
        $user->update([
            'password_changed_at' => now()->timestamp,
        ]);

        $user->notify(new PasswordResetEmail($resetToken));

        return redirect()
            ->back()
            ->with('success', 'Password reset email sent successfully.');
    }

    public function unlock(User $user)
    {
        $this->authorize('update', $user);

        $user->unlockAccount();

        return redirect()
            ->back()
            ->with('success', 'Account unlocked successfully.');
    }

    public function getLoginHistory(User $user)
    {
        $this->authorize('view', $user);

        $history = $user->loginLogs()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.users.login-history', compact('user', 'history'));
    }
}
