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
            'additionel_information' => 'string||required',
            'date' => 'date|required',
            'picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|required',
            'publish_at' => 'date|required',
            'subtitle' => 'string|max:255|required',
            'title' => 'string|unique:events|max:255|required',
        ];
    }
}
