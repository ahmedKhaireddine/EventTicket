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
            'event_id' => 'integer|exists:events,id|required',
            'ticket' => 'array',
            'ticket.number' => 'integer',
            'ticket.type' => 'string',
            'ticket.description' => 'string',
            'ticket.price' => 'integer',
            'ticket_id' => 'integer|exists:tickets,id|required'
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
            if ($this->has('event_id') && $this->has('ticket_id')) {
                $ticket = Ticket::find($this->ticket_id);
                $event = Event::find($this->event_id);

                if ($ticket->event()->exists()) {
                    if ($ticket->event->id != $event->id) {
                        $validator->errors()->add('field', trans('The ticket is not related to the event provided.'));
                    }
                }
            }
        });
    }
}
