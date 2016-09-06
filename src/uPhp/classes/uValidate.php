<?php

namespace uPhp\classes;

/**
 * u.array
 * Класс валидации
 *
 * @author Eugene Fureev <efureev@yandex.ru>
 */
class uValidate
{

    /**
     * Проверяет, является ли значение форматом даты
     *
     * @param string $date
     *
     * @return bool
     * @test: ok
     */
    public static function isDateFormat($date)
    {
        return (bool)preg_match('/^([0-9]{4})-((0?[0-9])|(1[0-2]))-((0?[0-9])|([1-2][0-9])|(3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $date);
    }

    /**
     * Проверяет, является ли значение датой
     *
     * @param $date
     *
     * @return bool
     */
    public static function isDate($date)
    {
        if ($date === '0000-00-00 00:00:00') {
            return true;
        }
        if (!preg_match('/^([0-9]{4})-((?:0?[0-9])|(?:1[0-2]))-((?:0?[0-9])|(?:[1-2][0-9])|(?:3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $date, $matches)) {
            return false;
        }

        return checkdate((int)$matches[2], (int)$matches[3], (int)$matches[1]);
    }

    /**
     * Валидация 24-часового формата времени
     *
     * @param $time
     *
     * @return bool
     */
    public static function isTime($time)
    {
        if ($time === '00:00:00' || $time === '00:00') {
            return true;
        }
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $time)) {
            return true;
        }

        return false;
    }

    /**
     * Валидация значения, как булев тип
     *
     * @param $bool
     *
     * @return bool
     */
    public static function isBool($bool)
    {
        return $bool === null || is_bool($bool) || preg_match('/^0|1$/', $bool);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public static function isInt($value)
    {
        return ((string)(int)$value === (string)$value);
    }

    /**
     * Валидация типа Integer без отрицательных значений
     *
     * @param $value
     *
     * @return bool
     */
    public static function isUnsignedInt($value)
    {
        return (preg_match('#^[0-9]+$#', (string)$value) && $value < 4294967296 && $value >= 0);
    }

    /**
     * Валидация типа Integer без отрицательных значений и нуля
     *
     * @param $value
     *
     * @return bool
     */
    public static function isNoZeroInt($value)
    {
        return (preg_match('#^[0-9]+$#', (string)$value) && $value < 4294967296 && $value > 0);
    }

    /**
     * @param $float
     *
     * @return bool
     */
    public static function isFloat($float)
    {
        return strval((float)$float) == strval($float);
    }

    /**
     * @param $float
     *
     * @return bool
     */
    public static function isUnsignedFloat($float)
    {
        return strval((float)$float) == strval($float) && $float >= 0;
    }

    /**
     * @param $gmt
     *
     * @return int
     */
    public static function isGMT($gmt)
    {
        return preg_match('/^(\+||\-)[0-9]{1,3}$/', $gmt);
    }

    /**
     * @param $html
     *
     * @return bool
     */
    public static function isCleanHtml($html)
    {
        $events = 'onmousedown|onmousemove|onmmouseup|onmouseover|onmouseout|onload|onunload|onfocus|onblur|onchange';
        $events .= '|onsubmit|ondblclick|onclick|onkeydown|onkeyup|onkeypress|onmouseenter|onmouseleave|onerror|onselect';
        $events .= '|onafterupdate|onbeforeactivate|onbeforecopy|onbeforecut|onbeforedeactivate|onbeforeeditfocus';
        $events .= '|onbeforepaste|onbeforeprint|onbeforeunload|onbeforeupdate|onmove|ondragenter|onmousewheel';
        $events .= '|onbounce|oncellchange|oncontextmenu|oncontrolselect|oncopy|oncut|ondataavailable|ondatasetchanged|';
        $events .= 'ondatasetcomplete|ondeactivate|ondrag|ondragend|onreset|onabort|ondragdrop|onresize|onactivate';
        $events .= '|ondragleave|ondragover|ondragstart|ondrop|onerrorupdate|onfilterchange|onfinish|onafterprint';
        $events .= '|onfocusin|onfocusout|onhashchange|onhelp|oninput|onlosecapture|onmessage|onmouseup|onmovestart';
        $events .= '|onoffline|ononline|onpaste|onpropertychange|onreadystatechange|onresizeend|onmoveend';
        $events .= '|onresizestart|onrowenter|onrowexit|onrowsdelete|onrowsinserted|onscroll|onsearch|onselectionchange';
        $events .= '|onselectstart|onstart|onstop';

        return (!preg_match('/<[ \t\n]*script/ims', $html) && !preg_match('/(' . $events . ')[ \t\n]*=/ims', $html) && !preg_match('/.*script\:/ims', $html) && !preg_match('/<[ \t\n]*i?frame/ims', $html));
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public static function isPercentage($value)
    {
        return (self::isFloat($value) && $value >= 0 && $value <= 100);
    }

    /**
     * @param $color
     *
     * @return int
     */
    public static function isColor($color)
    {
        return preg_match('/^(#[0-9a-fA-F]{6}|[a-zA-Z0-9-]*)$/', $color);
    }

    /**
     * @param $url
     *
     * @return int
     */
    public static function isUrl($url)
    {
        return preg_match('/^[~:#,%&_=\(\)\.\? \+\-@\/a-zA-Z0-9]+$/', $url);
    }

    /**
     * @param $url
     *
     * @return bool|int
     */
    public static function isAbsoluteUrl($url)
    {
        if (!empty($url)) {
            return preg_match('/^https?:\/\/[,:#%&_=\(\)\.\? \+\-@\/a-zA-Z0-9]+$/', $url);
        }

        return true;
    }

    /**
     * @param $data
     *
     * @return bool
     */
    public static function isString($data)
    {
        return is_string($data);
    }

    /**
     * @param $data
     *
     * @return bool
     */
    public static function isSerializedArray($data)
    {
        return $data === null || (is_string($data) && preg_match('/^a:[0-9]+:{.*;}$/s', $data));
    }

    /**
     * @param $data
     *
     * @return bool
     */
    public static function isCoordinate($data)
    {
        return preg_match('/^\-?[0-9]{1,8}\.[0-9]{1,8}$/s', $data);
    }
}
