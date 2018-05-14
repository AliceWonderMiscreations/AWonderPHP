<?php
declare(strict_types=1);

/**
 * Static methods related to punycode.
 *
 * @package AWonderPHP
 * @author  Alice Wonder <paypal@domblogger.net>
 * @license https://opensource.org/licenses/MIT MIT
 * @version 0.32
 * @link    https://github.com/AliceWonderMiscreations/AWonderPHP
 */

namespace AWonderPHP\PluggableUnplugged;

/**
 * Static punycode methods that are of use to WordPress plugins
 */

class PunycodeStatic
{
    /**
     * Takes a valid domain name and returns punycode variant, assuming the
     * idn_to_ascii function is available.
     *
     * GIGO function, invalid domain name will not throw exception.
     *
     * @param string $domain The domain name to translate into punycode.
     *
     * @return string The ascii punycode version of the domain name.
     */
    public static function punycodeDomain(string $domain): string
    {
        if (function_exists('idn_to_ascii')) {
            $domain=idn_to_ascii($domain, 0, INTL_IDNA_VARIANT_UTS46);
        }
        return $domain;
    }//end punycodeDomain()

    /**
     * Takes a valid ascii domain name and returns UTF-8 variant, assuming the
     * idn_to_utf8 function is available.
     *
     * GIGO function, invalid domain name will not throw exception.
     *
     * @param string $domain The domain name to translate into utf8.
     *
     * @return string The utf8 variant of the domain name.
     */
    public static function unpunycodeDomain(string $domain): string
    {
        if (function_exists('idn_to_utf8')) {
            $domain=idn_to_utf8($domain, 0, INTL_IDNA_VARIANT_UTS46);
        }
        return $domain;
    }//end unpunycodeDomain()
    
    /**
     * Takes a valid international e-mail address and return user@ punycode variant,
     * assuming idn_to_ascii is available.
     *
     * GIGO function, invalid e-mail will not throw exception.
     *
     * @param string $email The e-maill address with domain name to translate into utf8.
     *
     * @return string The e-mail with ascii variant of the domain name
     */
    public static function punycodeEmail(string $email): string
    {
        if (substr_count($email, '@') === 1) {
            $tmp=explode('@', $email);
            $email=$tmp[0] . '@' . self::punycodeDomain($tmp[1]);
        }
        return $email;
    }//end punycodeEmail()
}//end class

?>