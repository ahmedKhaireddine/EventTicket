<?php

namespace App\Http\Requests;

use App\Event;
use Illuminate\Foundation\Http\FormRequest;

class AddressUpdateRequest extends FormRequest
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
            'address' => 'array',
            'address.additionel_information' => 'string',
            'address.city' => 'string',
            'address.country' => 'string',
            'address.postal_code' => 'string',
            'address.street_address' => 'string',
            'address.venue' => 'string',
            'address_id' => 'integer|exists:addresses,id|required',
            'event_id' => 'integer|exists:events,id|required',
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
            if ($this->has('event_id') && $this->has('address_id')) {
                $event = Event::find($this->event_id);

                if ($event->address()->exists()) {
                    if ($event->address->id != $this->address_id) {
                        $validator->errors()->add('field', trans('The address is not related to the event provided.'));
                    }
                }
            }
        });
    }
}
