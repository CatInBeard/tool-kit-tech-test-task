<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionaryCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:questionaries,email',
            'password' => 'required|string|min:6',
            'catPhoto' => 'nullable|file|mimes:jpg,jpeg,png|max:8192',
            'phone' => 'nullable|string|max:20',
            'tg_name' => 'nullable|string|max:255',
        ];
    }
}

