<?php

namespace Paybox\Utility;

class StringValidator
{
    /**
     * @param string $fieldName
     * @param string $value
     * @param int    $minLength
     * @param int    $maxLength
     * @param bool   $isNumeric
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public static function checkString($fieldName, $value, $minLength, $maxLength, $isNumeric)
    {
        $value = (string) $value;

        if ($isNumeric && ! ctype_digit($value)) {
            throw new \InvalidArgumentException('$' . $fieldName . ' must only contain digits.');
        }

        $length = strlen($value);

        if ($length < $minLength || $length > $maxLength) {
            throw new \InvalidArgumentException('$' . $fieldName . ' is not of the correct length.');
        }

        return $value;
    }
}
