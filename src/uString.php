<?php

namespace efureev;

/**
 * Class uString
 *
 * @package efureev
 */
class uString
{

    /**
     * Start string with $startStr
     *
     * @param  string $string
     * @param  string $startStr
     *
     * @return boolean
     */
    public static function isStartStr($string, $startStr)
    {
        if ($startStr === '')
            return false;

        return strpos($string, $startStr) === 0;
    }

    /**
     * Finish string with $endStr
     *
     * @param  string $string
     * @param  string $endStr
     *
     * @return boolean
     */
    public static function endsWith($string, $endStr)
    {
        return substr($string, -strlen($endStr)) === $endStr;
    }

    /**
     * String contains substring
     *
     * @param  string  $string
     * @param  string  $subStr
     * @param  boolean $caseSensitive
     *
     * @return boolean
     */
    public static function contains($string, $subStr, $caseSensitive = true)
    {
        return $caseSensitive
            ? strpos($string, $subStr) !== false
            : stripos($string, $subStr) !== false;
    }

    /**
     * Strip many spaces to one
     *
     * @param  string $string The string to strip
     *
     * @return string
     */
    public static function stripSpace($string)
    {
        return preg_replace('/\s+/', ' ', $string);
    }

    /**
     * Clear string by ops:
     * - lower case
     * - Alphanumeric & digits only
     * - self::stripSpace
     * - trim
     *
     * @param  string $string the string to sanitize
     * @param  bool   $alpha
     * @param  bool   $strip
     *
     * @return string
     */
    public static function sanitizeString($string, $alpha = true, $strip = true)
    {
        $string = strtolower($string);
        if ($alpha) $string = preg_replace('/[^a-zA-Z 0-9]+/', '', $string);
        if ($strip) $string = self::stripSpace($string);
        $string = trim($string);

        return $string;
    }

    /**
     * Add leading zero to number
     *
     * @param  int $number The number to pad
     * @param  int $length The total length of the desired string
     *
     * @return string
     */
    public static function zeroPad($number, $length)
    {
        return str_pad($number, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the number of bytes in the given string.
     * This method ensures the string is treated as a byte array by using `mb_strlen()`.
     *
     * @param string $string the string being measured for length
     *
     * @return integer the number of bytes in the given string.
     */
    public static function byteLength($string)
    {
        return mb_strlen($string, '8bit');
    }

    /**
     * Performs string comparison using timing attack resistant approach.
     *
     * @see http://codereview.stackexchange.com/questions/13512
     *
     * @param string $expected string to compare.
     * @param string $actual   user-supplied string.
     *
     * @return boolean whether strings are equal.
     */
    public function compareString($expected, $actual)
    {
        $expected .= "\0";
        $actual .= "\0";
        $expectedLength = uString::byteLength($expected);
        $actualLength = uString::byteLength($actual);
        $diff = $expectedLength - $actualLength;
        for ($i = 0; $i < $actualLength; $i++) {
            $diff |= (ord($actual[ $i ]) ^ ord($expected[ $i % $expectedLength ]));
        }

        return $diff === 0;
    }
}
