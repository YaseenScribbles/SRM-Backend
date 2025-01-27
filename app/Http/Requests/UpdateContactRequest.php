<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:contacts,name,' . $this->route('contact.id'),
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'district' => 'nullable|string',
            'state_id' => 'required|numeric|exists:states,id',
            'phone' => 'nullable|string',
            'pincode' => 'nullable|string|size:6',
            'active' => 'nullable|boolean',
            'user_id' => 'required|numeric|exists:users,id',
            'email' => 'nullable|email',
            'distributor_id' => 'nullable|exists:distributors,id'
        ];
    }
}
