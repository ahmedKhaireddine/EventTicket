<?php

namespace App\Traits;

trait CheckFranceAddressTrait
{
    /**
     * Check the address exists in French.
     *
     * @param  array  $attributes
     * @return bool
     */
    public function checkAddress(array $attributes)
    {
        if (isset($attributes['street_address'], $attributes['postal_code'])) {
            $postCode = $attributes['postal_code'];
            $q = urlencode($attributes['street_address']);

            $dataAddress = json_decode(file_get_contents("https://api-adresse.data.gouv.fr/search/?limit=1&postCode={$postCode}&q={$q}"), true);

            $venueAddress = $dataAddress['features'][0]['properties'];

            if (ucwords($venueAddress['name']) === ucwords($attributes['street_address']) &&
                ucwords($venueAddress['postcode']) === ucwords($attributes['postal_code']) &&
                ucwords($venueAddress['city']) === ucwords($attributes['city'])) {
                return true;
            }
        }

        return false;
    }

}