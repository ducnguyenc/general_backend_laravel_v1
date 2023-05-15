<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => ['bail', 'required', 'email', 'max:255', function ($attribute, $value, $fail) {
                $isUser = User::where($attribute, $value)->whereIn('role', User::ARR_ROLE_ADMIN)->whereNotNull('email_verified_at')->exists();
                if ($isUser) {
                    $fail(trans('validation.unique', ['attribute' => $attribute]));
                }
            }],
            'password' => 'bail|required|min:8|max:255|confirmed',
        ];
    }
}
