<?php

namespace Alamgir\DemoPlugin\Admin;

/**
 * Admin Menus Class
 *
 * @since  1.0.0
 */
class Menus {

    /**
     * Settings
     *
     * @var \Settings
     */
    private $settings_options;

    /**
     * Call Construct
     *
     * @since  1.0.0
     */
    public function __construct() {
        $this->settings_options = new \Alamgir\DemoPlugin\Admin\Settings();

        add_action( 'admin_menu', [ $this, 'admin_menus_render' ] );
        add_action( 'admin_init', [ $this, 'admin_init' ] );
    }

    /**
     * Admin menus render
     *
     * @since  1.0.0
     *
     * @return void
     */
    public function admin_init() {
        //set the settings
        $this->settings_options->get_settings_sections();
        $this->settings_options->get_settings_fields();

        //initialize settings
        $this->settings_options->get_admin_init();
    }

    /**
     * Admin menus render
     *
     * @since  1.0.0
     *
     * @return void
     */
    public function admin_menus_render() {
        global $submenu;

        $menu_slug     = 'wp-demo-plugin';
        $menu_position = 15;
        $capability    = 'manage_options';

        $menu_pages[] = add_menu_page( __( 'WP Demo Plugin', 'wp-demo-plugin'), __( 'WP Demo Plugin', 'wp-demo-plugin'), $capability, $menu_slug, array( $this, 'admin_main_page_view' ), 'dashicons-tickets', $menu_position );

        $menu_pages[] = add_submenu_page( $menu_slug, __( 'Dashboard', 'wp-demo-plugin' ), __( 'Dashboard', 'wp-demo-plugin' ), $capability, 'wpdemo-dashboard', array( $this, 'admin_main_page_view' ) );
        $menu_pages[] = add_submenu_page( $menu_slug, __( 'Admin Menu1', 'wp-demo-plugin' ), __( 'Admin Menu1', 'wp-demo-plugin' ), $capability, 'admin-menu1', array( $this, 'admin_main_page_view' ) );
        $menu_pages[] = add_submenu_page( $menu_slug, __( 'Settings', 'wp-demo-plugin' ), __( 'Settings', 'wp-demo-plugin' ), $capability, 'wpdemo-settings', array( $this, 'settings_page' ) );

        $this->menu_pages[] = apply_filters( 'wpdemo_admin_menu', $menu_pages, $menu_slug, $capability );
    }

    /**
     * Admin main page view
     *
     * @since  1.0.0
     *
     * @return void
     */
    public function admin_main_page_view() {
        do_action( 'wp-demo-plugin-add-more-descriptions-top' );

        $headline =  __( 'Welcome to Our WP Demo Plugin', 'wp-demo-plugin' );
        ?>
        <div class="wrap">
            <h2><?php echo esc_html( $headline ); ?></h2>
            <div id="my-test-react-div">  </div>
        </div>
        <?php

        do_action( 'wp-demo-plugin-add-more-descriptions-bottom' );
    }

    /**
     * Settings page
     *
     * @since  1.0.0
     *
     * @return void
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h2 style="margin-bottom: 15px;"><?php esc_html_e( 'Settings', 'wp-demo-plugin' ); ?></h2>
            <div class="wpdemo-settings-wrap">
                <?php
                settings_errors();

                $this->settings_options->show_navigation();
                $this->settings_options->show_forms();
                ?>
            </div>
        </div>
        <?php
    }
}

