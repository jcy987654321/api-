@extends('layouts.app')

@section('title', '个人资料 - Powerful Management System')

@section('content')
<div style="max-width: 800px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
    <h2 style="margin-bottom: 20px;">个人资料</h2>

    @if (session('success'))
        <div style="background: #efe; border: 1px solid #cfc; color: #060; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; gap: 30px; align-items: start;">
        <div style="flex-shrink: 0;">
            <div style="width: 150px; height: 150px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid #ddd;">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <span style="font-size: 60px; color: #aaa;">{{ substr($user->name, 0, 1) }}</span>
                @endif
            </div>
        </div>

        <div style="flex-grow: 1;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px 0; font-weight: bold; width: 100px;">姓名</td>
                    <td style="padding: 10px 0;">{{ $user->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; font-weight: bold;">邮箱</td>
                    <td style="padding: 10px 0;">{{ $user->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; font-weight: bold;">手机号</td>
                    <td style="padding: 10px 0;">{{ $user->phone ?? '未设置' }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; font-weight: bold;">状态</td>
                    <td style="padding: 10px 0;">
                        <span style="padding: 2px 8px; border-radius: 12px; font-size: 12px; {{ $user->status === 'active' ? 'background: #e6f7ed; color: #1f7347;' : 'background: #fdf2f2; color: #9b1c1c;' }}">
                            {{ $user->status === 'active' ? '正常' : '禁用' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; font-weight: bold;">注册时间</td>
                    <td style="padding: 10px 0;">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
