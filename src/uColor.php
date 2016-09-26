<?php

namespace efureev;

/**
 * Class uColor
 *
 * @package efureev
 */
class uColor
{

    /**
     * Convert a hexa decimal color code to its RGB equivalent
     *
     * @param string  $hexStr         (hexadecimal color value)
     * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise
     *                                returns associative array)
     * @param string  $separator      (to separate RGB values. Applicable only if second parameter is true.)
     *
     * @return array|string|null (depending on second parameter. Returns Null if invalid hex color value)
     */
    public static function hex2RGB($hexStr, $returnAsString = false, $separator = ',')
    {
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
        $rgbArray = [];
        if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            return null; //Invalid hex color code
        }

        return $returnAsString ? implode($separator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
    }

}
