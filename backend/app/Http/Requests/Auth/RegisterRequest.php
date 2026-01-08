<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'registration_number' => 'required|string|exists:registration_numbers,number',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|string|regex:/^0[5-7][0-9]{8}$/|unique:users,phone',
            'specialty_id' => 'required|exists:specialties,id',
            'session_id' => 'required|exists:sessions,id',
            'email' => 'required|email|unique:users,email',
            'study_mode' => 'required|in:initial,alternance,continue',
            'password' => ['required', 'string', 'confirmed', Password::min(6)],
        ];
    }

    public function messages(): array
    {
        return [
            'registration_number.required' => 'Numéro d\'inscription requis',
            'registration_number.exists' => 'Numéro d\'inscription invalide',
            'first_name.required' => 'Prénom requis',
            'last_name.required' => 'Nom requis',
            'phone.regex' => 'Format invalide (ex: 0612345678)',
            'phone.unique' => 'Téléphone déjà utilisé',
            'specialty_id.required' => 'Spécialité requise',
            'specialty_id.exists' => 'Spécialité invalide',
            'email.required' => 'Email requis',
            'email.unique' => 'Email déjà utilisé',
            'study_mode.required' => 'Mode d\'étude requis',
            'study_mode.in' => 'Mode d\'étude invalide',
            'password.confirmed' => 'Confirmation ne correspond pas',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->registration_number) {
                $regNum = \App\Models\RegistrationNumber::where('number', $this->registration_number)->first();
                if ($regNum && $regNum->is_used) {
                    $validator->errors()->add('registration_number', 'Ce numéro est déjà utilisé.');
                }
            }
        });
    }
}
