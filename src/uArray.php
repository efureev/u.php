<?php

namespace efureev;

/**
 * Class uArray
 *
 * @package efureev
 */
class uArray
{

    /**
     * Проверяет, является ли массив ассоциативным
     * Является, если все ключи являются строками. Если `$allStrings` == false,
     * то масссив является ассоциативным, если хотя бы один ключ является строкой.
     * Note Пустой массив - не ассоциативен.
     *
     * @param array   $array      the array being checked
     * @param boolean $allStrings whether the array keys must be all strings in order for
     *                            the array to be treated as associative.
     *
     * @test: ok
     * @return boolean whether the array is associative
     */
    public static function isAssociative($array, $allStrings = true)
    {
        if (!is_array($array) || empty($array)) {
            return false;
        }

        if ($allStrings) {
            foreach ($array as $key => $value) {
                if (!is_string($key)) {
                    return false;
                }
            }

            return true;
        } else {
            foreach (array_keys($array) as $key) {
                if (is_string($key)) {
                    return true;
                }
            }

            return false;
        }
    }

    /**
     * Проверяет, является ли массив индексируемым
     * Массив индексируем, если все ключи массива - цифры. Если `$consecutive` == true,
     * то ключи массива будут последовательные и начинаться с 0.
     * Note Пустой массив - индексируемый
     *
     * @param array   $array       the array being checked
     * @param boolean $consecutive whether the array keys must be a consecutive sequence
     *                             in order for the array to be treated as indexed.
     *
     * @test: ok
     * @return boolean whether the array is associative
     */
    public static function isIndexed(array $array, $consecutive = false)
    {
        if (!is_array($array)) {
            return false;
        }

        if (empty($array)) {
            return true;
        }

        if ($consecutive) {
            return array_keys($array) === range(0, count($array) - 1);
        } else {
            foreach (array_keys($array) as $key) {
                if (!is_integer($key)) {
                    return false;
                }
            }

            return true;
        }
    }


    /**
     * Remove empty values from array: FALSE, 0, '0', '', null
     *
     * @param array $array
     *
     * @test: ok
     * @return array
     */
    public static function clean(array $array)
    {
        return array_filter($array);
    }

    /**
     * Remove from array by value.
     *
     * @return array
     */
    public static function removeValue()
    {
        $args = func_get_args();

        return array_diff($args[0], array_slice($args, 1));
    }

    /**
     * Checks if the given key exists in the array by a string representation
     *
     * @param string $key   <p>Name key in the array. Example: key[sub_key][sub_sub_key]</p>
     * @param array  $array <p>The array</p>
     *
     * @return bool returns true if key is exists, false otherwise
     */
    public static function exists($key, array $array)
    {
        if ($key === '') {
            return array_key_exists((string)$key, $array);
        }

        return self::parseAndValidateKeys($key, $array)['isExists'];
    }

    /**
     * Save element to the array by a string representation
     *
     * @param string $key     <p>Name key in the array. Example: key[sub_key][sub_sub_key]</p>
     * @param array  $array   <p>The array. This array is passed by reference</p>
     * @param mixed  $value   <p>The current value</p>
     * @param bool   $replace [optional] <p>Replace exists value</p>
     *
     * @return bool returns true if success, false otherwise
     */
    public static function save($key, array &$array, $value, $replace = true)
    {
        if ($key === '') {
            $array[ $key ] = $value;

            return true;
        }
        if ($key === '[]') {
            $array[] = $value;

            return true;
        }
        $parseInfo = self::parseAndValidateKeys($key, $array, 'save');
        if ($parseInfo['completed']) {
            $currEl = &$array;
            foreach ($parseInfo['keys'] as $key) {
                if (!array_key_exists((string)$key, (array)$currEl)) {
                    if (!$parseInfo['append'] && !is_array($currEl) && $currEl !== null) {
                        $parseInfo['completed'] = false;
                        break;
                    }
                    $mCurSource[ $key ] = [];
                } else {
                    if (!$parseInfo['append'] && !is_array($currEl)) {
                        $parseInfo['completed'] = false;
                        break;
                    }
                }
                $currEl = &$currEl[ $key ];
            }
            if ($parseInfo['completed']) {
                if (!$replace && $parseInfo['isExists']) {
                    return false;
                }
                if ($parseInfo['append']) {
                    $currEl[] = $value;
                } else {
                    $currEl = $value;
                }
            }
        }

        return $parseInfo['completed'];
    }

    /**
     * Delete element from the array by a string representation
     *
     * @param string $key   <p>Name key in the array. Example: key[sub_key][sub_sub_key]</p>
     * @param array  $array <p>The array. This array is passed by reference</p>
     *
     * @return bool returns true if success, false otherwise
     */
    public static function delete($key, array &$array)
    {
        if ($key === '' && array_key_exists((string)$key, $array)) {
            unset($array[ $key ]);

            return true;
        }
        if ($key === '[]') {
            return false;
        }

        return self::parseAndValidateKeys($key, $array, 'delete')['completed'];
    }

