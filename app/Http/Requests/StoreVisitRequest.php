<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitRequest extends FormRequest
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
            'contact_id' => 'required|numeric|exists:contacts,id',
            'purpose_id' => 'required|numeric|exists:purposes,id',
            'description' => 'nullable|string',
            'response' => 'nullable|string',
            'user_id' => 'required|numeric|exists:users,id',

            //images
            'visit_images' => 'required|array',
            'visit_images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
}
