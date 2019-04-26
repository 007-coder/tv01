<?php

namespace Sam\Installation\Config;

use Sam\Core\Validate\NumberValidator;

/**
 * Class ValidatorClass
 * Validate input POST data
 * @package Sam\Installation\Config
 * @author Yura Vakulenko
 */
class ConfigValidator
{

    const T_BOOL = 'boolean';
    const T_INTEGER = 'integer';
    const T_DOUBLE = 'double';
    const T_STRING = 'string';
    const T_ARRAY = 'array';
    const T_OBJECT = 'object';
    const T_RESOURCE = 'resource';
    const T_NULL = 'NULL';
    const T_UNKNOWN_TYPE = 'unknown type';

    /**
     * Error messages
     * @var array
     */
    protected $errorMessages = [
        'dataType' => [
            'boolean' => 'Value must be of type "boolean".',
            'integer' => 'Value must be of type "Integer".',
            'double' => 'Value must be of type "double".',
            'string' => 'Value must be of type "string".',
            'array' => 'Value must be of type "array".',
            'object' => 'Value must be of type "object".',
            'resource' => 'Value must be of type "resource".',
            'NULL' => 'Value must be of type "NULL" or empty.',
            'unknown type' => 'Unknown data type.',
        ],
        'custom' => [
            'methodName' => 'message',
        ]
    ];

    /**
     * Get error message from $this->errorMessages
     * @param string $type
     * @param $key
     * @return string
     */
    public function getErrorMessage($key, $type = 'dataType')
    {
        return (isset($this->errorMessages[$type][$key]))
            ? $this->errorMessages[$type][$key]
            : '';
    }

    /**
     * Validation: is value a "boolean" type
     * @param $value
     * @return bool
     */
    public function isBoolean($value)
    {
        $booleans = ['1', 'true', true, 1, '0', 'false', false, 0, 'yes', 'no', 'on', 'off'];

        if (in_array($value, $booleans, true)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validation: is value a "integer" type
     * @param $value
     * @return bool
     */
    public function isInteger($value)
    {
        /*if (filter_var($value, FILTER_VALIDATE_INT)) {
            return true;
        } else {
            return false;
        }*/
        return NumberValidator::isInt($value);
    }

    public function isArray($value)
    {
        return (is_array($value)) ? true : false;
    }

    /**
     * @param $value
     * @return bool
     */
    public function isIntPositive($value)
    {
        return NumberValidator::isIntPositive($value);
    }

    /**
     * @param $value
     * @return bool
     */
    public function isIntPositiveOrZero($value)
    {
        return NumberValidator::isIntPositiveOrZero($value);
    }


    /**
     * Validation: is value a "float" type
     * @param $value
     * @return bool
     */
    public function isDouble($value)
    {
        /*if (filter_var($value, FILTER_VALIDATE_INT)) {
            return false;
        } else {
            if (filter_var($value, FILTER_VALIDATE_FLOAT)) {
                return true;
            } else {
                if (in_array($value, [0, 0.0, '0', '0.0'], true)) {
                    return true;
                } else {
                    return false;
                }
            }
        }*/
        return NumberValidator::isReal($value);
    }

    /**
     * @param $value
     * @return bool
     */
    public function isRealPositive($value)
    {
        return NumberValidator::isRealPositive($value);
    }

    /**
     * @param $value
     * @return bool
     */
    public function isRealPositiveOrZero($value)
    {
        return NumberValidator::isRealPositiveOrZero($value);
    }


    /**
     * Validation: is value a "string" type
     * @param $value
     * @return bool
     */
    public function isString($value)
    {
        if (is_string($value)){
            return true;
        } else {
            return false;
        }

    }

    /**
     * Validation: is value a "NULL" type
     * @param $value
     * @return bool
     */
    public function isNULL($value)
    {
        if (is_null($value) || empty($value)) {
            return true;
        } else {
            return false;
        }
    }


}