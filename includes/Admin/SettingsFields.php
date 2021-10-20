<?php

namespace Alamgir\DemoPlugin\Admin;

/**
 * Settings Fields Class
 *
 * @since 1.0.0
 */
class SettingsFields {

    /**
     * Settings Sections
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function settings_sections() {
        $sections = [
            [
                'id'    => 'admin_settings_dashboard',
                'title' => __( 'Dashboard', 'wp-demo-plugin' ),
                'icon'  => 'dashicons-dashboard',
            ],
            [
                'id'    => 'admin_settings_general',
                'title' => __( 'General Options', 'wp-demo-plugin' ),
                'icon'  => 'dashicons-admin-generic',
            ],
            [
                'id'    => 'admin_settings_my_account',
                'title' => __( 'My Account', 'wp-demo-plugin' ),
                'icon'  => 'dashicons-id',
            ],
        ];

        return apply_filters( 'wpdemo_settings_sections', $sections );
    }

    /**
     * Settings fields
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function settings_fields() {
        $settings_fields = [
            'admin_settings_dashboard' => apply_filters(
                'wpdemo_options_others', [
                    [
                        'name'    => 'compatibility_acf',
                        'label'   => __( 'Test Select Field', 'wp-demo-plugin' ),
                        'desc'    => __( 'Select <strong>Yes</strong> if you want to make compatible wpdemo custom fields data with advanced custom fields.', 'wp-demo-plugin' ),
                        'type'    => 'select',
                        'default' => 'no',
                        'options' => [
                            'yes' => __( 'Yes', 'wp-demo-plugin' ),
                            'no'  => __( 'No', 'wp-demo-plugin' ),
                        ],
                    ],
                    [
                        'name'  => 'recaptcha_private',
                        'label' => __( 'Test Text Field', 'wp-demo-plugin' ),
                        'desc'  => __( '<a target="_blank" href="https://www.google.com/recaptcha/">Register here</a> to get reCaptcha Site and Secret keys.', 'wp-demo-plugin' ),
                    ],
                    [
                        'name'  => 'custom_css',
                        'label' => __( 'Custom CSS codes', 'wp-demo-plugin' ),
                        'desc'  => __( 'If you want to add your custom CSS code, it will be added on page header wrapped with style tag', 'wp-demo-plugin' ),
                        'type'  => 'textarea',
                    ],
                ]
            ),
            'admin_settings_general' => apply_filters(
                'wpdemo_options_dashboard', [
                    [
                        'name'  => 'un_auth_msg',
                        'label' => __( 'Unauthorized Message', 'wp-demo-plugin' ),
                        'desc'  => __( 'Not logged in users will see this message', 'wp-demo-plugin' ),
                        'type'  => 'textarea',
                    ],
                ]
            ),
            'admin_setting_my_account' => apply_filters(
                'wpdemo_options_wpdemo_my_account', [
                    [
                        'name'    => 'show_billing_address',
                        'label'   => __( 'Show Billing Address', 'wp-demo-plugin' ),
                        'desc'    => __( 'Show billing address in account page.', 'wp-demo-plugin' ),
                        'type'    => 'checkbox',
                        'default' => 'on',
                    ],
                ]
            ),

        ];

        return apply_filters( 'wpdemo_settings_fields', $settings_fields );
    }

    /**
     * Initialize and registers the settings sections and fileds to WordPress
     *
     * Usually this should be called at `admin_init` hook.
     *
     * This function gets the initiated settings sections and fields. Then
     * registers them to WordPress and ready for use.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function settings_admin_init() {
        //register settings sections
        foreach ( wp_demo_plugin()->setting_field->settings_sections() as $section ) {
            if ( false == get_option( $section['id'] ) ) {
                add_option( $section['id'] );
            }

            if ( isset( $section['desc'] ) && ! empty( $section['desc'] ) ) {
                $section['desc'] = '<div class="inside">' . $section['desc'] . '</div>';
                $callback = create_function( '', 'echo "' . str_replace( '"', '\"', $section['desc'] ) . '";' );
            } elseif ( isset( $section['callback'] ) ) {
                $callback = $section['callback'];
            } else {
                $callback = null;
            }

            add_settings_section( $section['id'], $section['title'], $callback, $section['id'] );
        }

        //register settings fields
        foreach ( wp_demo_plugin()->setting_field->settings_fields() as $section => $field ) {
            foreach ( $field as $option ) {
                $type = isset( $option['type'] ) ? $option['type'] : 'text';

                $args = array(
                    'id'                => $option['name'],
                    'class'             => isset( $option['class'] ) ? $option['class'] : '',
                    'label_for'         => $args['label_for'] = "{$section}[{$option['name']}]",
                    'desc'              => isset( $option['desc'] ) ? $option['desc'] : '',
                    'name'              => $option['label'],
                    'section'           => $section,
                    'size'              => isset( $option['size'] ) ? $option['size'] : null,
                    'options'           => isset( $option['options'] ) ? $option['options'] : '',
                    'std'               => isset( $option['default'] ) ? $option['default'] : '',
                    'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
                    'type'              => $type,
                    'placeholder'       => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
                    'min'               => isset( $option['min'] ) ? $option['min'] : '',
                    'max'               => isset( $option['max'] ) ? $option['max'] : '',
                    'step'              => isset( $option['step'] ) ? $option['step'] : '',
                );

                add_settings_field( $section . '[' . $option['name'] . ']', $option['label'], ( isset( $option['callback'] ) ? $option['callback'] : [ $this, 'callback_' . $type ] ), $section, $section, $args );
            }
        }

        // creates our settings in the options table
        foreach ( wp_demo_plugin()->setting_field->settings_sections() as $section ) {
            register_setting( $section['id'], $section['id'], wp_demo_plugin()->setting_field->sanitize_options() );
        }
    }

    /**
     * Displays a text field for a settings field
     *
     * @since 1.0.0
     *
     * @param array $args settings field args
     *
     * @return void
     */
    public function callback_text( $args ) {
        $value       = esc_attr( wp_demo_plugin()->setting_field->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $type        = isset( $args['type'] ) ? $args['type'] : 'text';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';

        $html        = sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );
        $html       .= wp_demo_plugin()->setting_field->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays a url field for a settings field
     *
     * @since 1.0.0
     *
     * @param array $args settings field args
     *
     * @return void
     */
    public function callback_url( $args ) {
        callback_text( $args );
    }

    /**
     * Displays a number field for a settings field
     *
     * @since 1.0.0
     *
     * @param array $args settings field args
     *
     * @return void
     */
    public function callback_number( $args ) {
        $value       = esc_attr( wp_demo_plugin()->setting_field->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $type        = isset( $args['type'] ) ? $args['type'] : 'number';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';
        $min         = empty( $args['min'] ) ? '' : ' min="' . $args['min'] . '"';
        $max         = empty( $args['max'] ) ? '' : ' max="' . $args['max'] . '"';
        $step        = empty( $args['max'] ) ? '' : ' step="' . $args['step'] . '"';

        $html        = sprintf( '<input type="%1$s" class="%2$s-number" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s%7$s%8$s%9$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder, $min, $max, $step );
        $html       .= wp_demo_plugin()->setting_field->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays a checkbox for a settings field
     *
     * @since 1.0.0
     *
     * @param array $args settings field args
     *
     * @return string $html
     */
    public function callback_checkbox( $args ) {
        $value = esc_attr( wp_demo_plugin()->setting_field->get_option( $args['id'], $args['section'], $args['std'] ) );

        $html  = '<fieldset>';
        $html  .= sprintf( '<label for="wpuf-%1$s[%2$s]">', $args['section'], $args['id'] );
        $html  .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] );
        $html  .= sprintf( '<input type="checkbox" class="checkbox" id="wpuf-%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', $args['section'], $args['id'], checked( $value, 'on', false ) );
        $html  .= sprintf( '%1$s</label>', $args['desc'] );
        $html  .= '</fieldset>';

        echo $html;
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @since 1.0.0
     *
     * @param array $args settings field args
     *
     * @return string $html
     */
    public function callback_multicheck( $args ) {
        $value = wp_demo_plugin()->setting_field->get_option( $args['id'], $args['section'], $args['std'] );
        $value = $value ? $value : array();
        $html  = '<fieldset>';
        $html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="" />', $args['section'], $args['id'] );
        foreach ( $args['options'] as $key => $label ) {
            $checked = in_array( $key, $value, true ) ? $key : '0';
            $html    .= sprintf( '<label for="wpuf-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
            $html    .= sprintf( '<input type="checkbox" class="checkbox" id="wpuf-%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $checked, $key, false ) );
            $html    .= sprintf( '%1$s</label><br>', $label );
        }

        $html .= wp_demo_plugin()->setting_field->get_field_description( $args );
        $html .= '</fieldset>';

        echo $html;
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @since 1.0.0
     *
     * @param array $args settings field args
     *
     * @return string $html
     */
    public function callback_radio( $args ) {
        $value = wp_demo_plugin()->setting_field->get_option( $args['id'], $args['section'], $args['std'] );
        $html  = '<fieldset>';

        foreach ( $args['options'] as $key => $label ) {
            $html .= sprintf( '<label for="wpuf-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
            $html .= sprintf( '<input type="radio" class="radio" id="wpuf-%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ) );
            $html .= sprintf( '%1$s</label><br>', $label );
        }

        $html .= wp_demo_plugin()->setting_field->get_field_description( $args );
        $html .= '</fieldset>';

        echo $html;
    }

    /**
     * Displays a selectbox for a settings field
     *
     * @since 1.0.0
     *
     * @param array $args settings field args
     *
     * @return string $html
     */
    public function callback_select( $args ) {
        $value = esc_attr( wp_demo_plugin()->setting_field->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $html  = sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );

        foreach ( $args['options'] as $key => $label ) {
            $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
        }

        $html .= sprintf( '</select>' );
        $html .= wp_demo_plugin()->setting_field->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays a textarea for a settings field
     *
     * @since 1.0.0
     *
     * @param array $args settings field args
     *
     * @return string $html
     */
    public function callback_textarea( $args ) {
        $value       = esc_textarea( wp_demo_plugin()->setting_field->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';

        $html        = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]"%4$s>%5$s</textarea>', $size, $args['section'], $args['id'], $placeholder, $value );
        $html        .= wp_demo_plugin()->setting_field->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays a textarea for a settings field
     *
     * @since 1.0.0
     *
     * @param array $args settings field args
     *
     * @return string
     */
    public function callback_html( $args ) {
        echo wp_demo_plugin()->setting_field->get_field_description( $args );
    }

    /**
     * Displays a rich text textarea for a settings field
     *
     * @since 1.0.0
     *
     * @param array $args settings field args
     *
     * @return string $html
     */
    public function callback_wysiwyg( $args ) {
        $value = wp_demo_plugin()->setting_field->get_option( $args['id'], $args['section'], $args['std'] );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : '500px';

        echo '<div style="max-width: ' . $size . ';">';

        $editor_settings = array(
            'teeny'         => true,
            'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
            'textarea_rows' => 10,
        );

        if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
            $editor_settings = array_merge( $editor_settings, $args['options'] );
        }

        wp_editor( $value, $args['section'] . '-' . $args['id'], $editor_settings );

        echo '</div>';

        echo wp_demo_plugin()->setting_field->get_field_description( $args );
    }

    /**
     * Displays a file upload field for a settings field
     *
     * @since 1.0.0
     *
     * @param array $args settings field args
     *
     * @return string $html
     */
    public function callback_file( $args ) {
        $value = esc_attr( wp_demo_plugin()->setting_field->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $id    = $args['section'] . '[' . $args['id'] . ']';
        $label = isset( $args['options']['button_label'] ) ? $args['options']['button_label'] : __( 'Choose File', 'wp-demo-plugin' );

        $html  = sprintf( '<input type="text" class="%1$s-text wpsa-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
        $html  .= '<input type="button" class="button wpsa-browse" value="' . $label . '" />';
        $html  .= wp_demo_plugin()->setting_field->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays a password field for a settings field
     *
     * @since 1.0.0
     *
     * @param array $args settings field args
     *
     * @return string $html
     */
    public function callback_password( $args ) {
        $value = esc_attr( wp_demo_plugin()->setting_field->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

        $html  = sprintf( '<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
        $html  .= wp_demo_plugin()->setting_field->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays a color picker field for a settings field
     *
     * @since 1.0.0
     *
     * @param array $args settings field args
     *
     * @return string $html
     */
    public function callback_color( $args ) {
        $value = esc_attr( wp_demo_plugin()->setting_field->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

        $html  = sprintf( '<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />', $size, $args['section'], $args['id'], $value, $args['std'] );
        $html  .= wp_demo_plugin()->setting_field->get_field_description( $args );

        echo $html;
    }



    /**
     * Sanitize callback for Settings API
     *
     * @since 1.0.0
     *
     * @return mixed
     */
    public function sanitize_options( $options = '' ) {
        if ( ! $options ) {
            return $options;
        }

        foreach ( $options as $option_slug => $option_value ) {
            $sanitize_callback = wp_demo_plugin()->setting_field->get_sanitize_callback( $option_slug );

            // If callback is set, call it
            if ( $sanitize_callback ) {
                $options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
                continue;
            }
        }

        return $options;
    }

    /**
     * Get sanitization callback for given option slug
     *
     * @since 1.0.0
     *
     * @param string $slug option slug
     *
     * @return mixed string or bool false
     */
    public function get_sanitize_callback( $slug = '' ) {
        if ( empty( $slug ) ) {
            return false;
        }

        // Iterate over registered fields and see if we can find proper callback
        foreach ( wp_demo_plugin()->setting_field->settings_fields() as $section => $options ) {
            foreach ( $options as $option ) {
                if ( $option['name'] != $slug ) {
                    continue;
                }

                // Return the callback name
                return isset( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
            }
        }

        return false;
    }


    /**
     * Get the value of a settings field
     *
     * @since 1.0.0
     *
     * @param string  $option  settings field name
     * @param string  $section the section name this field belongs to
     * @param string  $default default text if it's not found
     * @return string
     */
    public function get_option( $option, $section, $default = '' ) {
        $options = get_option( $section );

        if ( isset( $options[ $option ] ) ) {
            return $options[ $option ];
        }

        return $default;
    }

    /**
     * Get field description for display
     *
     * @since 1.0.0
     *
     * @param array $args settings field args
     *
     * @return string $desc
     */
    public function get_field_description( $args ) {
        if ( ! empty( $args['desc'] ) ) {
            $desc = sprintf( '<p class="description">%s</p>', $args['desc'] );
        } else {
            $desc = '';
        }

        return $desc;
    }
}
