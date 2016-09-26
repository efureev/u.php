<?php

namespace efureev;

/**
 * Class uTime
 *
 * @package efureev
 */
class uTime
{

    /**
     * Return time list
     *
     * @param int $minutes step of minutes
     *
     * @return array
     */
    public static function timeList($minutes = 30)
    {
        $res = [];
        $imax = 24 * (60 / $minutes);

        for ($i = 0; $i <= $imax - 1; $i++) {
            $value = $i * $minutes;
            $h = floor($value / 60);
            $m = floor($value % 60);
            if (strlen($h) === 1) $h = '0' . $h;
            if (strlen($m) === 1) $m = '0' . $m;
            $res[ $value ] = $h . ':' . $m;
        }

        return $res;
    }
}
