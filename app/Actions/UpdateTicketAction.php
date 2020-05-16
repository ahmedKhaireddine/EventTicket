<?php

namespace App\Actions;

use App\Ticket;
use App\Traits\GoogleTranslateTrait;

class UpdateTicketAction
{
    use GoogleTranslateTrait;

    /**
     * @param  \App\Ticket  $ticket
     * @param  array  $attributes
     * @return \App\Ticket
     */
    public function execute(Ticket $ticket, array $attributes): Ticket
    {
        if (isset($attributes['ticket'])) {
            if (isset($attributes['ticket']['number'])) {
                $attributes['ticket']['tickets_number'] = $attributes['ticket']['number'];
                $attributes['ticket']['tickets_remain'] = $attributes['ticket']['number'];
                unset($attributes['ticket']['number']);
            }

            $ticket->fill($attributes['ticket']);

            $ticket->save();
        }

        if (isset($attributes['ticket_translation_data'])) {
            $this->storeTicketTranslation($ticket, $attributes['ticket_translation_data']);
        }

        return $ticket;
    }

    /**
     * @param  \App\Ticket  $ticket
     * @param  array  $attributes
     * @return \App\Ticket
     */
    private function storeTicketTranslation(Ticket $ticket, array $attributes): Ticket
    {
        $ticket->translations->map(function ($translation) use ($attributes) {
            if ($attributes['locale'] != $translation->locale) {
                $attributes = $this->translateIntoAnotherLanguage(
                    $attributes['locale'], $translation->locale, $attributes
                );
            }

            $translation->fill($attributes);

            $translation->save();
        });

        return $ticket;
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