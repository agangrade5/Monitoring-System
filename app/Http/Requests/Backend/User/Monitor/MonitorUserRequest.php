<?php

namespace App\Http\Requests\Backend\User\Monitor;
use App\Rules\{NoScripts, ValidEmailDomain, ValidUrl, ValidMobile};
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MonitorUserRequest extends FormRequest
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

            'mobile' => [
                'nullable',
                'string',
                'max:15',
                'required',
                new ValidMobile(),
            ],

            'url' => [
                'nullable',
                'string',
                'max:255',
                new ValidUrl(),
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}

