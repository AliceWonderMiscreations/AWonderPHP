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
  Represents lines 3220-3549 from original
*/

namespace AWonderPHP\TGMPA;

if (! class_exists('\Plugin_Upgrader', false)) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
}

/**
 * Installer class to handle bulk plugin installations.
 *
 * Extends WP_Upgrader and customizes to suit the installation of multiple
 * plugins.
 *
 * @since 2.2.0
 *
 * {@internal Since 2.5.0 the class is an extension of Plugin_Upgrader rather than WP_Upgrader.}}
 * {@internal Since 2.5.2 the class has been renamed from TGM_Bulk_Installer to TGMPA_Bulk_Installer.
 *            This was done to prevent backward compatibility issues with v2.3.6.}}
 *
 * Renamed to \AWonderPHP\TGMPA\BulkInstaller for AWonderPHP PSR-4 autoloading.
 *
 * @package TGM-Plugin-Activation
 * @author  Thomas Griffin
 * @author  Gary Jones
 */
class BulkInstaller extends Plugin_Upgrader
{
    /**
     * Holds result of bulk plugin installation.
     *
     * @since 2.2.0
     *
     * @var string
     */
    public $result;

    /**
     * Flag to check if bulk installation is occurring or not.
     *
     * @since 2.2.0
     *
     * @var boolean
     */
    public $bulk = false;

    /**
     * TGMPA instance
     *
     * @since 2.5.0
     *
     * @var object
     */
    protected $tgmpa;

    /**
     * Whether or not the destination directory needs to be cleared ( = on update).
     *
     * @since 2.5.0
     *
     * @var bool
     */
    protected $clear_destination = false;

    /**
     * References parent constructor and sets defaults for class.
     *
     * @since 2.2.0
     *
     * @param \Bulk_Upgrader_Skin|null $skin Installer skin.
     */
    public function __construct($skin = null)
    {
        // Get TGMPA class instance.
        $this->tgmpa = call_user_func(array( get_class($GLOBALS['tgmpa']), 'get_instance' ));

        parent::__construct($skin);

        if (isset($this->skin->options['install_type']) && 'update' === $this->skin->options['install_type']) {
            $this->clear_destination = true;
        }

        if ($this->tgmpa->is_automatic) {
            $this->activate_strings();
        }

        add_action('upgrader_process_complete', array( $this->tgmpa, 'populate_file_path' ));
    }//end __construct()


    /**
     * Sets the correct activation strings for the installer skin to use.
     *
     * @since 2.2.0
     *
     * @return void
     */
    public function activate_strings()
    {
        $this->strings['activation_failed']  = __('Plugin activation failed.', 'tgmpa');
        $this->strings['activation_success'] = __('Plugin activated successfully.', 'tgmpa');
    }//end activate_strings()


    /**
     * Performs the actual installation of each plugin.
     *
     * @since 2.2.0
     *
     * @see WP_Upgrader::run()
     *
     * @param array $options The installation config options.
     *
     * @return null|array Return early if error, array of installation data on success.
     */
    public function run($options)
    {
        $result = parent::run($options);

        // Reset the strings in case we changed one during automatic activation.
        if ($this->tgmpa->is_automatic) {
            if ('update' === $this->skin->options['install_type']) {
                $this->upgrade_strings();
            } else {
                $this->install_strings();
            }
        }

        return $result;
    }//end run()


