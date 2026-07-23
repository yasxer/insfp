<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentProfileCompleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'date_of_birth' => [
                'required',
                'date',
                'before:today',
                'after:1950-01-01'
            ],
            'address' => [
                'required',
                'string',
                'min:10',
                'max:500'
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^0[5-7][0-9]{8}$/',
                Rule::unique('users')->ignore($user->id)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'date_of_birth.required' => 'Date de naissance requise',
            'date_of_birth.date' => 'Date invalide',
            'date_of_birth.before' => 'La date de naissance doit être avant aujourd\'hui',
            'date_of_birth.after' => 'Date de naissance trop ancienne',
            'address.required' => 'Adresse requise',
            'address.min' => 'Adresse doit contenir au moins 10 caractères',
            'address.max' => 'Adresse ne doit pas dépasser 500 caractères',
            'phone.regex' => 'Format téléphone invalide (ex: 0612345678)',
            'phone.unique' => 'Téléphone déjà utilisé',
        ];
    }
}
