<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^0[5-7][0-9]{8}$/',
                Rule::unique('users')->ignore($user->id)
            ],
            'date_of_birth' => [
                'sometimes',
                'nullable',
                'date',
                'before:today',
                'after:1950-01-01'
            ],
            'address' => [
                'sometimes',
                'nullable',
                'string',
                'min:10',
                'max:500'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Format téléphone invalide (ex: 0612345678)',
            'phone.unique' => 'Téléphone déjà utilisé',
            'date_of_birth.date' => 'Date invalide',
            'date_of_birth.before' => 'Date de naissance doit être avant aujourd\'hui',
            'date_of_birth.after' => 'Date de naissance trop ancienne',
            'address.min' => 'Adresse doit contenir au moins 10 caractères',
            'address.max' => 'Adresse ne doit pas dépasser 500 caractères',
        ];
    }
}
