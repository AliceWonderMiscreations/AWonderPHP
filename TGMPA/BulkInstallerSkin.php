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
 * Installer skin to set strings for the bulk plugin installations..
 *
 * Extends Bulk_Upgrader_Skin and customizes to suit the installation of multiple
 * plugins.
 *
 * @since 2.2.0
 *
 * {@internal Since 2.5.2 the class has been renamed from TGM_Bulk_Installer_Skin to
 *            TGMPA_Bulk_Installer_Skin.
 *            This was done to prevent backward compatibility issues with v2.3.6.}}
 *
 * @see https://core.trac.wordpress.org/browser/trunk/src/wp-admin/includes/class-wp-upgrader-skins.php
 *
 * Renamed to \AWonderPHP\TGMPA\BulkInstallerSkin for AWonderPHP PSR-4 autoloading.
 *
 * @package TGM-Plugin-Activation
 * @author  Thomas Griffin
 * @author  Gary Jones
 */
class BulkInstallerSkin extends \Bulk_Upgrader_Skin
{
    /**
     * Holds plugin info for each individual plugin installation.
     *
     * @since 2.2.0
     *
     * @var array
     */
    public $plugin_info = array();

    /**
     * Holds names of plugins that are undergoing bulk installations.
     *
     * @since 2.2.0
     *
     * @var array
     */
    public $plugin_names = array();

    /**
     * Integer to use for iteration through each plugin installation.
     *
     * @since 2.2.0
     *
     * @var integer
     */
    public $i = 0;

    /**
     * TGMPA instance
     *
     * @since 2.5.0
     *
     * @var object
     */
    protected $tgmpa;

    /**
     * Constructor. Parses default args with new ones and extracts them for use.
     *
     * @since 2.2.0
     *
     * @param array $args Arguments to pass for use within the class.
     */
    public function __construct($args = array())
    {
        // Get TGMPA class instance.
        $this->tgmpa = call_user_func(array( get_class($GLOBALS['tgmpa']), 'get_instance' ));

        // Parse default and new args.
        $defaults = array(
            'url'          => '',
            'nonce'        => '',
            'names'        => array(),
            'install_type' => 'install',
        );
        $args     = wp_parse_args($args, $defaults);

        // Set plugin names to $this->plugin_names property.
        $this->plugin_names = $args['names'];

        // Extract the new args.
        parent::__construct($args);
    }//end __construct()


    /**
     * Sets install skin strings for each individual plugin.
     *
     * Checks to see if the automatic activation flag is set and uses the
     * the proper strings accordingly.
     *
     * @since 2.2.0
     *
     * @return void
     */
    public function add_strings()
    {
        if ('update' === $this->options['install_type']) {
            parent::add_strings();
            /* translators: 1: plugin name, 2: action number 3: total number of actions. */
            $this->upgrader->strings['skin_before_update_header'] = __('Updating Plugin %1$s (%2$d/%3$d)', 'tgmpa');
        } else {
            /* translators: 1: plugin name, 2: error message. */
            $this->upgrader->strings['skin_update_failed_error'] = __('An error occurred while installing %1$s: <strong>%2$s</strong>.', 'tgmpa');
            /* translators: 1: plugin name. */
            $this->upgrader->strings['skin_update_failed'] = __('The installation of %1$s failed.', 'tgmpa');

            if ($this->tgmpa->is_automatic) {
                // Automatic activation strings.
                $this->upgrader->strings['skin_upgrade_start'] = __('The installation and activation process is starting. This process may take a while on some hosts, so please be patient.', 'tgmpa');
                /* translators: 1: plugin name. */
                $this->upgrader->strings['skin_update_successful'] = __('%1$s installed and activated successfully.', 'tgmpa');
                $this->upgrader->strings['skin_upgrade_end']       = __('All installations and activations have been completed.', 'tgmpa');
                /* translators: 1: plugin name, 2: action number 3: total number of actions. */
                $this->upgrader->strings['skin_before_update_header'] = __('Installing and Activating Plugin %1$s (%2$d/%3$d)', 'tgmpa');
            } else {
                // Default installation strings.
                $this->upgrader->strings['skin_upgrade_start'] = __('The installation process is starting. This process may take a while on some hosts, so please be patient.', 'tgmpa');
                /* translators: 1: plugin name. */
                $this->upgrader->strings['skin_update_successful'] = __('%1$s installed successfully.', 'tgmpa');
                $this->upgrader->strings['skin_upgrade_end']       = __('All installations have been completed.', 'tgmpa');
                /* translators: 1: plugin name, 2: action number 3: total number of actions. */
                $this->upgrader->strings['skin_before_update_header'] = __('Installing Plugin %1$s (%2$d/%3$d)', 'tgmpa');
            }

            // Add "read more" link only for WP < 4.8.
            if (version_compare($this->tgmpa->wp_version, '4.8', '<')) {
                $this->upgrader->strings['skin_update_successful'] .= ' <a href="#" class="hide-if-no-js" onclick="%2$s"><span>' . esc_html__('Show Details', 'tgmpa') . '</span><span class="hidden">' . esc_html__('Hide Details', 'tgmpa') . '</span>.</a>';
            }
        }
    }//end add_strings()


