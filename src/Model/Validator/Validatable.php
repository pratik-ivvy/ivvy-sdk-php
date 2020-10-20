<?php

namespace Ivvy\Model\Validator;

use Ivvy\Model\BusinessRuleException;

/**
 * Interface: Validatable
 *
 * Represents a business object that can be validated
 */
interface Validatable
{
    /**
     * @throws BusinessRuleException
     */
    public function validate(Validator $validator);
}
