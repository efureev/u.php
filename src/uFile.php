<?php

namespace efureev;

/**
 * Class uFile
 *
 * @package efureev
 */
class uFile
{

    /**
     * Return File Extension
     *
     * @param $filename
     *
     * @return mixed
     */
    public static function getExt($filename)
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    /**
     * Return format file size
     *
     * @param   integer $bytes    The number in bytes to format
     * @param   integer $decimals The number of decimal points to include
     *
     * @return  string
     */
    public static function sizeFormat($bytes, $decimals = 0)
    {
        $bytes = floatval($bytes);

        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < pow(1024, 2)) {
            return number_format($bytes / 1024, $decimals, '.', '') . ' KiB';
        } elseif ($bytes < pow(1024, 3)) {
            return number_format($bytes / pow(1024, 2), $decimals, '.', '') . ' MiB';
        } elseif ($bytes < pow(1024, 4)) {
            return number_format($bytes / pow(1024, 3), $decimals, '.', '') . ' GiB';
        } elseif ($bytes < pow(1024, 5)) {
            return number_format($bytes / pow(1024, 4), $decimals, '.', '') . ' TiB';
        } elseif ($bytes < pow(1024, 6)) {
            return number_format($bytes / pow(1024, 5), $decimals, '.', '') . ' PiB';
        } else {
            return number_format($bytes / pow(1024, 5), $decimals, '.', '') . ' PiB';
        }
    }

    /**
     * Return File size
     *
     * @param      $file
     * @param bool $format return size on format string
     *
     * @return int|string
     * @throws \Exception
     */
    public static function size($file, $format = true)
    {
        if (!file_exists($file))
            throw new \Exception('File missing: ' . $file);

        return $format ? self::sizeFormat(filesize($file)) : filesize($file);
    }


}
