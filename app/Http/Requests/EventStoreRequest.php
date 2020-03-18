<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventStoreRequest extends FormRequest
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
            'additionel_information' => 'string|required',
            'end_date' => 'date_format:Y-m-d|nullable',
            'event_program' =>'array|nullable',
            'picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|required',
            'publish_at' => 'date_format:Y-m-d|required',
            'start_date' => 'date_format:Y-m-d|required',
            'start_time' => 'date_format:H:i|nullable',
            'subtitle' => 'string|max:255|required',
            'title' => 'string|unique:events|max:255|required',
        ];
    }
}
