<?php

namespace App\Actions;

use App\Ticket;
use App\Traits\GoogleTranslateTrait;

class StoreTicketAction
{
    use GoogleTranslateTrait;

    /**
     * @param  array  $attributes
     * @return \App\Ticket
     */
    public function execute(array $attributes): Ticket
    {
        $ticket = Ticket::create([
            'event_id' => $attributes['event_id'],
            'price' => $attributes['ticket']['price'],
            'tickets_number' => $attributes['ticket']['number'],
            'tickets_remain' => $attributes['ticket']['number'],
        ]);

        $this->storeTicketTranslation($ticket, $attributes['ticket_translation_data']);

        return $ticket;
    }

    /**
     * @param  \App\Ticket  $ticket
     * @param  array  $attributes
     * @return \App\Ticket
     */
    private function storeTicketTranslation(Ticket $ticket, array $attributes): Ticket
    {
        $translations = $ticket->translations;

        if ($translations->where('locale', 'en')->isEmpty()) {
            $attributes = $this->translateIntoAnotherLanguage($attributes['locale'], 'en', $attributes);

            $ticket->translations()->create($attributes);
        }

        if ($translations->where('locale', 'fr')->isEmpty()) {
            $attributes = $this->translateIntoAnotherLanguage($attributes['locale'], 'fr', $attributes);

            $ticket->translations()->create($attributes);
        }

        return $ticket->refresh();
    }

    /**
     * @param  string  $source
     * @param  string  $target
     * @param  array  $attributes
     * @return array
     */
    private function translateIntoAnotherLanguage(string $source, string $target, array $attributes): array
    {
        if (isset($attributes['description'])) {
            $attributes['description'] = $this->translate($source, $target, $attributes['description']);
        }

        if (isset($attributes['locale'])) {
            $attributes['locale'] = $target;
        }

        if (isset($attributes['location'])) {
            $attributes['location'] = $this->translate($source, $target, $attributes['location']);
        }

        if (isset($attributes['type'])) {
            $attributes['type'] = $this->translate($source, $target, $attributes['type']);
        }

        return $attributes;
    }
}