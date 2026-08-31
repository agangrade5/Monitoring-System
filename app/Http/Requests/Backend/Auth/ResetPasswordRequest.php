<?php

namespace App\Http\Requests\Backend\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use App\Rules\{NoScripts, AlphaSpacesRule, WithoutSpacesRule, StrictPasswordRule};

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token' => [
                'required',
            ],

            'email' => [
                'required',
                'email',
                new NoScripts('email'),
            ],

            'password' => [
                'required',
                'confirmed',
                new WithoutSpacesRule(),
                new StrictPasswordRule(),
            ],
        ];
    }
}
