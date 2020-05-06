<?php

namespace App\Actions;

use App\Event;
use App\EventTranslation;
use App\Traits\GoogleTranslateTrait;
use Illuminate\Support\Arr;

class UpdateEventTranslationAction
{
    use GoogleTranslateTrait;

    /**
     * @param  \App\Event  $event
     * @param  array  $attributes
     * @return \App\Event
     */
    public function execute(Event $event, array $attributes): Event
    {
        $event->translations->map(function ($translation) use ($attributes) {
            if ($attributes['locale'] != $translation->locale) {
                $attributes = $this->translateIntoAnotherLanguage(
                    $attributes['locale'], $translation->locale, $attributes
                );
            }

            $translation->fill($attributes);

            $translation->save();
        });

        return $event->refresh();
    }

    /**
     * Translate $attributes into another language.
     *
     * @param  string  $source
     * @param  string  $target
     * @param  array  $attributes
     * @return array
     */
    private function translateIntoAnotherLanguage(string $source, string $target, array $attributes): array
    {
        if (isset($attributes['additionel_information'])) {
            $attributes['additionel_information'] = $this->translate($source, $target, $attributes['additionel_information']);
        }

        if (isset($attributes['event_program'])) {
            $event_program = [];

            foreach ($attributes['event_program'] as $key => $value) {
                $event_program = Arr::add($event_program, $key, $this->translate($source, $target, $value));
            }

            $attributes['event_program'] = $event_program;
        }

        if (isset($attributes['locale'])) {
            $attributes['locale'] = $target;
        }

        if (isset($attributes['subtitle'])) {
            $attributes['subtitle'] = $this->translate($source, $target, $attributes['subtitle']);
        }

        if (isset($attributes['title'])) {
            $attributes['title'] = $this->translate($source, $target, $attributes['title']);
        }

        return $attributes;
    }
}