<?php
declare(strict_types=1);

/**
 * Replacements for some (not all) of the WordPress pluggable.php functions.
 *
 * @package AWonderPHP
 * @author  Alice Wonder <paypal@domblogger.net>
 * @license https://opensource.org/licenses/MIT MIT
 * @version 0.32
 * @link    https://github.com/AliceWonderMiscreations/AWonderPHP
 */

namespace AWonderPHP\PluggableUnplugged;

/**
 * Static methods of use to pluggable functions and other WordPress plugins
 */
class UnpluggedStatic
{

    /**
     * For use with `wp_hash()` pluggable function. Creates a secure hash string of the
     * specified number of bytes.
     *
     * If the specified number of bytes is < SODIUM_CRYPTO_GENERICHASH_BYTES_MIN
     * then SODIUM_CRYPTO_GENERICHASH_BYTES_MIN is used.
     *
     * If the specified number of bytes is > SODIUM_CRYPTO_GENERICHASH_BYTES_MAX
     * then SODIUM_CRYPTO_GENERICHASH_BYTES_MAX is used.
     *
     * If the specified number of bytes us null then SODIUM_CRYPTO_GENERICHASH_BYTES
     * is used.
     *
     * @param string $data  The string to be hashed.
     * @param string $salt  The key (salt) to be used.
     * @param int    $bytes Optional. The length in bytes for the hash. Defaults to
     *                      SODIUM_CRYPTO_GENERICHASH_BYTES.
     *
     * @return string      The base64 encoded hash
     */
    public static function cryptoHash(string $data, string $salt, int $bytes = 0): string
    {
        if ($bytes === 0) {
            $bytes = SODIUM_CRYPTO_GENERICHASH_BYTES;
        }
        if ($bytes > SODIUM_CRYPTO_GENERICHASH_BYTES_MAX) {
            $bytes = SODIUM_CRYPTO_GENERICHASH_BYTES_MAX;
        }
        if ($bytes < SODIUM_CRYPTO_GENERICHASH_BYTES_MIN) {
            $bytes = SODIUM_CRYPTO_GENERICHASH_BYTES_MIN;
        }
        // We have to do this because the WP supplied salt may not actually
        // be suitable key
        $key = hash('sha256', $salt, true);
        $raw = sodium_crypto_generichash($data, $key, $bytes);
        sodium_memzero($salt);
        sodium_memzero($key);
        return base64_encode($raw);
    }//end cryptoHash()

    /**
     * For use with `wp_rand()` pluggable function. Generates a random integer.
     *
     * @param int $min Optional. The lower limit inclusive.
     * @param int $max Optional. The max limit inclusive.
     *
     * @return int The random number between min and max inclusive
     */
    public static function safeRandInt(int $min = 0, int $max = 0): int
    {
        if ($min > $max) {
            $tmp = $min;
            $min = $max;
            $max = $tmp;
        }
        return random_int($min, $max);
    }//end safeRandInt()

    /**
     * For use with `wp_generate_password()` pluggable function. Generates a random password drawn
     * from the defined set of characters. Always generates a password at least 12 characters long.
     *
     * @param int  $length              Optional. The length of the password. Defaults to 16.
     * @param bool $special_chars       Optional. Whether to include standard special characters.
     *                                  Default True.
     * @param bool $extra_special_chars Optional. Whether to include other special characters.
     *                                  Default False.
     *
     * @return string The generated password.
     */
    // @codingStandardsIgnoreLine
    public static function generatePassword(int $length = 16, bool $special_chars = true, bool $extra_special_chars = false): string
    {
        if ($length < 12) {
            $length = 12;
        }
        if ($length > 255) {
            $length = 255;
        }
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if ($special_chars) {
            $alphabet .= '!@#$%^&*()';
        }
        if ($extra_special_chars) {
            $alphabet .= '-_ []{}<>~`+=,.;:/?|';
        }
        $alphabet = str_shuffle($alphabet);
        $max = (strlen($alphabet) - 1);
        $password = '';
        for ($i=0; $i< $length; $i++) {
            $rnd = self::safeRandInt(0, $max);
            $password .= $alphabet[$rnd];
        }
        return $password;
    }//end generatePassword()

    /**
     * For use with `wp_hash_password()` pluggable function. Create a hash (encrypt)
     * of a plain text password.
     *
     * @param string $password The plain text password.
     *
     * @return string The hash of the plain text password.
     */
    public static function hashPassword(string $password): string
    {
        if (defined('PASSWORD_SALT')) {
            // prehash the password
            $key = hash('sha256', PASSWORD_SALT, true);
            $raw = sodium_crypto_generichash($password, $key, 64);
            $password = base64_encode($raw);
            sodium_memzero($key);
            sodium_memzero($raw);
        }
        $hash_str = sodium_crypto_pwhash_str(
            $password,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
        );
        sodium_memzero($password);
        return $hash_str;
    }//end hashPassword()

    /**
     * For use with `wp_check_password()` pluggable function. Checks plain text against encrypted.
     *
     * @param string $password The plain text password.
     * @param string $hash     The hash to check against.
     *
     * @return bool True on valid, False on failure.
     */
    public static function checkPassword(string $password, string $hash): bool
    {
        $return = false;
        if (defined('PASSWORD_SALT')) {
            $key = hash('sha256', PASSWORD_SALT, true);
            $raw = sodium_crypto_generichash($password, $key, 64);
            $testpassword = base64_encode($raw);
            if (sodium_crypto_pwhash_str_verify($hash, $testpassword)) {
                $return = true;
            }
            sodium_memzero($key);
            sodium_memzero($raw);
            sodium_memzero($testpassword);
        }
        if (! $return) {
            if (sodium_crypto_pwhash_str_verify($hash, $password)) {
                $return = true;
            }
        }
        sodium_memzero($password);
        return $return;
    }//end checkPassword()
}//end class

?>