<?php

namespace uPhp\classes;
use uPhp\u;
/**
 * u.array
 * Класс для работы с массивами
 *
 * @author Eugene Fureev <efureev@yandex.ru>
 */
class uArray {

	/**
	 * Проверяет, является ли массив ассоциативным
	 *
	 * Является, если все ключи являются строками. Если `$allStrings` == false,
	 * то масссив является ассоциативным, если хотя бы один ключ является строкой.
	 *
	 * Note Пустой массив - не ассоциативен.
	 *
	 * @param array $array the array being checked
	 * @param boolean $allStrings whether the array keys must be all strings in order for
	 * the array to be treated as associative.
	 * @test: ok
	 * @return boolean whether the array is associative
	 */
	public static function isAssociative($array, $allStrings = true) {
		if (!is_array($array) || empty($array))
			return false;

		if ($allStrings) {
			foreach ($array as $key => $value) {
				if (!is_string($key))
					return false;
			}
			return true;
		} else {
			foreach ($array as $key => $value) {
				if (is_string($key))
					return true;
			}
			return false;
		}
	}

	/**
	 * Проверяет, является ли массив индексируемым
	 *
	 * Массив индексируем, если все ключи массива - цифры. Если `$consecutive` == true,
	 * то ключи массива будут последовательные и начинаться с 0.
	 *
	 * Note Пустой массив - индексируемый
	 *
	 * @param array $array the array being checked
	 * @param boolean $consecutive whether the array keys must be a consecutive sequence
	 * in order for the array to be treated as indexed.
	 * @test: ok
	 * @return boolean whether the array is associative
	 */
	public static function isIndexed(array $array, $consecutive = false) {
		if (!is_array($array))
			return false;

		if (empty($array))
			return true;

		if ($consecutive) {
			return array_keys($array) === range(0, count($array) - 1);
		} else {
			foreach ($array as $key => $value) {
				if (!is_integer($key))
					return false;
			}
			return true;
		}
	}


	/**
	 * Очищает массив от пустых значений, таких как: FALSE, 0, '0', '', null
	 * @param array $array
	 *
	 * @test: ok
	 * @return array
	 */
	public static function array_clean(array $array) {
		return array_filter($array);
	}

}
