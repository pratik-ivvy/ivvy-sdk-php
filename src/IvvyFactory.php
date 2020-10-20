<?php



namespace Ivvy;

use Ivvy\Ivvy;

final class IvvyFactory
{
    public function newInstance(string $apiKey, string $apiSecret)
    {
        $signature = new Signature;

        $client = new \GuzzleHttp\Client([
            'base_uri' => Ivvy::BASE_URI,
            'timeout'  => 5.0,
        ]);

        return new Ivvy($apiKey, $apiSecret, $signature, $client);
    }
}
