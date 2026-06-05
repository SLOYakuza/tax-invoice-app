<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->route('invoice')->authorizeToUpdate($this->user());
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
            'client_id' => 'required|exists:clients,id|integer',
            'invoice_date' => 'required|date|date_format:Y-m-d',
            'due_date' => 'nullable|date|date_format:Y-m-d|after:invoice_date',
            'description' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01|max:999999',
            'items.*.unit_price' => 'required|numeric|min:0|max:999999',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'Izbrati moraš klijenta.',
            'client_id.exists' => 'Izbrani klijent ne obstaja.',
            'invoice_date.required' => 'Datum računa je obvezen.',
            'invoice_date.date' => 'Datum računa mora biti veljaven datum.',
            'due_date.after' => 'Rok plačila mora biti po datumu računa.',
            'items.required' => 'Dodati moraš vsaj eno stavko.',
            'items.min' => 'Dodati moraš vsaj eno stavko.',
            'items.*.description.required' => 'Opis stavke je obvezen.',
            'items.*.quantity.required' => 'Količina stavke je obvezna.',
            'items.*.quantity.min' => 'Količina mora biti večja od 0.',
            'items.*.unit_price.required' => 'Cena stavke je obvezna.',
            'items.*.unit_price.min' => 'Cena ne sme biti negativna.',
            'items.*.tax_rate.required' => 'Stopnja PDV je obvezna.',
            'items.*.tax_rate.max' => 'Stopnja PDV ne sme presegati 100%.',
        ];
    }
}
