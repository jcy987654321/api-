<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    /**
     * Show the user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        return view('pages.profile.index', compact('user'));
    }

    /**
     * Show the edit profile form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('pages.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '用户资料更新成功',
                'data' => $user->fresh(),
            ]);
        }

        return redirect()->route('profile')
            ->with('success', '用户资料更新成功');
    }

    /**
     * Show the change password form.
     */
    public function showChangePassword()
    {
        return view('pages.profile.change-password');
    }

    /**
     * Change the user's password.
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'password.different' => '新密码不能与当前密码相同',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '当前密码不正确',
                    'errors' => [
                        'current_password' => ['当前密码不正确']
                    ]
                ], 422);
            }
            return back()->withErrors(['current_password' => '当前密码不正确']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '密码修改成功',
            ]);
        }

        return redirect()->route('profile')
            ->with('success', '密码修改成功');
    }

    /**
     * Show the upload avatar form.
     */
    public function showUploadAvatar()
    {
        $user = Auth::user();
        return view('pages.profile.upload-avatar', compact('user'));
    }

    /**
     * Upload the user's avatar.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = Auth::user();
        $avatarUrl = $user->updateAvatar($request->file('avatar'));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '头像上传成功',
                'data' => [
                    'avatar_url' => $avatarUrl,
                ],
            ]);
        }

        return redirect()->route('profile')
            ->with('success', '头像上传成功');
    }

    /**
     * Delete the user's avatar.
     */
    public function deleteAvatar(Request $request)
    {
        $user = Auth::user();
        $user->deleteAvatar();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '头像删除成功',
            ]);
        }

        return redirect()->route('profile')
            ->with('success', '头像删除成功');
    }
}
