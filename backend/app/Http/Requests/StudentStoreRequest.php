<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->method() === 'PUT' || $this->method() === 'PATCH';
        $studentId = $this->route('id');
        $userId = $isUpdate && $studentId ? $this->getUserIdForStudent($studentId) : null;

        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                $isUpdate && $userId
                    ? Rule::unique('users')->ignore($userId)
                    : 'unique:users,email'
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^0[5-7][0-9]{8}$/',
                $isUpdate && $userId
                    ? Rule::unique('users')->ignore($userId)
                    : 'unique:users,phone'
            ],
            'date_of_birth' => 'required|date|before:today|after:1950-01-01',
            'address' => 'required|string|min:10|max:500',
            'specialty_id' => 'required|exists:specialties,id',
            'registration_number' => $isUpdate
                ? 'sometimes|string'
                : 'required|unique:students,registration_number',
            'study_mode' => 'required|in:initial,alternance,continue',
            'current_semester' => 'required|integer|min:1|max:6',
            'group' => 'nullable|string|max:10',
            'years_enrolled' => 'integer|min:1',
            'password' => 'nullable|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Prénom requis',
            'first_name.max' => 'Prénom ne doit pas dépasser 100 caractères',
            'last_name.required' => 'Nom requis',
            'last_name.max' => 'Nom ne doit pas dépasser 100 caractères',
            'email.required' => 'Email requis',
            'email.email' => 'Format email invalide',
            'email.unique' => 'Email déjà utilisé',
            'phone.regex' => 'Format téléphone invalide (ex: 0612345678)',
            'phone.unique' => 'Téléphone déjà utilisé',
            'date_of_birth.required' => 'Date de naissance requise',
            'date_of_birth.date' => 'Date invalide',
            'date_of_birth.before' => 'La date de naissance doit être avant aujourd\'hui',
            'date_of_birth.after' => 'Date de naissance trop ancienne',
            'address.required' => 'Adresse requise',
            'address.min' => 'Adresse doit contenir au moins 10 caractères',
            'address.max' => 'Adresse ne doit pas dépasser 500 caractères',
            'specialty_id.required' => 'Spécialité requise',
            'specialty_id.exists' => 'Spécialité invalide',
            'registration_number.required' => 'Numéro d\'inscription requis',
            'registration_number.unique' => 'Numéro d\'inscription déjà utilisé',
            'study_mode.required' => 'Mode d\'étude requis',
            'study_mode.in' => 'Mode d\'étude invalide (initial, alternance ou continue)',
            'current_semester.required' => 'Semestre requis',
            'current_semester.integer' => 'Semestre doit être un nombre',
            'current_semester.min' => 'Semestre doit être entre 1 et 6',
            'current_semester.max' => 'Semestre doit être entre 1 et 6',
            'group.max' => 'Groupe ne doit pas dépasser 10 caractères',
            'years_enrolled.integer' => 'Années d\'inscription doit être un nombre',
            'years_enrolled.min' => 'Années d\'inscription doit être au moins 1',
            'password.min' => 'Mot de passe doit contenir au moins 8 caractères',
        ];
    }

    private function getUserIdForStudent($studentId)
    {
        if (!$studentId) return null;

        $student = \App\Models\Student::find($studentId);
        return $student?->user_id;
    }
}
