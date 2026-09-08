<?php

namespace App\Http\Requests\Backend\Setting;

use Illuminate\Foundation\Http\FormRequest;

class TwilioSettingRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'enable_twilio' => ['nullable', 'in:0,1'],
            'twilio_account_sid' => ['required_if:enable_twilio,1', 'nullable', 'string', 'max:255'],
            'twilio_auth_token' => ['required_if:enable_twilio,1', 'nullable', 'string', 'max:255'],
            'twilio_from_number' => ['required_if:enable_twilio,1', 'nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'enable_twilio' => 'Enable Twilio switch',
            'twilio_account_sid' => 'Twilio Account SID',
            'twilio_auth_token' => 'Twilio Auth Token',
            'twilio_from_number' => 'Twilio From Number',
        ];
    }
}
