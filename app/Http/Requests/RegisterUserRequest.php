<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'name' => 'required|string|min:2|max:60',
            'email' => 'required|email:rfc,dns|min:6|max:255',
            'phone' => 'required|string|min:13|max:13|starts_with:+380',
            'position_id' => 'required|numeric|exists:positions,id',
            'photo' => 'required|image|mimes:jpeg,jpg|max:5120|dimensions:min_width=70,min_height=70',
        ];
    }

    public function messages()
    {
        return [
            'photo.max' => 'The photo may not be greater than 5 Mbytes.',
            'photo.dimensions' => 'The photo must be 70x70 pixels.',
        ];
    }
}
