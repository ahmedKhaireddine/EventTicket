<?php

namespace App\Traits;

use \Statickidz\GoogleTranslate;

trait GoogleTranslateTrait
{
    /**
     * Translate text.
     *
     * @param  string  $source
     * @param  string  $target
     * @param  string  $text
     * @return string
     */
    public function translate(string $source, string $target, string $text)
    {
        $trans = new GoogleTranslate();

        return $trans->translate($source, $target, $text);
    }
}