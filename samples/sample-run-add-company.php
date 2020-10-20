<?php
require dirname(__DIR__) . '/vendor/autoload.php';

$apiKey = $argv[1];
$apiSecret = $argv[2];
$businessName = $argv[3];

$ivvy = (new Ivvy\IvvyFactory)->newInstance($apiKey, $apiSecret);

$jobFactory = new Ivvy\JobFactory(
    new Ivvy\Model\Validator\AddCompanyValidator,
    new Ivvy\Model\Validator\UpdateCompanyValidator,
    new Ivvy\Model\Validator\AddContactValidator,
    new Ivvy\Model\Validator\UpdateContactValidator
);

// NOTE: there's a bug where you can't make it work unless AU and QLD
$address = new Ivvy\Model\Address([
    'stateCode' => 'QLD',
    'countryCode' => 'AU',
    'postalCode' => '4227',
]);
$company = new Ivvy\Model\Company(compact('businessName', 'address'));

$asyncId = $ivvy->run([
    $jobFactory->newAddCompanyJob($company),
]);

if ($asyncId) {
    echo "Async ID: {$asyncId}\n";
} else {
    echo "Couldn't connect to the API server. Check iVvy's credentials\n";
    exit(1);
}
