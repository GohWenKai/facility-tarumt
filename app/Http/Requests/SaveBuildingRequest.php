<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveBuildingRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'name'     => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}