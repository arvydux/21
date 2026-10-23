<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePushSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'number' => ['required', 'integer', 'min:1'],
            'endpoint' => ['required', 'url:https', 'max:500'],
            'keys.p256dh' => ['required', 'string', 'max:255'],
            'keys.auth' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'number.required' => 'Nenurodytas užsakymo numeris.',
            'number.integer' => 'Neteisingas užsakymo numeris.',
            'endpoint.*' => 'Naršyklė neatsiuntė pranešimų adreso.',
            'keys.p256dh.*' => 'Naršyklė neatsiuntė pranešimų rakto.',
            'keys.auth.*' => 'Naršyklė neatsiuntė pranešimų rakto.',
        ];
    }
}
