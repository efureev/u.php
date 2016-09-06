<?php

namespace uPhp\classes;

/**
 * u.array
 * Класс для работы с датами
 *
 * @author Eugene Fureev <efureev@yandex.ru>
 */
class uDate
{

    /**
     * Возвращает список времени
     *
     * @param int $minutes
     *
     * @return array
     */
    public static function getTimeList($minutes = 30)
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
