<?php

namespace efureev;

/**
 * Class uList
 *
 * @package efureev
 */
class uList
{

    /**
     * Return range integer list
     *
     * @param int $from
     * @param int $to
     * @param int $step шаг
     *
     * @return array
     */
    public static function rangeList($from, $to, $step = 1)
    {
        if ($from==$to)
            return [(int)$from];

        $negative = $from > $to ? 1 : 0;

        $array = [];
        for ($i = ($negative ? $to : $from); $i <= ($negative ? $from : $to); $i += $step) {
            $array [] = $i;
        }
/*
        for ($i = $from; $i <= $to; $i += $step) {
            $array [] = $i;
        }*/

        return $negative ? array_reverse($array) : $array;
    }

}