    /**
     * Processes the bulk installation of plugins.
     *
     * @since 2.2.0
     *
     * {@internal This is basically a near identical copy of the WP Core
     * Plugin_Upgrader::bulk_upgrade() method, with minor adjustments to deal with
     * new installs instead of upgrades.
     * For ease of future synchronizations, the adjustments are clearly commented, but no other
     * comments are added. Code style has been made to comply.}}
     *
     * @see Plugin_Upgrader::bulk_upgrade()
     * @see https://core.trac.wordpress.org/browser/tags/4.2.1/src/wp-admin/includes/class-wp-upgrader.php#L838
     * (@internal Last synced: Dec 31st 2015 against https://core.trac.wordpress.org/browser/trunk?rev=36134}}
     *
     * @param array $plugins The plugin sources needed for installation.
     * @param array $args    Arbitrary passed extra arguments.
     *
     * @return array|false   Install confirmation messages on success, false on failure.
     */
    public function bulk_install($plugins, $args = array())
    {
        // [TGMPA + ] Hook auto-activation in.
        add_filter('upgrader_post_install', array( $this, 'auto_activate' ), 10);

        $defaults    = array(
            'clear_update_cache' => true,
        );
        $parsed_args = wp_parse_args($args, $defaults);

        $this->init();
        $this->bulk = true;

        $this->install_strings(); // [TGMPA + ] adjusted.

        /* [TGMPA - ] $current = get_site_transient( 'update_plugins' ); */

        /* [TGMPA - ] add_filter('upgrader_clear_destination', array($this, 'delete_old_plugin'), 10, 4); */

        $this->skin->header();

        // Connect to the Filesystem first.
        $res = $this->fs_connect(array( WP_CONTENT_DIR, WP_PLUGIN_DIR ));
        if (! $res) {
            $this->skin->footer();
            return false;
        }

        $this->skin->bulk_header();

        /*
         * Only start maintenance mode if:
         * - running Multisite and there are one or more plugins specified, OR
         * - a plugin with an update available is currently active.
         * @TODO: For multisite, maintenance mode should only kick in for individual sites if at all possible.
         */
        $maintenance = ( is_multisite() && ! empty($plugins) );

        /*
        [TGMPA - ]
        foreach ( $plugins as $plugin )
            $maintenance = $maintenance || ( is_plugin_active( $plugin ) && isset( $current->response[ $plugin] ) );
        */
        if ($maintenance) {
            $this->maintenance_mode(true);
        }

        $results = array();

        $this->update_count   = count($plugins);
        $this->update_current = 0;
        foreach ($plugins as $plugin) {
            $this->update_current++;

            /*
            [TGMPA - ]
            $this->skin->plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin, false, true);

            if ( !isset( $current->response[ $plugin ] ) ) {
                $this->skin->set_result('up_to_date');
                $this->skin->before();
                $this->skin->feedback('up_to_date');
                $this->skin->after();
                $results[$plugin] = true;
                continue;
            }

            // Get the URL to the zip file.
            $r = $current->response[ $plugin ];

            $this->skin->plugin_active = is_plugin_active($plugin);
            */

            $result = $this->run(
                array(
                    'package'           => $plugin, // [TGMPA + ] adjusted.
                    'destination'       => WP_PLUGIN_DIR,
                    'clear_destination' => false, // [TGMPA + ] adjusted.
                    'clear_working'     => true,
                    'is_multi'          => true,
                    'hook_extra'        => array(
                        'plugin' => $plugin,
                    ),
                )
            );

            $results[ $plugin ] = $this->result;

            // Prevent credentials auth screen from displaying multiple times.
            if (false === $result) {
                break;
            }
        }

        $this->maintenance_mode(false);

        /**
         * Fires when the bulk upgrader process is complete.
         *
         * @since WP 3.6.0 / TGMPA 2.5.0
         *
         * @param Plugin_Upgrader $this Plugin_Upgrader instance. In other contexts, $this, might
         *                              be a Theme_Upgrader or Core_Upgrade instance.
         * @param array           $data {
         *     Array of bulk item update data.
         *
         *     @type string $action   Type of action. Default 'update'.
         *     @type string $type     Type of update process. Accepts 'plugin', 'theme', or 'core'.
         *     @type bool   $bulk     Whether the update process is a bulk update. Default true.
         *     @type array  $packages Array of plugin, theme, or core packages to update.
         * }
         */
        do_action( // WPCS: prefix OK.
            'upgrader_process_complete',
            $this,
            array(
                'action'  => 'install', // [TGMPA + ] adjusted.
                'type'    => 'plugin',
                'bulk'    => true,
                'plugins' => $plugins,
            )
        );

        $this->skin->bulk_footer();

        $this->skin->footer();

        // Cleanup our hooks, in case something else does a upgrade on this connection.
        /* [TGMPA - ] remove_filter('upgrader_clear_destination', array($this, 'delete_old_plugin')); */

        // [TGMPA + ] Remove our auto-activation hook.
        remove_filter('upgrader_post_install', array( $this, 'auto_activate' ), 10);

        // Force refresh of plugin update information.
        wp_clean_plugins_cache($parsed_args['clear_update_cache']);

        return $results;
    }//end bulk_install()


    /**
     * Handle a bulk upgrade request.
     *
     * @since 2.5.0
     *
     * @see Plugin_Upgrader::bulk_upgrade()
     *
     * @param array $plugins The local WP file_path's of the plugins which should be upgraded.
     * @param array $args    Arbitrary passed extra arguments.
     *
     * @return string|bool Install confirmation messages on success, false on failure.
     */
    public function bulk_upgrade($plugins, $args = array())
    {

        add_filter('upgrader_post_install', array( $this, 'auto_activate' ), 10);

        $result = parent::bulk_upgrade($plugins, $args);

        remove_filter('upgrader_post_install', array( $this, 'auto_activate' ), 10);

        return $result;
    }//end bulk_upgrade()


    /**
     * Abuse a filter to auto-activate plugins after installation.
     *
     * Hooked into the 'upgrader_post_install' filter hook.
     *
     * @since 2.5.0
     *
     * @param bool $bool The value we need to give back (true).
     *
     * @return bool
     */
    public function auto_activate($bool)
    {
        // Only process the activation of installed plugins if the automatic flag is set to true.
        if ($this->tgmpa->is_automatic) {
            // Flush plugins cache so the headers of the newly installed plugins will be read correctly.
            wp_clean_plugins_cache();

            // Get the installed plugin file.
            $plugin_info = $this->plugin_info();

            // Don't try to activate on upgrade of active plugin as WP will do this already.
            if (! is_plugin_active($plugin_info)) {
                $activate = activate_plugin($plugin_info);

                // Adjust the success string based on the activation result.
                $this->strings['process_success'] = $this->strings['process_success'] . "<br />\n";

                if (is_wp_error($activate)) {
                    $this->skin->error($activate);
                    $this->strings['process_success'] .= $this->strings['activation_failed'];
                } else {
                    $this->strings['process_success'] .= $this->strings['activation_success'];
                }
            }
        }

        return $bool;
    }//end auto_activate()
}//end class

?>