    /**
     * Get element of the array by a string representation
     *
     * @param string $key          <p>Name key in the array. Example: key[sub_key][sub_sub_key]</p>
     * @param array  $array        <p>The array</p>
     * @param string $default      [optional] <p>Default value if key not exist, default: null</p>
     * @param bool   $ignoreString [optional] <p>Ignore string element as array, get only element, default: true</p>
     *
     * @return mixed returns value by a key, or default value otherwise
     */
    public static function get($key, array $array, $default = null, $ignoreString = true)
    {
        if ($key === '') {
            if (!array_key_exists((string)$key, $array)) {
                return $default;
            }

            return $array[ $key ];
        }
        if ($key === '[]') {
            return $default;
        }
        $parseInfo = self::parseAndValidateKeys($key, $array, 'get');
        if ($ignoreString) {
            return (!$parseInfo['isString'] && $parseInfo['completed']) ? $parseInfo['value'] : $default;
        }

        return ($parseInfo['completed'] && ($parseInfo['isExists'] || $parseInfo['isString'])) ? $parseInfo['value'] : $default;
    }

    /**
     * Parse string & validate passed array, if mode present do next
     *  mode:
     *      get - get value if exists
     *      delete - unset existing element
     *      save - additional conditions for completed state
     *
     * @param string $key   <p>String representation of array element</p>
     * @param array  $array <p>The array, passed by reference for manipulating</p>
     * @param string $mode  [optional] <p>Mode action</p>
     *
     * @return array
     */
    private static function parseAndValidateKeys($key, array &$array, $mode = '')
    {
        $parseInfo = [
            'keys'         => [],
            'lastKey'      => '',
            'prevEl'       => &$array,
            'currEl'       => &$array,
            'isExists'     => null,
            'cntEBrackets' => 0,
            'isString'     => false,
            'completed'    => false,
            'first'        => true,
            'append'       => false,
            'value'        => null
        ];
        $parseInfo['isBroken'] = (bool)preg_replace_callback(['/(?J:\[([\'"])(?<el>.*?)\1\]|(?<el>\]?[^\[]+)|\[(?<el>(?:[^\[\]]+|(?R))*)\])/'],
            function($m) use (&$parseInfo, &$array) {
                if ($m[0] == '[]') {
                    $parseInfo['isExists'] = false;
                    $parseInfo['cntEBrackets']++;
                    $parseInfo['append'] = $parseInfo['cntEBrackets'] == 1;

                    return '';
                }
                $parseInfo['append'] = false;
                $parseInfo['keys'][] = $m['el'];
                if ($parseInfo['isExists'] !== false) {
                    if (!is_array($parseInfo['currEl'])) {
                        $parseInfo['isExists'] = false;
                        $parseInfo['lastKey'] = $m['el'];

                        return '';
                    }
                    if (($parseInfo['isExists'] = array_key_exists((string)$m['el'],
                            $parseInfo['currEl']) && is_array($parseInfo['currEl']))
                    ) {
                        if (!$parseInfo['first']) {
                            $parseInfo['prevEl'] = &$parseInfo['currEl'];
                        }
                        $parseInfo['currEl'] = &$array[ $m['el'] ];
                        $parseInfo['lastKey'] = $m['el'];
                        $parseInfo['first'] = false;
                    }
                }

                return '';
            }, $key);
        if ($parseInfo['isExists'] === false && is_array($parseInfo['prevEl']) && is_string($parseInfo['currEl'])) {
            $parseInfo['isString'] = true;
            if ($mode == 'get' && isset($parseInfo['currEl'][ $parseInfo['lastKey'] ])) {
                $parseInfo['completed'] = true;
                $parseInfo['value'] = $parseInfo['currEl'][ $parseInfo['lastKey'] ];
            }
        }
        if ($mode == 'get' && $parseInfo['isExists']) {
            $parseInfo['completed'] = true;
            $parseInfo['value'] = $parseInfo['prevEl'][ $parseInfo['lastKey'] ];
        }
        if ($mode == 'delete' && $parseInfo['isExists']) {
            unset($parseInfo['prevEl'][ $parseInfo['lastKey'] ]);
            $parseInfo['completed'] = true;
        }
        if ($mode == 'save') {
            if ($parseInfo['append']) {
                if ($parseInfo['cntEBrackets'] == 1) {
                    $parseInfo['completed'] = true;
                }
            } else {
                if ($parseInfo['cntEBrackets'] == 0) {
                    $parseInfo['completed'] = true;
                }
            }
        }
        if ($parseInfo['isBroken']) {
            $parseInfo['completed'] = false;
        }

        return $parseInfo;
    }
}
