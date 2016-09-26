<?php

namespace efureev;

/**
 * Class uSecurity
 *
 * @package efureev
 */
class uSecurity
{

    public function generateRandomKey($length = 32)
    {
        if (!is_int($length)) {
            throw new \Exception('First parameter ($length) must be an integer');
        }

        if ($length < 1) {
            throw new \Exception('First parameter ($length) must be greater than 0');
        }

        // always use random_bytes() if it is available
        if (function_exists('random_bytes')) {
            return random_bytes($length);
        }

        $useLibreSSL = defined('OPENSSL_VERSION_TEXT')
            && preg_match('{^LibreSSL (\d\d?)\.(\d\d?)\.(\d\d?)$}', OPENSSL_VERSION_TEXT, $matches)
            && (10000 * $matches[1]) + (100 * $matches[2]) + $matches[3] >= 20105;

        // Since 5.4.0, openssl_random_pseudo_bytes() reads from CryptGenRandom on Windows instead
        // of using OpenSSL library. LibreSSL is OK everywhere but don't use OpenSSL on non-Windows.
        if ($useLibreSSL
            || (
                DIRECTORY_SEPARATOR !== '/'
                && substr_compare(PHP_OS, 'win', 0, 3, true) === 0
                && function_exists('openssl_random_pseudo_bytes')
            )
        ) {
            $key = openssl_random_pseudo_bytes($length, $cryptoStrong);
            if ($cryptoStrong === false) {
                throw new \Exception(
                    'openssl_random_pseudo_bytes() set $cryptoStrong false. Your PHP setup is insecure.'
                );
            }
            if ($key !== false && uString::byteLength($key) === $length) {
                return $key;
            }
        }

        // mcrypt_create_iv() does not use libmcrypt. Since PHP 5.3.7 it directly reads
        // CryptGenRandom on Windows. Elsewhere it directly reads /dev/urandom.
        if (function_exists('mcrypt_create_iv')) {
            $key = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
            if (uString::byteLength($key) === $length) {
                return $key;
            }
        }

        $randomFile = null;

        // If not on Windows, try to open a random device.
        if (DIRECTORY_SEPARATOR === '/') {
            // urandom is a symlink to random on FreeBSD.
            $device = PHP_OS === 'FreeBSD' ? '/dev/random' : '/dev/urandom';
            // Check random device for special character device protection mode. Use lstat()
            // instead of stat() in case an attacker arranges a symlink to a fake device.
            $lstat = @lstat($device);
            if ($lstat !== false && ($lstat['mode'] & 0170000) === 020000) {
                $randomFile = fopen($device, 'rb') ?: null;

                if (is_resource($randomFile)) {
                    // Reduce PHP stream buffer from default 8192 bytes to optimize data
                    // transfer from the random device for smaller values of $length.
                    // This also helps to keep future randoms out of user memory space.
                    $bufferSize = 8;

                    if (function_exists('stream_set_read_buffer')) {
                        stream_set_read_buffer($randomFile, $bufferSize);
                    }
                    // stream_set_read_buffer() isn't implemented on HHVM
                    if (function_exists('stream_set_chunk_size')) {
                        stream_set_chunk_size($randomFile, $bufferSize);
                    }
                }
            }
        }

        if (is_resource($randomFile)) {
            $buffer = '';
            $stillNeed = $length;
            while ($stillNeed > 0) {
                $someBytes = fread($randomFile, $stillNeed);
                if ($someBytes === false) {
                    break;
                }
                $buffer .= $someBytes;
                $stillNeed -= uString::byteLength($someBytes);
                if ($stillNeed === 0) {
                    // Leaving file pointer open in order to make next generation faster by reusing it.
                    return $buffer;
                }
            }
            fclose($randomFile);
            $randomFile = null;
        }

        throw new \Exception('Unable to generate a random key');
    }

    /**
     * Generates a random string of specified length.
     * The string generated matches [A-Za-z0-9_-]+ and is transparent to URL-encoding.
     *
     * @param integer $length the length of the key in characters
     * @return string the generated random key
     * @throws \Exception on failure.
     */
    public function generateRandomString($length = 32)
    {
        if (!is_int($length)) {
            throw new \Exception('First parameter ($length) must be an integer');
        }

        if ($length < 1) {
            throw new \Exception('First parameter ($length) must be greater than 0');
        }

        $bytes = $this->generateRandomKey($length);
        // '=' character(s) returned by base64_encode() are always discarded because
        // they are guaranteed to be after position $length in the base64_encode() output.
        return strtr(substr(base64_encode($bytes), 0, $length), '+/', '_-');
    }

}
