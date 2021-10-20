<?php

namespace Alamgir\DemoPlugin\Admin;

/**
 * Admin Class
 *
 * @since 1.0.0
 */
class Admin {

    /**
     * Admin Class constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ], 10 );
        add_action( 'admin_notices', [ $this, 'show_notices' ] );
    }

    /**
     * Load admin menus pages
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function admin_menu() {
        add_submenu_page( 'tools.php', __( 'Test Menu', 'wp-demo-plugin' ), __( 'Test Menu', 'wp-demo-plugin' ), 'manage_options', 'wp-demo-plugin-admin-notices', [ $this, 'render_page_content' ] );
    }

    /**
     * Render menu page content
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function render_page_content() {
        wp_demo_plugin_get_template_part( 'admin-content' );
    }

    /**
     * Show notices in admin area
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function show_notices() {;
        wp_demo_plugin_get_template_part( 'admin-notice' );
    }
}
