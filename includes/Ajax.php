<?php

namespace Alamgir\DemoPlugin;

/**
 * Ajax handler
 *
 * @since 1.0.0
 */
class Ajax {

    /**
     * Class constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'wp_ajax_test_ajax_method', [ $this, 'test_ajax_method' ] );
    }

    /**
     * Test ajax method
     *
     * @since  1.0.0
     *
     * @return void
     */
    public function test_ajax_method() {
        check_ajax_referer( 'test_ajax_method' );
    }
}
