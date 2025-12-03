<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacilityRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'building_id' => 'required|integer|exists:buildings,id',
            'name'        => 'required|string|max:255',
            'type'        => 'required|string',
            'capacity'    => 'required|integer|min:1',
            'status'      => 'required|string|in:Available,Maintenance,Closed',
            'start_time'  => 'required|date_format:H:i|after_or_equal:08:00|before:22:00',
            'end_time'    => 'required|date_format:H:i|after:start_time|before_or_equal:22:00',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}