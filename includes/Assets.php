<?php

namespace Alamgir\DemoPlugin;

/**
 * Assets class
 *
 * @since 1.0.0
 */
class Assets {

    /**
     * Assets class construct
     *
     * @since 1.0.0
     */
    public function __construct(){
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
    }

    /**
     * Admin register scripts
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function enqueue_admin_scripts() {
        // JS
        wp_enqueue_script( 'wp-demo-plugin-admin-scripts', WP_DEMO_PLUGIN_ASSETS . '/js/wp-demo-plugin-admin.js', array('jquery'), WP_DEMO_PLUGIN_VERSION );

        // CSS
        wp_enqueue_style( 'wp-demo-plugin-admin-styles', WP_DEMO_PLUGIN_ASSETS . '/css/admin.css', array(), WP_DEMO_PLUGIN_VERSION, 'all' );

        do_action( 'wpdemo_enqueue_admin_scripts' );

        $admin_scripts  = $this->get_admin_localized_scripts();
        $global_scripts = $this->get_global_localized_scripts();

        wp_localize_script( 'admin-wpdemo-scripts', 'wpdemo', $admin_scripts );
        wp_localize_script( 'admin-wpdemo-scripts', 'wpdemo_global', $global_scripts );
    }

    /**
     * Frontend register scripts
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function enqueue_frontend_scripts() {
        // JS
        wp_enqueue_script( 'wp-demo-plugin-script', WP_DEMO_PLUGIN_ASSETS . '/js/wp-demo-plugin.js', array('jquery'), time(), true );

        // CSS
        wp_enqueue_style( 'wp-demo-plugin-style', WP_DEMO_PLUGIN_ASSETS . '/css/style.css', array(), time(), 'all' );

        do_action( 'wpdemo_enqueue_frontend_scripts' );

        $frontend_scripts   = $this->get_frontend_localized_scripts();
        $validation_scripts = $this->get_global_localized_scripts();

        wp_localize_script( 'wpdemo-script', 'wpdemo', $frontend_scripts );
        wp_localize_script( 'wpdemo-script', 'wpdemo_global', $validation_scripts );
    }

    /**
     * Admin script localize
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_admin_localized_scripts() {
        $admin_scripts = apply_filters( 'wpdemo_admin_localized_scripts', array(
            'wpdemo_test_loc'   => __( 'Hello World! This is admin scripts', 'wp-demo-plugin')
        ) );

        return $admin_scripts;
    }

    /**
     * Frontend script localize
     *
     * @since 1.0.0
     *
     * @return array $frontend_localized
     */
    public function get_frontend_localized_scripts() {
        $frontend_localized = apply_filters( 'wpdemo_frontend_localized_scripts', array(
            'wpdemo_test_loc'   => __( 'Hello World! This is frontend scripts', 'wp-demo-plugin')
        ) );

        return $frontend_localized;
    }

    /**
     * Validation script localize
     *
     * @since 1.0.0
     *
     * @return array $global_localized
     */
    public function get_global_localized_scripts() {
        $global_localized = apply_filters( 'wpdemo_global_localized_scripts', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'wpdemo_test_localize' )
        ) );

        return $global_localized;
    }
}
