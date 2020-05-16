<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketStoreRequest extends FormRequest
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
            'attributes' => 'array|required',
            'attributes.event_id' => 'integer|exists:events,id|required',
            'attributes.ticket' => 'array|required_with:attributes',
            'attributes.ticket.number' => 'integer|required',
            'attributes.ticket.price' => 'integer|required',
            'attributes.ticket_translation_data' => 'array|required_with:attributes',
            'attributes.ticket_translation_data.description' => 'string|required',
            'attributes.ticket_translation_data.locale' => 'string|required',
            'attributes.ticket_translation_data.location' => 'string|required',
            'attributes.ticket_translation_data.type' => 'string|required',
        ];
    }
}
