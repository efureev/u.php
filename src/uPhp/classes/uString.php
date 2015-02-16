<?php

namespace uPhp\classes;
use uPhp\u;
/**
 * u.array
 * Класс для работы со строками
 *
 * @author Eugene Fureev <efureev@yandex.ru>
 */
class uString {

	/**
	 * Проверяет, что строка в utf8 кодировке.
	 *
	 * NOTE: This function checks for 5-Byte sequences, UTF8
	 *       has Bytes Sequences with a maximum length of 4.
	 *
	 * Written by Tony Ferrara <http://blog.ircmaxwell.com>
	 *
	 * @param  string $string The string to be checked
	 * @return boolean
	 */
	public static function seems_utf8($string) {
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
	 * @return bool
	 */
	protected static function seemsUtf8Regex($string) {
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

		return ! preg_match($regex, $string);
	}

	/**
	 * Начинается ли строка с подстроки
	 *
	 * @param  string $string
	 * @param  string $starts_with
	 * @return boolean
	 */
	public static function starts_with($string, $starts_with) {
		return strpos($string, $starts_with) === 0;
	}

	/**
	 * Заканчивается ли строка подстрокой
	 *
	 * @param  string $string
	 * @param  string $ends_with
	 * @return boolean
	 */
	public static function ends_with($string, $ends_with) {
		return substr($string, -strlen($ends_with)) === $ends_with;
	}

	/**
	 * Строка содержит подстроку
	 *
	 * @param  string $haystack
	 * @param  string $needle
	 * @param  boolean $caseSensitive Регистронезависимый. TRUE = учитывать регистр, False = не учитывать
	 * @return boolean
	 */
	public static function strContains($haystack, $needle, $caseSensitive = true){
		return $caseSensitive
			? strpos($haystack, $needle) !== false
			: stripos($haystack, $needle) !== false;
	}

	/**
	 * Удаление множественных пробелов
	 *
	 * @param  string $string The string to strip
	 * @return string
	 */
	public static function strip_space($string) {
		return preg_replace('/\s+/', ' ', $string);
	}

	/**
	 * Очищает строку по следующим операциям :
	 * - Нижний регистр
	 * - Удаляет все, исключая латиницу и цифры
	 * - Удаляет множественные пробелы
	 * - Удаляет пробелы из начала и с конца
	 *
	 * @param  string $string the string to sanitize
	 * @return string
	 */
	public static function sanitize_string($string) {
		$string = strtolower($string);
		$string = preg_replace('/[^a-zA-Z 0-9]+/', '', $string);
		$string = self::strip_space($string);
		$string = trim($string);

		return $string;
	}

	/**
	 * Добавляет ведущие нули до определенной длины
	 *
	 * @param  int  $number The number to pad
	 * @param  int  $length The total length of the desired string
	 * @return string
	 */
	public static function zero_pad($number, $length){
		return str_pad($number, $length, '0', STR_PAD_LEFT);
	}

	/**
	 * Высчитывает процент цисла $numerator от числа $denominator
	 *
	 * @param int|float $numerator
	 * @param int|float $denominator
	 * @param int $decimals
	 * @param string $dec_point
	 * @param string $thousands_sep
	 * @return int|float
	 */
	public static function calculate_percentage($numerator, $denominator, $decimals = 2, $dec_point = '.', $thousands_sep = ','){
		return number_format(($numerator / $denominator) * 100, $decimals, $dec_point, $thousands_sep);
	}

	/**
	 * Генерирует строку со случайными знаками
	 *
	 * @throws  \LengthException  If $length is bigger than the available
	 *                           character pool and $no_duplicate_chars is
	 *                           enabled
	 *
	 * @param   integer $length             The length of the string to
	 *                                      generate
	 * @param   boolean $human_friendly     Whether or not to make the
	 *                                      string human friendly by
	 *                                      removing characters that can be
	 *                                      confused with other characters (
	 *                                      O and 0, l and 1, etc)
	 * @param   boolean $include_symbols    Whether or not to include
	 *                                      symbols in the string. Can not
	 *                                      be enabled if $human_friendly is
	 *                                      true
	 * @param   boolean $no_duplicate_chars Whether or not to only use
	 *                                      characters once in the string.
	 * @return  string
	 */
	public static function random_string($length = 16, $human_friendly = true, $include_symbols = false, $no_duplicate_chars = false) {
		$nice_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefhjkmnprstuvwxyz23456789';
		$all_an     = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
		$symbols    = '!@#$%^&*()~_-=+{}[]|:;<>,.?/"\'\\`';
		$string     = '';

		if ($human_friendly) {
			$pool = $nice_chars;
		} else {
			$pool = $all_an;

			if ($include_symbols) {
				$pool .= $symbols;
			}
		}

		if (!$no_duplicate_chars) {
			return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
		}

		// Don't allow duplicate letters to be disabled if the length is
		// longer than the available characters
		if ($no_duplicate_chars && strlen($pool) < $length) {
			throw new \LengthException('$length exceeds the size of the pool and $no_duplicate_chars is enabled');
		}

		// Convert the pool of characters into an array of characters and
		// shuffle the array
		$pool       = str_split($pool);
		$poolLength = count($pool);
		$rand       = mt_rand(0, $poolLength - 1);

		// Generate our string
		for ($i = 0; $i < $length; $i++) {
			$string .= $pool[$rand];

			// Remove the character from the array to avoid duplicates
			array_splice($pool, $rand, 1);

			// Generate a new number
			if (($poolLength - 2 - $i) > 0) {
				$rand = mt_rand(0, $poolLength - 2 - $i);
			} else {
				$rand = 0;
			}
		}

		return $string;
	}

	/**
	 * Валидация email
	 *
	 * @param  string $possible_email An email address to validate
	 * @return bool
	 */
	public static function validate_email($possible_email) {
		return (bool) filter_var($possible_email, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * Оборачивает все ссылки в гиперссылки HTML.
	 *
	 * @param  string $text The string to parse
	 * @return string
	 */
	public static function linkify($text) {
		$text = preg_replace('/&apos;/', '&#39;', $text); // IE does not handle &apos; entity!
		$section_html_pattern = '%# Rev:20100913_0900 github.com/jmrware/LinkifyURL
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

		return preg_replace_callback($section_html_pattern, [__CLASS__, 'linkifyCallback'], $text);
	}

	/**
	 * Callback for the preg_replace in the linkify() method.
	 *
	 * Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>
	 *
	 * @param  array  $matches Matches from the preg_ function
	 * @return string
	 */
	protected static function linkifyRegex($text) {
		$url_pattern = '/# Rev:20100913_0900 github.com\/jmrware\/LinkifyURL
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

		$url_replace = '$1$4$7$10$13<a href="$2$5$8$11$14">$2$5$8$11$14</a>$3$6$9$12';

		return preg_replace($url_pattern, $url_replace, $text);
	}

	/**
	 * Callback for the preg_replace in the linkify() method.
	 *
	 * Part of the LinkifyURL Project <https://github.com/jmrware/LinkifyURL>
	 *
	 * @param  array  $matches Matches from the preg_ function
	 * @return string
	 */
	protected static function linkifyCallback($matches) {
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
	 * @return  string
	 */
	public static function safe_truncate($string, $length, $append = '...') {
		$ret        = substr($string, 0, $length);
		$last_space = strrpos($ret, ' ');

		if ($last_space !== false && $string != $ret) {
			$ret     = substr($ret, 0, $last_space);
		}

		if ($ret != $string) {
			$ret .= $append;
		}

		return $ret;
	}

	/**
	 * Truncate the string to given length of words.
	 *
	 * @param $string
	 * @param $limit
	 * @param string $append
	 * @return string
	 */
	public static function limit_words($string, $limit = 100, $append = '...') {
		preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $string, $matches);

		if (!isset($matches[0]) || strlen($string) === strlen($matches[0])) {
			return $string;
		}

		return rtrim($matches[0]).$append;
	}
}
