<?php

namespace Alamgir\DemoPlugin\REST;

/**
 * API Registrar class
 *
 * @since 1.0.0
 */
class Manager {

    /**
     * Class dir and class name mapping
     *
     * @since 1.0.0
     *
     * @var array
     */
    protected $class_map;

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        if ( ! class_exists( 'WP_REST_Server' ) ) {
            return;
        }

        $this->class_map = apply_filters(
            'wp_demo_plugin_rest_api_class_map', array(
                WP_DEMO_PLUGIN_DIR . '/includes/REST/DemoRestApi.php' => 'Alamgir\DemoPlugin\REST\DemoRestApi',
            )
        );

        // Init REST API routes
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
    }

    /**
     * Register REST API routes
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_rest_routes() {
        foreach ( $this->class_map as $file_name => $controller ) {
            require_once $file_name;
            $this->$controller = new $controller();
            $this->$controller->register_routes();
        }
    }
}
