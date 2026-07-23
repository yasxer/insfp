<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModuleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->method() === 'PUT' || $this->method() === 'PATCH';
        $moduleId = $this->route('id') ?? $this->input('module_id');

        return [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                $isUpdate
                    ? Rule::unique('modules')->ignore($moduleId)
                    : 'unique:modules,code'
            ],
            'description' => 'nullable|string|max:1000',
            'specialty_id' => 'required|exists:specialties,id',
            'semester' => 'required|integer|min:1|max:6',
            'coefficient' => 'required|numeric|min:0.5|max:10',
            'hours_per_week' => 'required|integer|min:1|max:40',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nom du module requis',
            'name.max' => 'Nom ne doit pas dépasser 255 caractères',
            'code.required' => 'Code du module requis',
            'code.max' => 'Code ne doit pas dépasser 50 caractères',
            'code.unique' => 'Code module déjà utilisé',
            'description.max' => 'Description ne doit pas dépasser 1000 caractères',
            'specialty_id.required' => 'Spécialité requise',
            'specialty_id.exists' => 'Spécialité invalide',
            'semester.required' => 'Semestre requis',
            'semester.integer' => 'Semestre doit être un nombre',
            'semester.min' => 'Semestre doit être entre 1 et 6',
            'semester.max' => 'Semestre doit être entre 1 et 6',
            'coefficient.required' => 'Coefficient requis',
            'coefficient.numeric' => 'Coefficient doit être un nombre',
            'coefficient.min' => 'Coefficient doit être entre 0.5 et 10',
            'coefficient.max' => 'Coefficient doit être entre 0.5 et 10',
            'hours_per_week.required' => 'Heures par semaine requises',
            'hours_per_week.integer' => 'Heures par semaine doit être un nombre',
            'hours_per_week.min' => 'Heures par semaine doit être entre 1 et 40',
            'hours_per_week.max' => 'Heures par semaine doit être entre 1 et 40',
        ];
    }
}