    /**
     * Outputs the header strings and necessary JS before each plugin installation.
     *
     * @since 2.2.0
     *
     * @param string $title Unused in this implementation.
     *
     * @return void
     */
    public function before($title = '')
    {
        if (empty($title)) {
            $title = esc_html($this->plugin_names[ $this->i ]);
        }
        parent::before($title);
    }//end before()


    /**
     * Outputs the footer strings and necessary JS after each plugin installation.
     *
     * Checks for any errors and outputs them if they exist, else output
     * success strings.
     *
     * @since 2.2.0
     *
     * @param string $title Unused in this implementation.
     *
     * @return void
     */
    public function after($title = '')
    {
        if (empty($title)) {
            $title = esc_html($this->plugin_names[ $this->i ]);
        }
        parent::after($title);

        $this->i++;
    }//end after()


    /**
     * Outputs links after bulk plugin installation is complete.
     *
     * @since 2.2.0
     *
     * @return void
     */
    public function bulk_footer()
    {
        // Serve up the string to say installations (and possibly activations) are complete.
        parent::bulk_footer();

        // Flush plugins cache so we can make sure that the installed plugins list is always up to date.
        wp_clean_plugins_cache();

        $this->tgmpa->show_tgmpa_version();

        // Display message based on if all plugins are now active or not.
        $update_actions = array();

        if ($this->tgmpa->is_tgmpa_complete()) {
            // All plugins are active, so we display the complete string and hide the menu to protect users.
            echo '<style type="text/css">#adminmenu .wp-submenu li.current { display: none !important; }</style>';
            $update_actions['dashboard'] = sprintf(
                esc_html($this->tgmpa->strings['complete']),
                '<a href="' . esc_url(self_admin_url()) . '">' . esc_html($this->tgmpa->strings['dashboard']) . '</a>'
            );
        } else {
            $update_actions['tgmpa_page'] = '<a href="' . esc_url($this->tgmpa->get_tgmpa_url()) . '" target="_parent">' . esc_html($this->tgmpa->strings['return']) . '</a>';
        }

        /**
         * Filter the list of action links available following bulk plugin installs/updates.
         *
         * @since 2.5.0
         *
         * @param array $update_actions Array of plugin action links.
         * @param array $plugin_info    Array of information for the last-handled plugin.
         */
        $update_actions = apply_filters('tgmpa_update_bulk_plugins_complete_actions', $update_actions, $this->plugin_info);

        if (! empty($update_actions)) {
            $this->feedback(implode(' | ', (array) $update_actions));
        }
    }//end bulk_footer()


                    /* *********** DEPRECATED METHODS *********** */

    /**
     * Flush header output buffer.
     *
     * @since      2.2.0
     * @deprecated 2.5.0 use {@see Bulk_Upgrader_Skin::flush_output()} instead
     * @see        Bulk_Upgrader_Skin::flush_output()
     *
     * @return void
     */
    public function before_flush_output()
    {
        _deprecated_function(__FUNCTION__, 'TGMPA 2.5.0', '\Bulk_Upgrader_Skin::flush_output()');
        $this->flush_output();
    }//end before_flush_output()


    /**
     * Flush footer output buffer and iterate $this->i to make sure the
     * installation strings reference the correct plugin.
     *
     * @since      2.2.0
     * @deprecated 2.5.0 use {@see Bulk_Upgrader_Skin::flush_output()} instead
     * @see        Bulk_Upgrader_Skin::flush_output()
     *
     * @return void
     */
    public function after_flush_output()
    {
        _deprecated_function(__FUNCTION__, 'TGMPA 2.5.0', '\Bulk_Upgrader_Skin::flush_output()');
        $this->flush_output();
        $this->i++;
    }//end after_flush_output()
}//end class