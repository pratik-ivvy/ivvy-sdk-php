# ivvy-sdk-php

A PSR-4 compilant SDK to work with iVvy's API 1.0

## Installation

Install through composer.

```bash
$ composer require ivvycode/ivvy-sdk-php:^0.1
```

## How to use?

This package exposes 2 high level APIs through the classes `Ivvy` and `JobFactory`. It is important that you understand this, as **this package is designed for you to work mainly these 2 classes** and the entities inside the `Ivvy\Model` namespace.

The `Ivvy` class is your client class to communicate with iVvy. It exposes endpoints that directly map to a namespace's action (as per iVvys API 1.0 docs).

```php
$ivvy = (new Ivvy\IvvyFactory)->newInstance($apiKey, $apiSecret);

$companies = $ivvy->getCompanyList();
```

The `JobFactory` class is a special class that is supposed to work with iVvy's asynchronous job queue on the `batch` namespace, specifically with the `run` action. It basically abstracts the creation of jobs to be sent. By the release `0.1.0`, there is no factory or "easy" way to instantiate this class, so we just pass all the concrete dependencies.

```php
$jobFactory = new Ivvy\JobFactory(
    new Ivvy\Model\Validator\AddCompanyValidator,
    new Ivvy\Model\Validator\UpdateCompanyValidator,
    new Ivvy\Model\Validator\AddContactValidator,
    new Ivvy\Model\Validator\UpdateContactValidator
);

$companyToBeAdded = new Ivvy\Model\Company([ ... ]);
$companyToBeUpdated = new Ivvy\Model\Company([ ... ]);

$addJob = $jobFactory->newAddCompanyJob($companyToBeAdded);
$updateJob = $jobFactory->newUpdateCompanyJob($companyToBeUpdated);

$asyncId = $ivvy->run([$addJob, $updateJob]);
```

In the particular case of jobs that are for adding or updating entities, JobFactory will call the validation methods on those entities, passing the corresponding specification. In simple words, it will throw a `BusinessRuleException` whenever you're trying to create a job using an entity that doesn't holds the appropriate values for said job. (I.E: you try to update a company, but the company object has no ID set).

```php
try {
    $companyWithInvalidValues = new Ivvy\Model\Company([ ... ]);

    $jobFactory->newAddCompanyJob($companyWithInvalidValues);
} catch (Ivvy\Model\BusinessRuleException $e) {
    error_log($e->getMessage()); // An exception will be thrown here
}
```
