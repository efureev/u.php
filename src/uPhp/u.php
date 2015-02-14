<?php

namespace uPhp;

defined('UPHP_PATH') or define('UPHP_PATH', __DIR__);



/**
 * u.php
 *
 * @author Eugene Fureev <efureev@yandex.ru>
 * @link   http://github.com/efureev/u.php/
 */
final class u {

    private static $classMap = [];
    private static $innerClassMap = [
        'uPhp\Exceptions\UnknownMethodException' => '/Exceptions/UnknownMethodException.php',
    ];

    public static function __callStatic($name, $arguments) {
        static::autoloadInnerClass();
        static::$classMap = include(UPHP_PATH . '/classes.php');

        foreach (static::$classMap as $classNamespace => $className) {
            if (method_exists($classNamespace, $name)) {
                return call_user_func_array([$classNamespace, $name], $arguments);
            }
        }

        throw new \uPhp\Exceptions\UnknownMethodException('Calling unknown method: ' . get_class() . "::$name()");
    }

    private static function autoloadInnerClass() {
        foreach (static::$innerClassMap as $nsClass => $classFile) {
            if ($classFile === false || !is_file(UPHP_PATH.$classFile))
                throw new Exception ("Inner class autoload error for class: ". $nsClass);
            include_once(UPHP_PATH.$classFile);
        }
    }

    public static function autoload($className)
    {
        if (isset(static::$classMap[$className])) {
            $classFile = static::$classMap[$className];
            if ($classFile === false || !is_file($classFile))
                return;
        } else
            return;

        include_once($classFile);

        if (!class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
            throw new \uPhp\Exceptions\UnknownClassException ("Unable to find '$className' in file: $classFile. Namespace missing?");
        }
    }

    public static function version() {
        return '0.1.0';
    }

    public function __toString() {
        return static::version();
    }

}

spl_autoload_register(['uPhp\u', 'autoload'], true, true);