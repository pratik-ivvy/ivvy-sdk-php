<?php

namespace Ivvy\Model\Validator;

use Ivvy\Model\Company;

/**
 * Class: UpdateCompanyValidatorTest
 *
 * Contains the business rules for adding a Company.
 *
 * @see Validator
 */
class UpdateCompanyValidator implements Validator
{
    use ProcessBusinessRuleTrait;

    protected function process(Company $company)
    {
        if (!$company->id) {
            yield 'An id is needed to update a Company';
        }

        if (
            !$company->address || !$company->address->countryCode ||
            !$company->address->stateCode || !$company->address->postalCode
        ) {
            yield 'An address with countryCode, stateCode and postalCode is needed to add a Company';
        }

        yield;
    }
}
