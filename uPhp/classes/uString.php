<?php

namespace uPhp\classes;
/**
 * u.array
 * Класс для работы со строками
 *
 * @author Eugene Fureev <efureev@yandex.ru>
 */
class uString
{

    /**
     * Проверяет, что строка в utf8 кодировке.
     * NOTE: This function checks for 5-Byte sequences, UTF8
     *       has Bytes Sequences with a maximum length of 4.
     * Written by Tony Ferrara <http://blog.ircmaxwell.com>
     *
     * @param  string $string The string to be checked
     *
     * @return boolean
     */
    public static function seemsUtf8($string)
    {
        if (function_exists('mb_check_encoding')) {
            return mb_check_encoding($string, 'UTF-8');
        }

        // @codeCoverageIgnoreStart
        return self::seemsUtf8Regex($string);
        // @codeCoverageIgnoreEnd
    }

    /**
     * A non-Mbstring UTF-8 checker.
     *
     * @param $string
     *
     * @return bool
     */
    protected static function seemsUtf8Regex($string)
    {
        // Obtained from http://stackoverflow.com/a/11709412/430062 with permission.
        $regex = '/(
    [\xC0-\xC1] # Invalid UTF-8 Bytes
    | [\xF5-\xFF] # Invalid UTF-8 Bytes
    | \xE0[\x80-\x9F] # Overlong encoding of prior code point
    | \xF0[\x80-\x8F] # Overlong encoding of prior code point
    | [\xC2-\xDF](?![\x80-\xBF]) # Invalid UTF-8 Sequence Start
    | [\xE0-\xEF](?![\x80-\xBF]{2}) # Invalid UTF-8 Sequence Start
    | [\xF0-\xF4](?![\x80-\xBF]{3}) # Invalid UTF-8 Sequence Start
    | (?<=[\x0-\x7F\xF5-\xFF])[\x80-\xBF] # Invalid UTF-8 Sequence Middle
    | (?<![\xC2-\xDF]|[\xE0-\xEF]|[\xE0-\xEF][\x80-\xBF]|[\xF0-\xF4]|[\xF0-\xF4][\x80-\xBF]|[\xF0-\xF4][\x80-\xBF]{2})[\x80-\xBF] # Overlong Sequence
    | (?<=[\xE0-\xEF])[\x80-\xBF](?![\x80-\xBF]) # Short 3 byte sequence
    | (?<=[\xF0-\xF4])[\x80-\xBF](?![\x80-\xBF]{2}) # Short 4 byte sequence
    | (?<=[\xF0-\xF4][\x80-\xBF])[\x80-\xBF](?![\x80-\xBF]) # Short 4 byte sequence (2)
)/x';

