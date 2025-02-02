<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return !is_null($user) && $user->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge(
            [
                'limit' => 'integer|min:1|max:500',
                'page' => 'integer|min:1',
            ],
            $this->getFilterRules()
        );
    }

    public function getFilterRules(): array
    {
        $rules = [];
        $filterFields = (new User())->getFilters();

        foreach ($filterFields as $field) {
            $rules[$field] = 'nullable|string';
        }

        return $rules;
    }
}
