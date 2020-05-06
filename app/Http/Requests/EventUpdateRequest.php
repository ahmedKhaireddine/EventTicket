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
            'attributes' => 'array',
            'attributes.event' => 'array',
            'attributes.event.end_date' => 'date_format:Y-m-d|nullable',
            'attributes.event.picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'attributes.event.start_date' => 'date_format:Y-m-d',
            'attributes.event.start_time' => 'date_format:H:i|nullable',
            'attributes.event_translate_data' => 'array',
            'attributes.event_translate_data.additionel_information' => 'string',
            'attributes.event_translate_data.event_program' => ' array|nullable',
            'attributes.event_translate_data.locale' => 'string',
            'attributes.event_translate_data.subtitle' => 'string|max:255',
            'attributes.event_translate_data.title' => 'string|max:255',
        ];
    }
}
