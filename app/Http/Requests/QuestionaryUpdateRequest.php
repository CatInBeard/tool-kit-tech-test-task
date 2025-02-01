<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionaryUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->route('user');

        return [
            'name' => 'string|max:255',
            'email' => 'email|unique:questionaries,email,' . $userId,
            'password' => 'string|min:6',
            'catPhoto' => 'nullable|file|mimes:jpg,jpeg,png|max:8192',
            'phone' => 'nullable|string|max:20',
            'tg_name' => 'nullable|string|max:255',
        ];
    }
}

