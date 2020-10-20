<?php

namespace Ivvy\Model\Validator;

/**
 * Interface: Validator
 *
 * Represent a set of business rules to validate
 */
interface Validator
{
    /**
     * Process the passed object according to the rules. Due to PHP's lack
     * of generics, we call the parameter `$genericObject` and we don't
     * type-hint it.
     *
     * @param mixed $genericObject
     *
     * @return array - contains a message describing the error for every
     *                 broken business rule.
     */
    public function processBusinessRules($genericObject);
}
