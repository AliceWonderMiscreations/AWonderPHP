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
    
    /**
     * A better way of creating CSRF tokens in WordPress.
     *
     * @param int $ttl       Optional. The time in seconds the nonce token is valid for.
     *                       Defaults to 3 hours. Admin functions should probably be
     *                       shorter.
     * @param string $action Optional. Defaults to 'generic'.
     *
     * @psalm-suppress UndefinedFunction
     *
     * @return string The 128-bit nonce token.
     */
    public static function csrfGenerateToken(int $ttl = 10800, string $action = 'generic'): string
    {
        $user = wp_get_current_user();
        $action = trim(strtolower($action));
        if (strlen($action) === 0) {
            $action = 'generic';
        }
        $nonce_type = $action . '_nonces';
        // TODO check to see if always created for non-logged in users
        $wp_session = \WP_Session::get_instance();
        if (! isset($wp_session[$nonce_type])) {
            $wp_session[$nonce_type] = array();
        }
        $nonce = self::generateNonce();
        $expires = time() + $ttl;
        $wp_session[$nonce_type][$nonce] = $expires;
        return $nonce;
    }//end csrfGenerateToken()

    
    /**
     * A better way of validating CSRF tokens in WordPress.
     *
     * @param string $nonce  The nonce to validate.
     * @param string $action Optional. Defaults to 'generic'.
     *
     * @psalm-suppress UndefinedFunction
     *
     * @return bool True if the nonce was valid, otherwise False.
     */
    public static function csrfValidateToken(string $nonce, string $action = 'generic'): bool
    {
        $user = wp_get_current_user();
        $action = trim(strtolower($action));
        if (strlen($action) === 0) {
            $action = 'generic';
        }
        $nonce_type = $action . '_nonces';
        // TODO check to see if always created for non-logged in users
        //  and destroyed upon logged in user logout
        $wp_session = \WP_Session::get_instance();
        if (! isset($wp_session[$nonce_type])) {
            return false;
        }
        if (! isset($wp_session[$nonce_type][$nonce])) {
            return false;
        }
        if (! is_numeric($wp_session[$nonce_type][$nonce])) {
            return false;
        }
        $expires = intval($wp_session[$nonce_type][$nonce], 10);
        if ($expires < time()) {
            return false;
        }
        // It's valid, so expire the nonce and return true
        $wp_session[$nonce_type][$nonce] = 0;
        return true;
    }//end csrfValidateToken()
}//end class

?>