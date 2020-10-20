<?php

namespace Ivvy;

use Ivvy\Ivvy;

final class IvvyFactory
{
    public function newInstance($apiKey, $apiSecret)
    {
        $signature = new Signature;

        $client = new \Guzzle\Http\Client([
            'base_uri' => Ivvy::BASE_URI,
            'timeout'  => 5.0,
        ]);

        return new Ivvy($apiKey, $apiSecret, $signature, $client);
    }
}
