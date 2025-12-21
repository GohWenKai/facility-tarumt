<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $id = Auth::id(); // Get current user ID to ignore in unique check

        return [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'address' => 'required|string|max:2000',
            'tel' => ['required', 'string', 'max:15', 'regex:/^\+60\d{9,10}$/'],
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => [
                'nullable',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+=\[\]{}|\\:";\'<>,.\/~`-]).+$/'
            ],
        ];
    }

    public function messages()
    {
        return [
            'new_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one symbol (!@#$%).',
        ];
    }
}