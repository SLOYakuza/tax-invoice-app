<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->is_active;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'required|string|size:2',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Ime klijenta je obvezno.',
            'name.string' => 'Ime mora biti besedilo.',
            'name.max' => 'Ime ne sme presegati 255 znakov.',
            'email.email' => 'E-pošta mora biti veljavna e-poštna naslov.',
            'country.required' => 'Država je obvezna.',
            'country.size' => 'Država mora biti dvočrkovna koda.',
        ];
    }
}
