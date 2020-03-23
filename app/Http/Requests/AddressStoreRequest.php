<?php

namespace App\Http\Requests;

use App\Event;
use Illuminate\Foundation\Http\FormRequest;

class AddressStoreRequest extends FormRequest
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
            'address' => 'array|required',
            'address.additionel_information' => 'string|nullable',
            'address.city' => 'string|required_with:address',
            'address.country' => 'string|required_with:address',
            'address.postal_code' => 'string|required_with:address',
            'address.street_address' => 'string|required_with:address',
            'address.venue' => 'string|nullable',
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
            if ($this->has('event_id')) {
                $event = Event::find($this->event_id);

                if ($event->address()->exists()) {
                    $validator->errors()->add('field', 'This event already has a relationship with another address.');
                }
            }
        });
    }
}
