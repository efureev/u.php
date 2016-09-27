<?php

namespace uPhp\classes;
/**
 * u.array
 * Класс для работы со файловой системой
 *
 * @author Eugene Fureev <efureev@yandex.ru>
 */
class uFS
{

    /**
     * Возвращает пути всех файлов и папок, находящихся в директории
     *
     * @param string $dir
     *
     * @return array
     */
    public static function dirContent($dir)
    {
        $contents = [];
        foreach (new \RecursiveIteratorIterator(
                     new \RecursiveDirectoryIterator(
                         $dir,
                         \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS
                     )
                 ) as $pathname => $fi) {
            $contents[] = $pathname;
        }
        natsort($contents);

        return $contents;
    }

    /**
     * Возвращает размер директории в байтах
     *
     * @param string $dir
     *
     * @return integer
     */
    public static function dirSize($dir)
    {
        $size = 0;
        foreach (new \RecursiveIteratorIterator(
                     new \RecursiveDirectoryIterator(
                         $dir,
                         \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS
                     )
                 ) as $key) {
            if ($key->isFile()) {
                $size += $key->getSize();
            }
        }

        return $size;
    }



    /**
     * Удаляет дирректорию рекурсивно (включая симлинки).
     *
     * @param  string $dir              директория для удаления
     * @param  bool   $traverseSymlinks Удалять содержимое симЛинков рекурсивно
     *
     * @return bool
     * @throws \RuntimeException
     */
    public static function dirRemove($dir, $traverseSymlinks = false)
    {
        if (!file_exists($dir)) {
            return true;
        } elseif (!is_dir($dir)) {
            throw new \RuntimeException('Given path is not a directory');
        }

        if (!is_link($dir) || $traverseSymlinks) {
            foreach (scandir($dir) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $currentPath = $dir . '/' . $file;

                if (is_dir($currentPath)) {
                    static::dirRemove($currentPath, $traverseSymlinks);
                } elseif (!unlink($currentPath)) {
                    // @codeCoverageIgnoreStart
                    throw new \RuntimeException('Unable to delete ' . $currentPath);
                    // @codeCoverageIgnoreEnd
                }
            }
        }

        // Windows treats removing directory symlinks identically to removing directories.
        if (is_link($dir) && !defined('PHP_WINDOWS_VERSION_MAJOR')) {
            if (!unlink($dir)) {
                // @codeCoverageIgnoreStart
                throw new \RuntimeException('Unable to delete ' . $dir);
                // @codeCoverageIgnoreEnd
            }
        } else {
            if (!rmdir($dir)) {
                // @codeCoverageIgnoreStart
                throw new \RuntimeException('Unable to delete ' . $dir);
                // @codeCoverageIgnoreEnd
            }
        }

        return true;
    }

    /**
     * Создание директории
     * This method is similar to the PHP `mkdir()` function except that
     * it uses `chmod()` to set the permission of the created directory
     * in order to avoid the impact of the `umask` setting.
     *
     * @param string  $path      path of the directory to be created.
     * @param integer $mode      the permission to be set for the created directory.
     * @param boolean $recursive whether to create parent directories if they do not exist.
     *
     * @return boolean whether the directory is created successfully
     */
    public static function dirCreate($path, $mode = 0775, $recursive = true)
    {
        if (is_dir($path)) {
            return true;
        }
        $parentDir = dirname($path);
        if ($recursive && !is_dir($parentDir)) {
            static::dirCreate($parentDir, $mode, true);
        }
        $result = mkdir($path, $mode);
        chmod($path, $mode);

        return $result;
    }




    /**
     * Возвращает нормализованый путь к файлу/папке.
     * - Конвертирует все разделители в `DIRECTORY_SEPARATOR` (e.g. "\a/b\c" becomes "/a/b/c")
     * - Удаляет завершающий разделитель (e.g. "/a/b/c/" becomes "/a/b/c")
     * - Удаляет мульти-слеш (e.g. "/a///b/c" becomes "/a/b/c")
     * - Удаляет ".." и "." (e.g. "/a/./b/../c" becomes "/a/c")
     *
     * @param string $path  the file/directory path to be normalized
     * @param string $slash the directory separator to be used in the normalized result. Defaults to
     *                      `DIRECTORY_SEPARATOR`.
     *
     * @return string the normalized file/directory path
     */
    public static function normalizePath($path, $slash = DIRECTORY_SEPARATOR)
    {
        $path = rtrim(strtr($path, '/\\', $slash . $slash), $slash);
        if (strpos($slash . $path, "{$slash}.") === false && strpos($path, "{$slash}{$slash}") === false) {
            return $path;
        }
        // the path may contain ".", ".." or double slashes, need to clean them up
        $parts = [];
        foreach (explode($slash, $path) as $part) {
            if ($part === '..' && !empty($parts) && end($parts) !== '..') {
                array_pop($parts);
            } elseif ($part === '.' || $part === '' && !empty($parts)) {
                continue;
            } else {
                $parts[] = $part;
            }
        }
        $path = implode($slash, $parts);

        return $path === '' ? '.' : $path;
    }

}
