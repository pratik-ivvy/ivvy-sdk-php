<?php

namespace Ivvy\Model;

use Exception;

class BusinessRuleException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
