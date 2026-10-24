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
            'number' => ['required_without:token', 'integer', 'min:1'],
            'token' => ['required_without:number', 'string', 'alpha_num', 'size:10'],
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
            'number.required_without' => 'Nenurodytas užsakymo numeris.',
            'number.*' => 'Neteisingas užsakymo numeris.',
            'token.required_without' => 'Nenurodytas užsakymo numeris.',
            'token.*' => 'Neteisinga užsakymo nuoroda.',
            'endpoint.*' => 'Naršyklė neatsiuntė pranešimų adreso.',
            'keys.p256dh.*' => 'Naršyklė neatsiuntė pranešimų rakto.',
            'keys.auth.*' => 'Naršyklė neatsiuntė pranešimų rakto.',
        ];
    }
}
