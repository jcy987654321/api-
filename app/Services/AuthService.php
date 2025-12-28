<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(string $email, string $password, bool $remember = false): ?User
    {
        $user = $this->validateCredentials($email, $password);

        if (! $user) {
            return null;
        }

        Auth::guard('admin')->login($user, $remember);

        return $user;
    }

    public function logout(): void
    {
        Auth::guard('admin')->logout();
    }

    public function validateCredentials(string $email, string $password): ?User
    {
        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            return null;
        }

        if (! $user->isActive() || ! $user->isAdmin()) {
            return null;
        }

        if (! Hash::check($password, (string) $user->password)) {
            return null;
        }

        return $user;
    }

    public function checkUser(int $id): bool
    {
        /** @var User|null $user */
        $user = User::query()->find($id);

        return (bool) $user && $user->isAdmin() && $user->isActive();
    }
}
