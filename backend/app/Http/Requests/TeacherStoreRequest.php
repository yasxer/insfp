<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeacherStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->method() === 'PUT' || $this->method() === 'PATCH';
        $teacherId = $this->route('id');
        $userId = $isUpdate && $teacherId ? $this->getUserIdForTeacher($teacherId) : null;

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
            'password' => $isUpdate
                ? 'nullable|string|min:8'
                : 'required|string|min:8',
            'specialization' => 'required|string|max:255',
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
            'password.required' => 'Mot de passe requis',
            'password.min' => 'Mot de passe doit contenir au moins 8 caractères',
            'specialization.required' => 'Spécialisation requise',
            'specialization.max' => 'Spécialisation ne doit pas dépasser 255 caractères',
        ];
    }

    private function getUserIdForTeacher($teacherId)
    {
        if (!$teacherId) return null;

        $teacher = \App\Models\Teacher::find($teacherId);
        return $teacher?->user_id;
    }
}
