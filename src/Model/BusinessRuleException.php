<?php

namespace Ivvy\Model;

use Exception;

class BusinessRuleException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
