<?php

namespace Ivvy\Model;

/**
 * Class: CustomField
 *
 * @see BaseModel
 */
class CustomField extends BaseModel
{
    public $fieldId;
    public $displayName;
    public $value;

    /**
     * Construct a new CustomField object
     */
    public function __construct($fieldId, $displayName, $value)
    {
        $this->fieldId     = $fieldId;
        $this->displayName = $displayName;
        $this->value       = $value;
    }
}
