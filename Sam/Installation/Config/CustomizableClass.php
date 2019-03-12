<?php

/**
 * Interface CustomizableClassInterface
 */
interface CustomizableClassInterface
{
    /**
     * Extending class needs to be abstract class or implement getInstance method
     * and call protected static method _getInstance with __CLASS__ as parameter
     * <code>
     * public static function getInstance() {
     *     return parent::_getInstance(__CLASS__);
     * }
     * </code>
     */
    public static function getInstance();

    /**
     * Initialize state of instance, is called by getInstance().
     * It is public method, can be called again, but we need to adjust current code so it could return $this
     * @return void // TODO: @return $this
     */
    public function initInstance();
}

/**
 * Abstract class for implementing a customizable class pattern
 *
 * Extending class needs to implement getInstance method
 * and call protected static method _getInstance with __CLASS__ as parameter
 * <code>
 * public static function getInstance() {
 *     return parent::_getInstance(__CLASS__);
 * }
 * </code>
 */
abstract class CustomizableClass implements CustomizableClassInterface
{

    /**
     * Hide the constructor from direct access
     * Use ::getInstance() of the extending class instead
     *
     */
    protected function __construct()
    {
    }

    /**
     * To initialize instance properties
     */
    public function initInstance()
    {
    }

    /**
     * Returns an instance of the class
     * @param string $class class name
     * @return mixed instance of $class or $class .'Custom' if available
     */
    protected static function _getInstance($class)
    {
        // check whether class name is legal
        if (!preg_match('/^[\\a-zA-Z0-9_]+$/', $class)) {
            throw new RuntimeException('Illegal class name ' . $class);
        }

        $instanceClass = class_exists($class . 'Custom', false) ? $class . 'Custom' : $class;
        // check whether class exists. Auto-load is ok
        if (!class_exists($instanceClass)) {
            throw new RuntimeException('Unknown class ' . $instanceClass);
        }

        // do some checking on the class
        try {
            $reflection = new ReflectionClass($instanceClass);
        } catch (ReflectionException $e) {
            throw new RuntimeException('Unknown class ' . $instanceClass);
        }

        // check whether the class extends Singleton
        if (!$reflection->isSubclassOf(__CLASS__)) {
            throw new RuntimeException($instanceClass . ' must be sub class of ' . __CLASS__);
        }

        // if instance is customized class, check whether it is a subclass of the class
        if ($instanceClass === $class . 'Custom'
            && !$reflection->isSubclassOf($class)
        ) {
            throw new RuntimeException($instanceClass . ' must be sub class of ' . $class);
        }

        /** @var self $instance */
        $instance = new $instanceClass;
        $instance->initInstance();
        return $instance;
    }

}