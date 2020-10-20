<?php
require dirname(__DIR__) . '/vendor/autoload.php';

$apiKey = $argv[1];
$apiSecret = $argv[2];
$id = $argv[3];
$email = $argv[4];
$firstName = $argv[5];

$ivvy = (new Ivvy\IvvyFactory)->newInstance($apiKey, $apiSecret);

$jobFactory = new Ivvy\JobFactory(
    new Ivvy\Model\Validator\AddCompanyValidator,
    new Ivvy\Model\Validator\UpdateCompanyValidator,
    new Ivvy\Model\Validator\AddContactValidator,
    new Ivvy\Model\Validator\UpdateContactValidator
);

$contact = new Ivvy\Model\Contact(compact('id', 'email', 'firstName'));

$asyncId = $ivvy->run([
    $jobFactory->newUpdateContactJob($contact),
]);

if ($asyncId) {
    echo "Async ID: {$asyncId}\n";
} else {
    echo "Couldn't connect to the API server. Check iVvy's credentials\n";
    exit(1);
}
