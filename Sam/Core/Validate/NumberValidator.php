<?php
/**
 * SAM-4737: General Validator
 *
 * @copyright       2018 Bidpath, Inc.
 * @author          Igors Kotlevskis
 * @package         com.swb.sam2
 * @version         SVN: $Id: $
 * @since           3/11/2019
 * file encoding    UTF-8
 *
 * Bidpath, Inc., 269 Mt. Hermon Road #102, Scotts Valley, CA 95066, USA
 * Phone: ++1 (415) 543 5825, &lt;info@bidpath.com&gt;
 */

namespace Sam\Core\Validate;

/**
 * Class NumberValidator
 * @package Sam\Core\Validate
 */
class NumberValidator
{
    /**
     * Returns whether or not $integer is a valid integer value
     * accepts negative or positive value
     * e.g., '1'.
     *
     * @param string|float|int $value the value to test
     * @return boolean the result
     */
    public static function isInt($value)
    {
        if (is_string($value)) {
            $value = trim($value);
        }
        if (is_numeric($value)) {
            $float = (float)$value;
            $int = (int)$value;
            $is = Floating::eq($float, $int);
            return $is;
        }
        return false;
    }

    /**
     * Returns whether or not $integer is a valid integer value
     * accepts positive value
     * e.g., '1' not '-1' or '0'.
     *
     * @param string|float|int $value the value to test
     * @return boolean the result
     */
    public static function isIntPositive($value)
    {
        $is = self::isInt($value) && (int)$value > 0;
        return $is;
    }

    /**
     * Check if passed value is positive integer or zero
     * @param string|float|int $value
     * @return bool
     */
    public static function isIntPositiveOrZero($value)
    {
        $is = self::isInt($value)
            && (int)$value >= 0;
        return $is;
    }

    /**ъ
     * Returns whether or not $value is a valid real number
     * accepts negative or positive value
     * e.g., '1.0'.
     *
     * @param string|float|int $value the value to test
     * @return boolean the result
     */
    public static function isReal($value)
    {
        if (is_string($value)) {
            $value = trim($value);
        }
        $isValidFormat = false;
        if (is_numeric($value)) {
            $isValidFormat = (bool)preg_match('/^-?\d{1,14}(\.\d{1,10})?$/', $value);
        }
        return $isValidFormat;
    }

    /**
     * Returns whether or not $value is a valid positive real number (not zero)
     *
     * @param string|float|int $value the value to test
     * @return boolean the result
     */
    public static function isRealPositive($value)
    {
        $isValidFormat = self::isReal($value) && Floating::gt($value, 0);
        return $isValidFormat;
    }

    /**
     * Returns whether or not $value is a valid positive real number or zero
     * accepts positive value
     *
     * @param string|float|int $value the value to test
     * @return boolean the result
     */
    public static function isRealPositiveOrZero($value)
    {
        $isValidFormat = self::isReal($value) && Floating::gteq($value, 0);
        return $isValidFormat;
    }
}