<?php

namespace App\Http\Requests\Backend\Monitor;

use App\Rules\{NoScripts, ValidEmailDomain, ValidUrl};
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('urls') && is_array($this->urls)) {
            $cleanUrls = array_values(array_filter(array_map(function ($url) {
                if ($url === null) {
                    return null;
                }
                $url = trim($url);
                if ($url !== '' && !preg_match('#^https?://#i', $url)) {
                    $url = 'https://' . $url;
                }
                return $url !== '' ? $url : null;
            }, $this->urls)));

            $this->merge(['urls' => $cleanUrls]);
        }

        if ($this->has('url') && is_string($this->url) && trim($this->url) !== '') {
            $url = trim($this->url);
            if (!preg_match('#^https?://#i', $url)) {
                $url = 'https://' . $url;
            }
            $this->merge(['url' => $url]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                new NoScripts(),
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:50',
                new NoScripts(),
                new ValidEmailDomain(),
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];

        if ($this->has('urls')) {
            $rules['urls'] = ['required', 'array', 'min:1'];
            $rules['urls.*'] = ['required', 'string', 'max:255', new ValidUrl()];
        } else {
            $rules['url'] = ['required', 'string', 'max:255', new ValidUrl()];
        }

        return $rules;
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'urls.required' => 'Please provide at least one website URL or domain.',
            'urls.min' => 'Please provide at least one website URL or domain.',
            'urls.*.required' => 'The website URL field cannot be empty.',
        ];
    }
}

