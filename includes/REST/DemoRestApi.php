<?php

namespace Alamgir\DemoPlugin\REST;

use WP_REST_Controller;
use WP_REST_Server;

/**
 * This is just a test REST API class
 *
 * @since 1.0.0
 */
class DemoRestApi extends WP_REST_Controller {

    /**
     * Endpoint namespace
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $namespace = 'wpdemo/v1';

    /**
     * Route name
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $base = 'test';

    /**
     * Register all api init
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ), 10 );
    }

    /**
     * Register all routes
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace, '/' . $this->base, array(
				array(
                    'method'   => WP_REST_Server::READABLE,
					'callback' => array( $this, 'this_is_test_api' ),
					'args'     => $this->get_collection_params(),
				),
            )
        );
    }

    /**
     * Get test data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function this_is_test_api( $request ) {
        $params = $request->get_params();
        $array  = array( 'Alamgir', 'Jahid', 'Jahagir' );

        $response = rest_ensure_response( $array );

        return $response;
    }

}
