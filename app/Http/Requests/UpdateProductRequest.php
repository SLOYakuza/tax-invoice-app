<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->route('product')->authorizeToUpdate($this->user());
        return true;
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
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0|max:999999',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'unit' => 'required|string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Naziv proizvoda je obvezen.',
            'name.string' => 'Naziv mora biti besedilo.',
            'name.max' => 'Naziv ne sme presegati 255 znakov.',
            'price.required' => 'Cena je obvezna.',
            'price.numeric' => 'Cena mora biti številka.',
            'price.min' => 'Cena ne sme biti negativna.',
            'price.max' => 'Cena je previsoka.',
            'tax_rate.required' => 'Stopnja PDV je obvezna.',
            'tax_rate.numeric' => 'Stopnja PDV mora biti številka.',
            'tax_rate.max' => 'Stopnja PDV ne sme presegati 100%.',
            'unit.required' => 'Enota je obvezna.',
        ];
    }
}
