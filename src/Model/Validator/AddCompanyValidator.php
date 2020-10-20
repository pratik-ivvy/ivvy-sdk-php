<?php

namespace Ivvy\Model\Validator;

use Iterator;
use Ivvy\Model\Company;

/**
 * Class: AddCompanyValidatorTest
 *
 * Contains the business rules for adding a Company.
 *
 * @see Validator
 */
class AddCompanyValidator implements Validator
{
    use ProcessBusinessRuleTrait;

    protected function process(Company $company)
    {
        if (!$company->businessName) {
            yield 'A businessName is needed to add a Company';
        }

        if (
            !$company->address || !$company->address->countryCode ||
            !$company->address->stateCode || !$company->address->postalCode
        ) {
            yield 'An address with countryCode, stateCode and postalCode is needed to add a Company';
        }

        if ($company->id) {
            yield 'Cannot add a Company that already has an id';
        }

        yield;
    }
}
