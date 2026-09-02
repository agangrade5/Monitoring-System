<?php

namespace App\Http\Requests\Backend\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\{NoScripts, ValidEmailDomain, ValidUrl, ValidMobile,WithoutSpacesRule,StrictPasswordRule};

class UserRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                new NoScripts(),
            ],

            'email' => [
                'nullable',
                'string',
                'email',
                'max:50',
                'required_without:mobile',
                new NoScripts(),
                new ValidEmailDomain(),
            ],

           'password' => [
                'required',
                'confirmed',
                new WithoutSpacesRule(),
                new StrictPasswordRule(),
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }
    
}
