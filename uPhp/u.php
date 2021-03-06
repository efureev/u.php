<?php

namespace uPhp;

defined('UPHP_PATH') or define('UPHP_PATH', __DIR__);


/**
 * u.php
 *
 * @author Eugene Fureev <efureev@yandex.ru>
 * @link   http://github.com/efureev/u.php/
 * @method static bool isDateFormat(string $date) Проверяет, является ли значение форматом даты
 * @method static bool isAssociative($array, $allStrings = true) Проверяет, является ли массив ассоциативным
 * @method static bool isIndexed(array $array, $consecutive = false) Проверяет, является ли массив индексируемым
 * @method static array arrayClean(array $array) Очищает массив от пустых значений
 */
final class u
{

    private static $classMap = [];
    private static $innerClassMap = [
        'uPhp\Exceptions\UnknownMethodException' => '/Exceptions/UnknownMethodException.php',
        'uPhp\Exceptions\InvalidParamException'  => '/Exceptions/InvalidParamException.php',
    ];

    public static function __callStatic($name, $arguments)
    {
        static::autoloadInnerClass();
        static::$classMap = include(UPHP_PATH . '/classes.php');

        foreach (array_keys(static::$classMap) as $classNamespace) {
            if (method_exists($classNamespace, $name)) {
                return call_user_func_array([$classNamespace, $name], $arguments);
            }
        }

        throw new \uPhp\Exceptions\UnknownMethodException('Calling unknown method: ' . get_class() . "::$name()");
    }

    private static function autoloadInnerClass()
    {
        foreach (static::$innerClassMap as $nsClass => $classFile) {
            if ($classFile === false || !is_file(UPHP_PATH . $classFile))
                throw new \Exception ("Inner class autoload error for class: " . $nsClass);
            include_once(UPHP_PATH . $classFile);
        }
    }

    /**
     * @param $className
     *
     * @throws \uPhp\Exceptions\UnknownClassException
     * @test: ok
     */
    public static function autoload($className)
    {
        if (isset(static::$classMap[ $className ])) {
            $classFile = static::$classMap[ $className ];
            if ($classFile === false || !is_file($classFile))
                return;
        } else
            return;

        include_once($classFile);

        if (!class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
            throw new \uPhp\Exceptions\UnknownClassException ("Unable to find '$className' in file: $classFile. Namespace missing?");
        }
    }

    /**
     * @return string
     * @test: ok
     */
    public static function version()
    {
        return '0.1.2';
    }

    /**
     * @return string
     * @test: ok
     */
    public function __toString()
    {
        return static::version();
    }

}

spl_autoload_register(['uPhp\u', 'autoload'], true, true);