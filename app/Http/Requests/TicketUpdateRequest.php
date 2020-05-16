<?php

namespace App\Http\Requests;

use App\Event;
use App\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class TicketUpdateRequest extends FormRequest
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
            'attributes.event_id' => 'integer|exists:events,id|required_with:attributes',
            'attributes.ticket' => 'array',
            'attributes.ticket.number' => 'integer',
            'attributes.ticket.price' => 'integer',
            'attributes.ticket_translation_data' => 'array',
            'attributes.ticket_translation_data.description' => 'string',
            'attributes.ticket_translation_data.locale' => 'string|required_with:attributes.ticket_translation_data',
            'attributes.ticket_translation_data.location' => 'string',
            'attributes.ticket_translation_data.type' => 'string',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('attributes.event_id')) {
                $ticket = $this->route('ticket');
                $event = Event::find($this->input('attributes.event_id'));

                if ($ticket->event()->exists()) {
                    if ($ticket->event->id != $event->id) {
                        $validator->errors()->add('field', trans('The ticket is not related to the event provided.'));
                    }
                }
            }
        });
    }
}
