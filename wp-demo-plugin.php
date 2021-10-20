<?php
/**
 * Plugin Name: WP Demo Plugin
 * Plugin URI: https://wordpress.org/plugins/wp-demo-plugin
 * Description: A Demo for WordPress Plugin
 * Version: 1.0.0
 * Author: Alamgir
 * Author URI: https://github.com/alamgircsebd
 * Text Domain: wp-demo-plugin
 * Domain Path: /languages/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

/*
 * Copyright (c) 2021 Alamgir Hossain (email: alamgircse.bd@gmail.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Demo_Plugin final class
 *
 * @class WP_Demo_Plugin The class that holds the entire WP_Demo_Plugin plugin
 */
final class WP_Demo_Plugin {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Instance of self
     *
     * @var WP_Demo_Plugin
     */
    private static $instance = null;

    /**
     * Minimum PHP version required
     *
     * @var string
     */
    private $min_php = '5.6.0';

    /**
     * Holds various class instances
     *
     * @since 1.0.0
     *
     * @var array
     */
    private $container = array();

    /**
     * Constructor for the WP_Demo_Plugin class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses register_activation_hook()
     * @uses register_deactivation_hook()
     * @uses add_action()
     */
    public function __construct() {
        require_once __DIR__ . '/vendor/autoload.php';

        // Define all constant
        $this->define_constant();

        register_activation_hook( __FILE__, [ $this, 'activate' ] );
        register_deactivation_hook( __FILE__, [ $this, 'deactivation' ] );

        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
    }

    /**
     * Initializes the WP_Demo_Plugin() class
     *
     * Checks for an existing WP_Demo_Plugin() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Magic getter to bypass referencing objects
     *
     * @since 1.0.0
     *
     * @param string $prop
     *
     * @return Class Instance
     */
    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
        }
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     *
     * @since 1.0.0
     */
    public function activate() {
        $installer = new \Alamgir\DemoPlugin\Install\Installer();
        $installer->prepare_install();
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     *
     * @since 1.0.0
     */
    public function deactivation() {
        // deactivation codes here
    }

    /**
     * Defined constant
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function define_constant() {
        define( 'WP_DEMO_PLUGIN_VERSION', $this->version );
        define( 'WP_DEMO_PLUGIN_FILE', __FILE__ );
        define( 'WP_DEMO_PLUGIN_DIR', __DIR__ );
        define( 'WP_DEMO_PLUGIN_PATH', dirname( WP_DEMO_PLUGIN_FILE ) );
        define( 'WP_DEMO_PLUGIN_ASSETS', plugins_url( '/assets', WP_DEMO_PLUGIN_FILE ) );
        define( 'WP_DEMO_PLUGIN_INC', WP_DEMO_PLUGIN_PATH . '/includes' );
    }

    /**
     * Load the plugin
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_plugin() {
        //includes file
        $this->includes();

        // init actions and filter
        $this->init_hooks();

        do_action( 'wp_demo_plugin_loaded', $this );
    }

    /**
     * Includes all files
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function includes() {
        require_once WP_DEMO_PLUGIN_INC . '/functions.php';

        if ( is_admin() ) {
            require_once WP_DEMO_PLUGIN_INC . '/Admin/SettingsFields.php';
        }
    }

    /**
     * Init all filters
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function init_hooks() {
        add_action( 'init', array( $this, 'localization_setup' ) );
        add_action( 'init', array( $this, 'init_classes' ), 1 );
    }

    /**
     * Init all the classes
     *
     * @return void
     */
    public function init_classes() {
        if ( is_admin() ) {
            new \Alamgir\DemoPlugin\Admin\Admin();
            new \Alamgir\DemoPlugin\Admin\Menus();
        }
        new \Alamgir\DemoPlugin\Assets();

        $this->container['api']           = new \Alamgir\DemoPlugin\REST\Manager();
        $this->container['setting_field'] = new \Alamgir\DemoPlugin\Admin\SettingsFields();

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            new \Alamgir\DemoPlugin\Ajax();
        }
    }

    /**
     * Check if the PHP version is supported
     *
     * @return bool
     */
    public function is_supported_php() {
        if ( version_compare( PHP_VERSION, $this->min_php, '<=' ) ) {
            return false;
        }

        return true;
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function template_path() {
        return apply_filters( 'wp_demo_plugin_template_path', 'wp-demo-plugin/' );
    }

    /**
     * Initialize plugin for localization
     *
     * @since 1.0.0
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'wp-demo-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
}

/**
 * Load WP Demo Plugin when all plugins loaded
 *
 * @return WP_Demo_Plugin
 */
function wp_demo_plugin() {
    return WP_Demo_Plugin::init();
}

wp_demo_plugin();
