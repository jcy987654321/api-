@extends('layouts.app')

@section('title', '登录 - Powerful Management System')

@section('content')
<div style="max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
    <h2 style="text-align: center;">用户登录</h2>

    @if (session('success'))
        <div style="background: #efe; border: 1px solid #cfc; color: #060; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background: #fee; border: 1px solid #fcc; color: #a00; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div style="margin-bottom: 15px;">
            <label for="email" style="display: block; margin-bottom: 5px;">邮箱</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="password" style="display: block; margin-bottom: 5px;">密码</label>
            <input type="password" name="password" id="password" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 20px; display: flex; align-items: center;">
            <input type="checkbox" name="remember" id="remember" style="margin-right: 10px;">
            <label for="remember">记住我</label>
        </div>

        <button type="submit" style="width: 100%; padding: 10px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer;">登录</button>
    </form>

    <div style="margin-top: 15px; text-align: center;">
        还没有账号？ <a href="{{ route('register') }}">立即注册</a>
    </div>
</div>
@endsection
