<?php

namespace App\Services;

use GuzzleHttp\Client;
use Spatie\Geocoder\Geocoder;

class GeocoderService
{
    public function getCoordinatesForAddress(string $address): array
    {
        $client = new Client();

        $geocoder = new Geocoder($client);

        $geocoder->setApiKey(config('geocoder.key'));

        $geocoder->setCountry(config('geocoder.country', 'US'));

        return $geocoder->getCoordinatesForAddress($address);
    }
}
