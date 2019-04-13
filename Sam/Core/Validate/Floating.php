<?php
namespace Sam\Core\Validate;

/**
 * Class to compare floats rounding to precision
 * Class Floating
 */

class Floating extends \CustomizableClass
{
    protected static $precision;

    /**
     * Get a Floating instance
     * @return Floating
     */
    public static function getInstance()
    {
        return parent::_getInstance(__CLASS__);
    }

    /**
     * Compare two floats for equality
     * Wrapper for _eq($x, $y)
     *
     * @param float|string $x
     * @param float|string $y
     * @return boolean $x = $y
     */
    public static function eq($x, $y)
    {
        $instance = self::getInstance();
        return $instance->isEqual($x, $y);
    }

    /**
     * Compare whether two floats are not equal
     * Wrapper for _neq($x, $y)
     *
     * @param float|string $x
     * @param float|string $y
     * @return boolean $x != $y
     */
    public static function neq($x, $y)
    {
        $instance = self::getInstance();
        return $instance->isNotEqual($x, $y);
    }

    /**
     * Compare whether a float is greater than the other
     * Wrapper for _gt($x, $y)
     *
     * @param float|string $x
     * @param float|string $y
     * @return boolean $x > $y
     */
    public static function gt($x, $y)
    {
        $instance = self::getInstance();
        return $instance->isGreater($x, $y);
    }

    /**
     * Compare whether a float is less than the other
     * Wrapper for _lt($x, $y)
     *
     * @param float|string $x
     * @param float|string $y
     * @return boolean $x < $y
     */
    public static function lt($x, $y)
    {
        $instance = self::getInstance();
        return $instance->isLower($x, $y);
    }

    /**
     * Compare whether one float is greater or equal than the other
     * Wrapper for _gteq($x, $y)
     *
     * @param float|string $x
     * @param float|string $y
     * @return boolean $x >= $y
     */
    public static function gteq($x, $y)
    {
        $instance = self::getInstance();
        return $instance->isGreaterOrEqual($x, $y);
    }

    /**
     * Compare whether on float is less or equal than the other
     * Wrapper for _lteq($x, $y)
     *
     * @param float|string $x
     * @param float|string $y
     * @return boolean $x <= $y
     */
    public static function lteq($x, $y)
    {
        $instance = self::getInstance();
        return $instance->isLowerOrEqual($x, $y);
    }

    /**
     * Compare two floats for equality
     *
     * @param float|string $x
     * @param float|string $y
     * @return boolean $x = $y
     */
    protected function isEqual($x, $y)
    {
        $x = (float)$x;
        $y = (float)$y;
        $precision = self::getPrecision();
        $isEqual = round($x, $precision) === round($y, $precision);
        return $isEqual;
    }

    /**
     * Compare whether two floats are not equal
     *
     * @param float|string $x
     * @param float|string $y
     * @return boolean $x != $y
     */
    protected function isNotEqual($x, $y)
    {
        $x = (float)$x;
        $y = (float)$y;
        $precision = self::getPrecision();
        $notEqual = round($x, $precision) !== round($y, $precision);
        return $notEqual;
    }

    /**
     * Compare whether a float is greater than the other
     *
     * @param float|string $x
     * @param float|string $y
     * @return boolean $x > $y
     */
    protected function isGreater($x, $y)
    {
        $x = (float)$x;
        $y = (float)$y;
        $precision = self::getPrecision();
        $isGreater = round($x, $precision) > round($y, $precision);
        return $isGreater;
    }

    /**
     * Compare whether a float is less than the other
     *
     * @param float|string $x
     * @param float|string $y
     * @return boolean $x < $y
     */
    protected function isLower($x, $y)
    {
        $x = (float)$x;
        $y = (float)$y;
        $precision = self::getPrecision();
        $isLess = round($x, $precision) < round($y, $precision);
        return $isLess;
    }

    /**
     * Compare whether one float is greater or equal than the other
     *
     * @param float|string $x
     * @param float|string $y
     * @return boolean $x >= $y
     */
    protected function isGreaterOrEqual($x, $y)
    {
        $x = (float)$x;
        $y = (float)$y;
        $precision = self::getPrecision();
        $isGreaterOrEqual = round($x, $precision) >= round($y, $precision);
        return $isGreaterOrEqual;
    }

    /**
     * Compare whether on float is less or equal than the other
     *
     * @param float|string $x
     * @param float|string $y
     * @return boolean $x <= $y
     */
    protected function isLowerOrEqual($x, $y)
    {
        $x = (float)$x;
        $y = (float)$y;
        $precision = self::getPrecision();
        $isLowerOrEqual = round($x, $precision) <= round($y, $precision);
        return $isLowerOrEqual;
    }

    /**
     * Set precision for comparisons
     *
     * @param integer $precision
     */
    public static function setPrecision($precision)
    {
        self::$precision = $precision;
    }

    /**
     * @return int - float precision on which we compare
     */
    public static function getPrecision()
    {
        if (self::$precision === null) {
            self::$precision = (function_exists('cfg'))
                ? cfg()->core->general->floatPrecisionCompare
                : 2;            
        }
        return self::$precision;
    }
}
