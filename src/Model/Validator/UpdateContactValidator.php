<?php

namespace Ivvy\Model\Validator;

use Iterator;
use Ivvy\Model\Contact;
use Respect\Validation\Validator as RespectValidator;

/**
 * Class: UpdateContactValidatorTest
 *
 * Contains the business rules for adding a Contact.
 *
 * @see Validator
 */
class UpdateContactValidator implements Validator
{
    use ProcessBusinessRuleTrait;

    protected function process(Contact $contact)
    {
        if (!$contact->id) {
            yield 'An id is needed to update a Contact';
        }

        if (!$contact->email) {
            yield 'An email is needed to update a Contact';
        }

        if ($contact->email && !RespectValidator::email()->validate($contact->email)) {
            yield 'Contact has an invalid email';
        }

        if ($contact->phone && !RespectValidator::phone()->validate($contact->phone)) {
            yield 'Contact has an invalid phone';
        }

        yield;
    }
}
