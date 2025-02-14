<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $this->route('user.id'),
            'password' => 'nullable|confirmed',
            'role' => 'required',
            'agent' => 'nullable',
            'manager_id' => 'nullable',
            'state_id' => 'nullable',
            'phone' => 'nullable',
            'active' => 'nullable|boolean',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
