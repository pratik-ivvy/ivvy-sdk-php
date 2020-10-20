<?php

namespace Ivvy\Model\Validator;

/**
 * Abstracts the mixin for process business rules implementation as it works on
 * generic objects.
 */
trait ProcessBusinessRuleTrait
{
    /**
     * @{inheritDocs}
     */
    public function processBusinessRules($genericObject)
    {
        return array_filter(iterator_to_array($this->process($genericObject)));
    }
}
