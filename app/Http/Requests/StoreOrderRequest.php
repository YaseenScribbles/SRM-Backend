<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'contact_id' => 'required|exists:contacts,id',
            'remarks' => 'nullable|string',
            'user_id' => 'required|exists:users,id',

            //order_items

            'order_items' => 'required|array',
            'order_items.*.size_id' => 'required|exists:brands,size_id',
            'order_items.*.qty' => 'required|numeric',
        ];
    }
}
