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
     * @param string $path
     *
     * @return string
     */
    public static function getBase($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function getFilename($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function getDirname($path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
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

    /**
     * Returns the file permissions as a nice string, like -rw-r--r-- or false if the file is not found.
     *
     * @param   string $file  The name of the file to get permissions form
     * @param null|int $perms Numerical value of permissions to display as text.
     *
     * @return string
     */
    public static function perms($file, $perms = null)
    {
        if (null === $perms) {
            if (!file_exists($file)) {
                return false;
            }
            $perms = fileperms($file);
        }
        //@codeCoverageIgnoreStart
        if (($perms & 0xC000) == 0xC000) { // Socket
            $info = 's';
        } elseif (($perms & 0xA000) == 0xA000) { // Symbolic Link
            $info = 'l';
        } elseif (($perms & 0x8000) == 0x8000) { // Regular
            $info = '-';
        } elseif (($perms & 0x6000) == 0x6000) { // Block special
            $info = 'b';
        } elseif (($perms & 0x4000) == 0x4000) { // Directory
            $info = 'd';
        } elseif (($perms & 0x2000) == 0x2000) { // Character special
            $info = 'c';
        } elseif (($perms & 0x1000) == 0x1000) { // FIFO pipe
            $info = 'p';
        } else { // Unknown
            $info = 'u';
        }
        //@codeCoverageIgnoreEnd
        // Owner
        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));
        // Group
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));
        // All
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));

        return $info;
    }


    /**
     * Normal path to file
     * - Convert all separators to `DIRECTORY_SEPARATOR` (e.g. "\a/b\c" becomes "/a/b/c")
     * - Remove last separator (e.g. "/a/b/c/" becomes "/a/b/c")
     * - Remove multi-slash (e.g. "/a///b/c" becomes "/a/b/c")
     * - Remove ".." и "." (e.g. "/a/./b/../c" becomes "/a/c")
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

    /**
     * Check is current path directory
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isDir($path)
    {
        $path = self::normalizePath($path);

        return is_dir($path);
    }

    /**
     * Check is current path regular file
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isFile($path)
    {
        $path = self::normalizePath($path);

        return file_exists($path) && is_file($path);
    }

    /**
     * Returns all paths inside a directory.
     *
     * @param string $dir
     *
     * @return array
     */
    public static function ls($dir)
    {
        $contents = [];
        $flags = \FilesystemIterator::KEY_AS_PATHNAME
            | \FilesystemIterator::CURRENT_AS_FILEINFO
            | \FilesystemIterator::SKIP_DOTS;
        $dirIter = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, $flags));
        foreach ($dirIter as $path => $fi) {
            $contents[] = $path;
        }
        natsort($contents);

        return $contents;
    }

    /**
     * Returns size of a given directory in bytes.
     *
     * @param string $dir
     *
     * @return integer
     */
    public static function dirSize($dir)
    {
        $size = 0;
        $flags = \FilesystemIterator::CURRENT_AS_FILEINFO
            | \FilesystemIterator::SKIP_DOTS;
        $dirIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, $flags));
        foreach ($dirIterator as $key) {
            if ($key->isFile()) {
                $size += $key->getSize();
            }
        }

        return $size;
    }


}
