<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistributorRequest extends FormRequest
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
            'name' => 'required|string|unique:contacts,name',
            'address' => 'nullable|string',
            'city' => 'nullable|string' ,
            'district' => 'nullable|string',
            'state_id' => 'required|numeric|exists:states,id',
            'phone' => 'nullable|string',
            'email' => 'required|email|unique:distributors,email',
            'pincode' => 'nullable|string|size:6',
            'user_id' => 'required|numeric|exists:users,id'
        ];
    }
}
