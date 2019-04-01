<?php
namespace Sam\Installation\Config;


/**
 * Class ValidatorClass
 * Validate input POST data
 * @package Sam\Installation\Config
 */
class ValidatorClass
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

    protected $errorMessages = [
        'boolean' => '',
        'integer' => '',
        'double' => '',
        'string' => '',
        'array' => '',
        'object' => '',
        'resource' => '',
        'NULL' => '',
        'unknown type' => ''
    ];

    public function getErrorMessage($dataType) {
        return (isset($this->errorMessages[$dataType]))
            ? $this->errorMessages[$dataType]
            : '';
    }


    public function isBoolean($value) {
        if (is_bool($value)) {
            return true;
        } else if (in_array($value, ['true', 'false'])){
            return true;
        } else {
            return false;
        }
    }


}