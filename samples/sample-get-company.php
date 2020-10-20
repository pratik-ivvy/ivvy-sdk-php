<?php
require dirname(__DIR__) . '/vendor/autoload.php';

$apiKey = $argv[1];
$apiSecret = $argv[2];
$id = $argv[3];

$ivvy = (new Ivvy\IvvyFactory)->newInstance($apiKey, $apiSecret);

$company = $ivvy->getCompany($id);

if ($company) {
    echo "{$company->businessName}\n";
} else {
    echo "Couldn't connect to the API server. Check iVvy's credentials\n";
    exit(1);
}
