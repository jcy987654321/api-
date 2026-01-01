<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('id');
        
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:blog_categories,slug' . ($categoryId ? ",{$categoryId}" : ''),
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '请输入分类名称',
            'name.max' => '分类名称不能超过255个字符',
            'slug.required' => '请输入分类别名',
            'slug.unique' => '该别名已被使用',
        ];
    }
}
