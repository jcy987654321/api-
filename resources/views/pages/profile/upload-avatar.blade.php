@extends('layouts.app')

@section('title', '更换头像 - Powerful Management System')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 text-start">
                    <h5 class="mb-0 fw-bold text-dark">更换头像</h5>
                </div>
                <div class="card-body py-5">
                    <div class="mb-4">
                        <p class="text-muted mb-3">当前头像</p>
                        <img src="{{ $user->getAvatarUrl() }}" 
                             alt="{{ $user->name }}" 
                             class="rounded-circle shadow-sm border"
                             style="width: 150px; height: 150px; object-fit: cover;"
                             id="avatar-preview">
                    </div>

                    <form action="{{ route('user.profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="avatar" class="btn btn-outline-primary px-4">
                                选择新图片
                                <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </label>
                            @error('avatar')
                                <div class="text-danger mt-2 small">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-2">支持 JPG, PNG, GIF，最大 2MB。</div>
                        </div>

                        <div id="upload-actions" class="d-none">
                            <button type="submit" class="btn btn-primary px-5">上传并保存</button>
                            <button type="button" class="btn btn-link text-secondary" onclick="resetPreview()">取消</button>
                        </div>
                    </form>

                    @if($user->avatar)
                        <hr class="my-4">
                        <form action="{{ route('user.profile.avatar.destroy') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger text-decoration-none small" onclick="return confirm('确定要删除当前头像并使用默认头像吗？')">
                                删除当前头像
                            </button>
                        </form>
                    @endif
                </div>
                <div class="card-footer bg-light py-3 text-start">
                    <a href="{{ route('profile') }}" class="btn btn-link text-decoration-none text-secondary p-0">返回个人资料</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
                document.getElementById('upload-actions').classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resetPreview() {
        document.getElementById('avatar-preview').src = "{{ $user->getAvatarUrl() }}";
        document.getElementById('avatar').value = "";
        document.getElementById('upload-actions').classList.add('d-none');
    }
</script>
@endsection
