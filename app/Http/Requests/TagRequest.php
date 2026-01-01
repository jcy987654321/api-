<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tagId = $this->route('id');
        
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:blog_tags,slug' . ($tagId ? ",{$tagId}" : ''),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '请输入标签名称',
            'name.max' => '标签名称不能超过255个字符',
            'slug.required' => '请输入标签别名',
            'slug.unique' => '该别名已被使用',
        ];
    }
}
