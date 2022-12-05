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
            'name' => 'required|max:255',
            'email' => ['required', 'email', 'max:255', function ($attribute, $value, $fail) {
                $isUser = User::where($attribute, $value)->whereNotNull('email_verified_at')->exists();
                if ($isUser) {
                    $fail('The selected ' . $attribute . ' is invalid.');
                }
            }],
            'password' => ['required', 'confirmed', 'min:8', 'max:255', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(), ],
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse(
            ['status' => 'Client error', 'data' => $validator->errors(), 'message' => ''],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        throw new ValidationException($validator, $response);
    }
}
