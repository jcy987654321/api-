<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\LoginHistoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly LoginHistoryService $loginHistoryService
    ) {
    }

    public function show(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('admin')->user();
        $loginHistory = $this->loginHistoryService->getLoginHistory($user->id, 10);

        return view('admin.profile', compact('user', 'loginHistory'));
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('admin')->user();

        $data = $request->only(['name', 'email']);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return redirect()
            ->route('admin.profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function changePassword(): View
    {
        return view('admin.change-password');
    }

    public function updatePassword(ChangePasswordRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('admin')->user();

        if (! Hash::check((string) $request->input('current_password'), (string) $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make((string) $request->input('password')),
        ]);

        return redirect()
            ->route('admin.profile')
            ->with('success', 'Password changed successfully.');
    }
}
