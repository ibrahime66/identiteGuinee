<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_type' => ['required', 'in:cni,passeport,permis'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
            'birth_place' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[+]?[0-9\s\-\(\)]+$/'],
            'priority' => ['nullable', 'in:normal,urgent'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'document_type.required' => 'Le type de document est obligatoire.',
            'document_type.in' => 'Le type de document sélectionné n\'est pas valide.',
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required' => 'Le nom est obligatoire.',
            'birth_date.required' => 'La date de naissance est obligatoire.',
            'birth_date.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'birth_place.required' => 'Le lieu de naissance est obligatoire.',
            'address.required' => 'L\'adresse est obligatoire.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.regex' => 'Le format du numéro de téléphone n\'est pas valide.',
        ];
    }

    public function getDocumentTypeLabel(): string
    {
        return match($this->document_type) {
            'cni' => 'Carte Nationale d\'Identité',
            'passeport' => 'Passeport',
            'permis' => 'Permis de conduire',
            default => $this->document_type,
        };
    }
}
