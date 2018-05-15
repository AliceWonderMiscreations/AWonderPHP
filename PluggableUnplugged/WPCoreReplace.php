<?php
declare(strict_types=1);

/**
 * Static methods intended to be used in place of WP core functions.
 *
 * @package AWonderPHP
 * @author  Alice Wonder <paypal@domblogger.net>
 * @license https://opensource.org/licenses/MIT MIT
 * @version 0.32
 * @link    https://github.com/AliceWonderMiscreations/AWonderPHP
 */

namespace AWonderPHP\PluggableUnplugged;

/**
 * Misc static methods that are of use to WordPress plugins
 */

class WPCoreReplace
{
  
  /**
     * A substitute for the WordPress add_query_arg function.
     * This function does NOT support user, pass, or fragment.
     *
     * @param string      $url              The url to modify.
     * @param array       $addQueryArgs     Optional. An array of key value pairs.
     * @param array       $removeQueryArgs  Optional. An array of query args to remove from $url, only key matters.
     * @param null|string $scheme           Optional. The scheme to use. Only supports http or https.
     *
     * @return null|string The url with query args added, or null on failure.
     */
    public static function modifyQueryArgs(string $url, array $addQueryArgs = array(), array $removeQueryArgs = array(), $scheme = null)
    {
        $parsed = parse_url($url);
        if (! is_null($scheme)) {
            $scheme = strtolower($scheme);
            if (in_array($scheme, array('http', 'https'))) {
                $parsed['scheme'] = $scheme;
            }
        }
        // todo - throw exception when host is missing
        
        if (isset($parsed['query'])) {
            $queryArray = explode('&', $parsed['query']);
        } else {
            $queryArray = array();
        }
        $newQueryArray = array();
        foreach ($queryArray as $string) {
            $keypair = explode('=', $string);
            $key = $keypair[0];
            $value = $keypair[1];
            if (! in_array($key, $removeQueryArgs)) {
                $newQueryArray[$key] = $value;
            }
        }
        
        foreach ($addQueryArgs as $key => $value) {
            if (! is_bool($value)) {
                $newQueryArray[$key] = $value;
            }
        }
        if (count($newQueryArray) > 0) {
            $arr = array();
            foreach ($newQueryArray as $key => $value) {
                $arr[] = $key . '=' . $value;
            }
            $parsed['query'] = implode('&', $arr);
        }
        $url = '';
        $realurl = '';
        if (isset($parsed['scheme'])) {
            $url = $parsed['scheme'] . '://';
            $realurl = $parsed['scheme'] . '://';
        }
        $url = $url . \AWonderPHP\PluggableUnplugged\PunycodeStatic::punycodeDomain($parsed['host']);
        $realurl = $realurl . $parsed['host'];
        if (isset($parsed['port'])) {
            $url = $url . ':' . $parsed['port'];
            $realurl = $realurl . ':' . $parsed['port'];
        }
        if (! isset($parsed['path'])) {
            $parsed['path'] = '/';
        }
        $url = $url . $parsed['path'];
        $realurl = $realurl . $parsed['path'];
        if (count($newQueryArray) > 0) {
            $url = $url . '?' . $parsed['query'];
            $realurl = $realurl . '?' . $parsed['query'];
        }
        if ($test = filter_var($url, FILTER_VALIDATE_URL)) {
            return $realurl;
        }
        return null;
    }//end modifyQueryArgs()
}//end class

?>