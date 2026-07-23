<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeacherProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'name' => 'sometimes|string|min:3|max:100',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^0[5-7][0-9]{8}$/',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'specialty_id' => 'sometimes|nullable|exists:specialties,id',
            'can_teach_module_ids' => 'sometimes|array',
            'can_teach_module_ids.*' => 'exists:modules,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.min' => 'Nom doit contenir au moins 3 caractères',
            'name.max' => 'Nom ne doit pas dépasser 100 caractères',
            'email.email' => 'Format email invalide',
            'email.unique' => 'Email déjà utilisé',
            'phone.regex' => 'Format téléphone invalide (ex: 0612345678)',
            'phone.unique' => 'Téléphone déjà utilisé',
            'password.min' => 'Mot de passe doit contenir au moins 8 caractères',
            'password.confirmed' => 'Confirmation du mot de passe ne correspond pas',
            'specialty_id.exists' => 'Spécialité invalide',
            'can_teach_module_ids.*.exists' => 'Module invalide',
        ];
    }
}
