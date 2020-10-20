<?php

namespace Ivvy\Model;

abstract class BaseModel
{
    /**
     * Returns the array representation of this object. Call `toArray()` on
     * properties of this class.
     *
     * @return array
     */
    public function toArray($removeEmptyValues = false)
    {
        $arr = [];

        foreach ($this as $prop => $value) {
            if (!$removeEmptyValues || !empty($value)) {
                $arr[$prop] = $value instanceof self ? $value->toArray($removeEmptyValues) : $value;
            }
        }

        return $arr;
    }
}
