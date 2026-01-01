<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $blogId = $this->route('id');
        
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:blogs,slug' . ($blogId ? ",{$blogId}" : ''),
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'cover_image' => 'nullable|string|max:500',
            'category_id' => 'required|exists:blog_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:blog_tags,id',
            'status' => 'required|in:draft,published',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '请输入文章标题',
            'title.max' => '标题不能超过255个字符',
            'slug.required' => '请输入文章别名',
            'slug.unique' => '该别名已被使用',
            'content.required' => '请输入文章内容',
            'category_id.required' => '请选择分类',
            'category_id.exists' => '所选分类不存在',
            'tags.array' => '标签格式不正确',
            'tags.*.exists' => '所选标签不存在',
            'status.required' => '请选择状态',
            'status.in' => '状态只能是草稿或发布',
        ];
    }
}
