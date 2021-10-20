<?php
// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get translated days
 *
 * @param  string day
 *
 * @since  1.0.0
 *
 * @return string
 */
function wp_demo_plugin_get_translated_days( $day ) {
    switch ( $day ) {
        case 'saturday':
            return __( 'Saturday', 'wp-demo-plugin' );

        case 'sunday':
            return __( 'Sunday', 'wp-demo-plugin' );

        case 'monday':
            return __( 'Monday', 'wp-demo-plugin' );

        case 'tuesday':
            return __( 'Tuesday', 'wp-demo-plugin' );

        case 'wednesday':
            return __( 'Wednesday', 'wp-demo-plugin' );

        case 'thursday':
            return __( 'Thursday', 'wp-demo-plugin' );

        case 'friday':
            return __( 'Friday', 'wp-demo-plugin' );

        case 'close':
            return apply_filters( 'wp_demo_plugin_close_day_label', __( 'Off Day', 'wp-demo-plugin' ) );

        default:
            return apply_filters( 'wp_demo_plugin_get_translated_days', '', $day );
            break;
    }
}


/**
 * Keep record of keys by group name
 *
 * @since 1.0.0
 *
 * @param string $key
 * @param string $group
 *
 * @return void
 */
function wp_demo_plugin_cache_update_group( $key, $group ) {
    $keys = get_option( $group, [] );

    if ( in_array( $key, $keys, true ) ) {
        return;
    }

    $keys[] = $key;
    update_option( $group, $keys );
}

/**
 * Bulk clear cache values by group name
 *
 * @since 1.0.0
 *
 * @param string $group
 *
 * @return void
 */
function wp_demo_plugin_cache_clear_group( $group ) {
    $keys = get_option( $group, [] );

    if ( ! empty( $keys ) ) {
        foreach ( $keys as $key ) {
            wp_cache_delete( $key, $group );
            unset( $keys[ $key ] );
        }
    }

    update_option( $group, $keys );
}

/**
 * Function to get the client ip address
 *
 * @since 1.0.0
 *
 * @return string
 */
function wp_demo_plugin_get_client_ip() {
    $ipaddress = '';
    $_server   = $_SERVER;

    if ( isset( $_server['HTTP_CLIENT_IP'] ) ) {
        $ipaddress = $_server['HTTP_CLIENT_IP'];
    } elseif ( isset( $_server['HTTP_X_FORWARDED_FOR'] ) ) {
        $ipaddress = $_server['HTTP_X_FORWARDED_FOR'];
    } elseif ( isset( $_server['HTTP_X_FORWARDED'] ) ) {
        $ipaddress = $_server['HTTP_X_FORWARDED'];
    } elseif ( isset( $_server['HTTP_FORWARDED_FOR'] ) ) {
        $ipaddress = $_server['HTTP_FORWARDED_FOR'];
    } elseif ( isset( $_server['HTTP_FORWARDED'] ) ) {
        $ipaddress = $_server['HTTP_FORWARDED'];
    } elseif ( isset( $_server['REMOTE_ADDR'] ) ) {
        $ipaddress = $_server['REMOTE_ADDR'];
    } else {
        $ipaddress = 'UNKNOWN';
    }

    return $ipaddress;
}

/**
 * Get template part implementation for wedocs
 *
 * Looks at the theme directory first
 */
function wp_demo_plugin_get_template_part( $slug, $name = '', $args = [] ) {
    $defaults = [
        'pro' => false,
    ];

    $args = wp_parse_args( $args, $defaults );

    if ( $args && is_array( $args ) ) {
        extract( $args );
    }

    $template = '';

    // Look in yourtheme/plugin-slug/slug-name.php and yourtheme/plugin-slug/slug.php
    $template = locate_template( [ wp_demo_plugin()->template_path() . "{$slug}-{$name}.php", wp_demo_plugin()->template_path() . "{$slug}.php" ] );

    /**
     * Change template directory path filter
     *
     * @since 1.0.0
     */
    $template_path = apply_filters( 'wp_demo_plugin_set_template_path', wp_demo_plugin()->plugin_path() . '/templates', $template, $args );

    // Get default slug-name.php
    if ( ! $template && $name && file_exists( $template_path . "/{$slug}-{$name}.php" ) ) {
        $template = $template_path . "/{$slug}-{$name}.php";
    }

    if ( ! $template && ! $name && file_exists( $template_path . "/{$slug}.php" ) ) {
        $template = $template_path . "/{$slug}.php";
    }

    // Allow 3rd party plugin filter template file from their plugin
    $template = apply_filters( 'wp_demo_plugin_get_template_part', $template, $slug, $name );

    if ( $template ) {
        include $template;
    }
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @param mixed  $template_name
 * @param array  $args          (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path  (default: '')
 *
 * @return void
 */
function wp_demo_plugin_get_template( $template_name, $args = [], $template_path = '', $default_path = '' ) {
    if ( $args && is_array( $args ) ) {
        extract( $args );
    }

    $located = wp_demo_plugin_locate_template( $template_name, $template_path, $default_path );

    if ( ! file_exists( $located ) ) {
        _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', esc_html( $located ) ), '2.1' );

        return;
    }

    do_action( 'wp_demo_plugin_before_template_part', $template_name, $template_path, $located, $args );

    include $located;

    do_action( 'wp_demo_plugin_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *      yourtheme       /   $template_path  /   $template_name
 *      yourtheme       /   $template_name
 *      $default_path   /   $template_name
 *
 * @param mixed  $template_name
 * @param string $template_path (default: '')
 * @param string $default_path  (default: '')
 *
 * @return string
 */
function wp_demo_plugin_locate_template( $template_name, $template_path = '', $default_path = '', $pro = false ) {
    if ( ! $template_path ) {
        $template_path = wp_demo_plugin()->template_path();
    }

    if ( ! $default_path ) {
        $default_path = wp_demo_plugin()->plugin_path() . '/templates/';
    }

    // Look within passed path within the theme - this is priority
    $template = locate_template(
        [
            trailingslashit( $template_path ) . $template_name,
        ]
    );

    // Get default template
    if ( ! $template ) {
        $template = $default_path . $template_name;
    }

    // Return what we found
    return apply_filters( 'wp_demo_plugin_locate_template', $template, $template_name, $template_path );
}
