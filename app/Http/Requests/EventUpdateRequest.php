<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'additionel_information' => 'string',
            'end_date' => 'date_format:Y-m-d',
            'event_program' =>'array',
            'picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'publish_at' => 'date_format:Y-m-d',
            'start_date' => 'date_format:Y-m-d',
            'start_time' => 'date_format:H:i',
            'subtitle' => 'string|max:255',
            'title' => 'string|unique:events|max:255',
        ];
    }
}
