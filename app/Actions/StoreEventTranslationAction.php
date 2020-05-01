<?php

namespace App\Actions;

use App\Event;
use App\EventTranslation;
use App\Traits\GoogleTranslateTrait;
use Illuminate\Support\Arr;

class StoreEventTranslationAction
{
    use GoogleTranslateTrait;

    /**
     * @param  \App\Event  $event
     * @param  array  $attributes
     * @return \App\Event
     */
    public function execute(Event $event, array $attributes): Event
    {
        $event->translations()->create($attributes);

        $translations = $event->translations;

        if ($translations->where('locale', 'en')->isEmpty()) {
            $attributes = $this->translateIntoAnotherLanguage('fr', 'en', $attributes);

            $event->translations()->create($attributes);
        }

        if ($translations->where('locale', 'fr')->isEmpty()) {
            $attributes = $this->translateIntoAnotherLanguage('en', 'fr', $attributes);

            $event->translations()->create($attributes);
        }

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
                $event_program = Arr::add($event_program, $key,$this->translate($source, $target, $value));
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