        return !preg_match($regex, $string);
    }



    /**
     * Высчитывает процент цисла $numerator от числа $denominator
     *
     * @param int|float $numerator
     * @param int|float $denominator
     * @param int       $decimals
     * @param string    $decimalPoint
     * @param string    $thousandsSeparator
     *
     * @return int|float
     */
    public static function calculatePercentage(
        $numerator,
        $denominator,
        $decimals = 2,
        $decimalPoint = '.',
        $thousandsSeparator = ','
    ) {
        return number_format(($numerator / $denominator) * 100, $decimals, $decimalPoint, $thousandsSeparator);
    }


    /**
     * Валидация email
     *
     * @param  string $possibleEmail An email address to validate
     *
     * @return bool
     */
    public static function validateEmail($possibleEmail)
    {
        return (bool)filter_var($possibleEmail, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Оборачивает все ссылки в гиперссылки HTML.
     *
     * @param  string $text The string to parse
     *
     * @return string
     */
    public static function linkify($text)
    {
        $text = preg_replace('/&apos;/', '&#39;', $text); // IE does not handle &apos; entity!
        $sectionHtmlPattern = '%# Rev:20100913_0900 github.com/jmrware/LinkifyURL
            # Section text into HTML <A> tags  and everything else.
              (                             # $1: Everything not HTML <A> tag.
                [^<]+(?:(?!<a\b)<[^<]*)*     # non A tag stuff starting with non-"<".
              |      (?:(?!<a\b)<[^<]*)+     # non A tag stuff starting with "<".
             )                              # End $1.
            | (                             # $2: HTML <A...>...</A> tag.
                <a\b[^>]*>                   # <A...> opening tag.
                [^<]*(?:(?!</a\b)<[^<]*)*    # A tag contents.
                </a\s*>                      # </A> closing tag.
             )                              # End $2:
            %ix';

        return preg_replace_callback($sectionHtmlPattern, [__CLASS__, 'linkifyCallback'], $text);
    }

    /**
     * Callback for the preg_replace in the linkify() method.
     * Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>
     *
     * @param  string $text
     *
     * @return string
     */
    protected static function linkifyRegex($text)
    {
        $urlPattern = '/# Rev:20100913_0900 github.com\/jmrware\/LinkifyURL
            # Match http & ftp URL that is not already linkified.
            # Alternative 1: URL delimited by (parentheses).
            (\() # $1 "(" start delimiter.
            ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+) # $2: URL.
            (\)) # $3: ")" end delimiter.
            | # Alternative 2: URL delimited by [square brackets].
            (\[) # $4: "[" start delimiter.
            ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+) # $5: URL.
            (\]) # $6: "]" end delimiter.
            | # Alternative 3: URL delimited by {curly braces}.
            (\{) # $7: "{" start delimiter.
            ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+) # $8: URL.
            (\}) # $9: "}" end delimiter.
            | # Alternative 4: URL delimited by <angle brackets>.
            (<|&(?:lt|\#60|\#x3c);) # $10: "<" start delimiter (or HTML entity).
            ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+) # $11: URL.
            (>|&(?:gt|\#62|\#x3e);) # $12: ">" end delimiter (or HTML entity).
            | # Alternative 5: URL not delimited by (), [], {} or <>.
            (# $13: Prefix proving URL not already linked.
            (?: ^ # Can be a beginning of line or string, or
            | [^=\s\'"\]] # a non-"=", non-quote, non-"]", followed by
           ) \s*[\'"]? # optional whitespace and optional quote;
            | [^=\s]\s+ # or... a non-equals sign followed by whitespace.
           ) # End $13. Non-prelinkified-proof prefix.
            (\b # $14: Other non-delimited URL.
            (?:ht|f)tps?:\/\/ # Required literal http, https, ftp or ftps prefix.
            [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]+ # All URI chars except "&" (normal*).
            (?: # Either on a "&" or at the end of URI.
            (?! # Allow a "&" char only if not start of an...
            &(?:gt|\#0*62|\#x0*3e); # HTML ">" entity, or
            | &(?:amp|apos|quot|\#0*3[49]|\#x0*2[27]); # a [&\'"] entity if
            [.!&\',:?;]? # followed by optional punctuation then
            (?:[^a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]|$) # a non-URI char or EOS.
           ) & # If neg-assertion true, match "&" (special).
            [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]* # More non-& URI chars (normal*).
           )* # Unroll-the-loop (special normal*)*.
            [a-z0-9\-_~$()*+=\/#[\]@%] # Last char can\'t be [.!&\',;:?]
           ) # End $14. Other non-delimited URL.
            /imx';

        $urlReplace = '$1$4$7$10$13<a href="$2$5$8$11$14">$2$5$8$11$14</a>$3$6$9$12';

        return preg_replace($urlPattern, $urlReplace, $text);
    }

    /**
     * Callback for the preg_replace in the linkify() method.
     * Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>
     *
     * @param  array $matches Matches from the preg_ function
     *
     * @return string
     */
    protected static function linkifyCallback($matches)
    {
        if (isset($matches[2])) {
            return $matches[2];
        }

        return self::linkifyRegex($matches[1]);
    }

    /**
     * Обрезание строки по длине без обрезки слов.
     *
     * @param   string  $string  The string to truncate
     * @param   integer $length  The length to truncate the string to
     * @param   string  $append  Text to append to the string IF it gets
     *                           truncated, defaults to '...'
     *
     * @return  string
     */
    public static function safeTruncate($string, $length, $append = '...')
    {
        $ret = substr($string, 0, $length);
        $lastSpace = strrpos($ret, ' ');

        if ($lastSpace !== false && $string != $ret) {
            $ret = substr($ret, 0, $lastSpace);
        }

        if ($ret != $string) {
            $ret .= $append;
        }

        return $ret;
    }

    /**
     * Truncate the string to given length of words.
     *
     * @param        $string
     * @param        $limit
     * @param string $append
     *
     * @return string
     */
    public static function limitWords($string, $limit = 100, $append = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $string, $matches);

        if (!isset($matches[0]) || strlen($string) === strlen($matches[0])) {
            return $string;
        }

        return rtrim($matches[0]) . $append;
    }

    /**
     * Заменяет первое вхождение в строке на подстроку
     *
     * @param     $search
     * @param     $replace
     * @param     $subject
     * @param int $cur
     *
     * @return string
     */
    public static function strReplaceFirst($search, $replace, $subject, $cur = 0)
    {
        $pos = strpos($subject, $search, $cur);
        if ($pos !== false) {
            return substr_replace($subject, $replace, (int)$pos, strlen($search));
        }

        return $subject;
    }


    /**
     * Заменяет все вхождения в строке на подстроку
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     *
     * @return string
     */
    public static function strReplaceAll($search, $replace, $subject)
    {
        return str_replace($search, $replace, $subject);
    }

    /**
     * Конвертирование \n и \r\n и \r в <br>
     *
     * @param string $str String to transform
     *
     * @return string
     */
    public static function nl2br($str)
    {
        return str_replace(["\r\n", "\r", "\n"], '<br>', $str);
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public static function nlRemoove($str)
    {
        return str_replace(["\r\n", "\r", "\n"], '', $str);
    }

    /**
     * Преобразует кавычки в елочки в строке
     *
     * @param string $str
     *
     * @return string
     */
    public static function strKavi4ki($str)
    {
        $str = preg_replace('/""+/', '"', $str); // удаляет множественные кавычки
        $str = preg_replace('/"([A-Za-zА-Яа-я0-9_])/', '«$1', $str);
        $str = preg_replace('#"#', '»', $str);

        return $str;
    }

    /**
     * Преобразует HTML-кавычки в HTML-елочки в строке
     *
     * @param string $str
     * @param bool   $isHtml true - то преобразуется в HTML сущности кавычек, иначе в обычные кавычки-елочки
     *
     * @return string
     */
    public static function strKavi4kiHTML($str, $isHtml = true)
    {
        $arr = $isHtml ? ['&laquo;', '&raquo;'] : ['«', '»'];

        $str = preg_replace('/&quot;&quot;+/', '&quot;', $str); // удаляет множественные кавычки
        $str = preg_replace('/&quot;([A-Za-zА-Яа-я0-9_])/', $arr[0] . '$1', $str);
        $str = preg_replace('#&quot;#', $arr[1], $str);

        return $str;
    }


}
