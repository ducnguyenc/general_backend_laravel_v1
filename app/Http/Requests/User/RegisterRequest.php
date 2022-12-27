<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

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
            'name' => 'bail|required|max:255',
            'email' => ['bail', 'required', 'email', 'max:255', function ($attribute, $value, $fail) {
                $isUser = User::where($attribute, $value)->whereNotNull('email_verified_at')->exists();
                if ($isUser) {
                    $fail('The selected ' . $attribute . ' is invalid.');
                }
            }],
            'password' => 'bail|required|min:8|max:255|confirmed',
        ];
    }
}
