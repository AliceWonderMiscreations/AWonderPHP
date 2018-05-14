<?php
declare(strict_types=1);

/**
 * Misc static methods that WP plugins can use.
 *
 * @package AWonderPHP
 * @author  Alice Wonder <paypal@domblogger.net>
 * @license https://opensource.org/licenses/MIT MIT
 * @version 0.32
 * @link    https://github.com/AliceWonderMiscreations/AWonderPHP
 */

namespace AWonderPHP\PluggableUnplugged;

/**
 * Misc static methods that are of use to WordPress plugins.
 */

class Misc
{

    /**
     * Creates a nonce that is at least 16 bytes. If a smaller nonce is requested it
     * will return a 16 byte nonce.
     *
     * @param int $bytes The size in bytes of the requested nonce.
     *
     * @return string The base64 encoded nonce.
     */
    public static function generateNonce(int $bytes = 16): string
    {
        if ($bytes < 16) {
            $bytes = 16;
        }
        $raw = random_bytes($bytes);
        return base64_encode($raw);
    }//end generateNonce()

    /**
     * Creates a cryptographically strong 256 bit salt.
     *
     * @return string The generated salt, a base64 encoded string.
     */
    public static function saltShaker(): string
    {
        $raw = random_bytes(32);
        return base64_encode($raw);
    }//end saltShaker()
}//end class

?>