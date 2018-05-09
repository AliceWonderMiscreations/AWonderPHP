<?php
declare(strict_types=1);

/**
 * Plugin installation and activation for WordPress themes.
 *
 * Please note that this is a drop-in library for a theme or plugin.
 * The authors of this library (Thomas, Gary and Juliette) are NOT responsible
 * for the support of your plugin or theme. Please contact the plugin
 * or theme author for support.
 *
 * @package   TGM-Plugin-Activation
 * @author    Thomas Griffin <username@example.org>
 * @author    Gary Jones <username@example.org>
 * @author    Juliette Reinders Folmer <username@example.org>
 * @license   https://opensource.org/licenses/gpl-2.0.php GPL-2.0+
 * @link      http://tgmpluginactivation.com/
 * @version   2.6.1
 * @copyright Copyright (c) 2011, Thomas Griffin
 */

/*
    Copyright 2011 Thomas Griffin (thomasgriffinmedia.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
  Modified by Alice Wonder Miscreations for use with a PSR-4 autoloader
  Represents lines 3553-3782 from original
*/

namespace AWonderPHP\TGMPA;

/**
 * Generic utilities for TGMPA.
 *
 * All methods are static, poor-dev name-spacing class wrapper.
 *
 * Class was called TGM_Utils in 2.5.0 but renamed TGMPA_Utils in 2.5.1 as this was conflicting with Soliloquy.
 *
 * Renamed to \AWonderPHP\TGMPA\Utils for AWonderPHP PSR-4 autoloading.
 *
 * @since 2.5.0
 *
 * @package TGM-Plugin-Activation
 * @author  Juliette Reinders Folmer
 */
class Utils
{
    /**
     * Whether the PHP filter extension is enabled.
     *
     * @see http://php.net/book.filter
     *
     * @since 2.5.0
     *
     * @static
     *
     * @var bool $has_filters True is the extension is enabled.
     */
    public static $has_filters;

    /**
     * Wrap an arbitrary string in <em> tags. Meant to be used in combination with array_map().
     *
     * Renamed to wrapInEmph for PSR2.
     *
     * @since 2.5.0
     *
     * @static
     *
     * @param string $string Text to be wrapped.
     *
     * @return string
     */
    public static function wrapInEmph($string): string
    {
        return '<em>' . wp_kses_post($string) . '</em>';
    }//end wrapInEmph()

    /**
     * Wrap an arbitrary string in <strong> tags. Meant to be used in combination with array_map().
     *
     * Renamed to wrapInStrong for PSR2.
     *
     * @since 2.5.0
     *
     * @static
     *
     * @param string $string Text to be wrapped.
     *
     * @return string
     */
    public static function wrapInStrong($string): string
    {
        return '<strong>' . wp_kses_post($string) . '</strong>';
    }//end wrapInStrong()

    /**
     * Helper function: Validate a value as boolean
     *
     * @since 2.5.0
     *
     * @static
     *
     * @param mixed $value Arbitrary value.
     *
     * @return bool
     */
    public static function validateBool($value): bool
    {
        if (! isset(self::$has_filters)) {
            self::$has_filters = extension_loaded('filter');
        }

        if (self::$has_filters) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        } else {
            return self::emulateFilterBool($value);
        }
    }//end validateBool()

    /**
     * Helper function: Cast a value to bool
     *
     * Raname to emulateFilterBool for PSR2.
     *
     * @since 2.5.0
     *
     * @static
     *
     * @param mixed $value Value to cast.
     *
     * @return bool
     */
    protected static function emulateFilterBool($value): bool
    {
        // @codingStandardsIgnoreStart
        static $true  = array(
            '1',
            'true', 'True', 'TRUE',
            'y', 'Y',
            'yes', 'Yes', 'YES',
            'on', 'On', 'ON',
        );
        static $false = array(
            '0',
            'false', 'False', 'FALSE',
            'n', 'N',
            'no', 'No', 'NO',
            'off', 'Off', 'OFF',
        );
        // @codingStandardsIgnoreEnd

        if (is_bool($value)) {
            return $value;
        } elseif (is_int($value) && ( 0 === $value || 1 === $value )) {
            return (bool) $value;
        } elseif (( is_float($value) && ! is_nan($value) ) && ( (float) 0 === $value || (float) 1 === $value )) {
            return (bool) $value;
        } elseif (is_string($value)) {
            $value = trim($value);
            if (in_array($value, $true, true)) {
                return true;
            } elseif (in_array($value, $false, true)) {
                return false;
            } else {
                return false;
            }
        }

        return false;
    }//end emulateFilterBool()
}//end class

?>