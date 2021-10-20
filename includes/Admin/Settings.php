<?php

namespace Alamgir\DemoPlugin\Admin;

/**
 * Admin Setting Option Class
 *
 * @since 1.0.0
 */
class Settings {

    /**
     * Show navigation
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function show_navigation() {
        $html  = '<h2 class="nav-tab-wrapper">';
        $count = count( wp_demo_plugin()->setting_field->settings_sections() );

        // don't show the navigation if only one section exists
        if ( 1 === $count ) {
            return;
        }

        foreach ( wp_demo_plugin()->setting_field->settings_sections() as $tab ) {
            $html .= sprintf( '<a href="#%1$s" class="nav-tab" id="%1$s-tab"><span class="dashicons %3$s"></span> %2$s</a>', $tab['id'], $tab['title'], ! empty( $tab['icon'] ) ? $tab['icon'] : '' );
        }

        $html .= '</h2>';

        echo $html;
    }

    /**
     * Get settings sections
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_settings_sections() {
        return wp_demo_plugin()->setting_field->settings_sections();
    }

    /**
     * Get settings fields
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_settings_fields() {
        return wp_demo_plugin()->setting_field->settings_fields();
    }

    /**
     * Get admin init
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_admin_init() {
        return wp_demo_plugin()->setting_field->settings_admin_init();
    }

    /**
     * Show forms
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function show_forms() {
        $settings_fields = wp_demo_plugin()->setting_field->settings_fields();
        ?>
        <div class="metabox-holder">
            <?php foreach ( wp_demo_plugin()->setting_field->settings_sections() as $form ) { ?>
                <div id="<?php echo $form['id']; ?>" class="group" style="display: none;">
                    <form method="post" action="options.php">
                        <?php
                        do_action( 'wsa_form_top_' . $form['id'], $form );
                        settings_fields( $form['id'] );
                        do_settings_sections( $form['id'] );
                        do_action( 'wsa_form_bottom_' . $form['id'], $form );
                        if ( isset( $settings_fields[ $form['id'] ] ) ) :
                            ?>
                            <div style="padding-left: 10px">
                                <?php submit_button(); ?>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            <?php } ?>
        </div>

        <script>
            jQuery(document).ready(function($) {
                $('.group').hide();
                var activetab = '';
                if (typeof(localStorage) != 'undefined' ) {
                    activetab = localStorage.getItem("activetab");
                }
                if (activetab != '' && $(activetab).length ) {
                    $(activetab).fadeIn();
                } else {
                    $('.group:first').fadeIn();
                }
                $('.group .collapsed').each(function(){
                    $(this).find('input:checked').parent().parent().parent().nextAll().each(
                        function(){
                            if ($(this).hasClass('last')) {
                                $(this).removeClass('hidden');
                                return false;
                            }
                            $(this).filter('.hidden').removeClass('hidden');
                        });
                });

                if (activetab != '' && $(activetab + '-tab').length ) {
                    $(activetab + '-tab').addClass('nav-tab-active');
                } else {
                    $('.nav-tab-wrapper a:first').addClass('nav-tab-active');
                }
                $('.nav-tab-wrapper a').click(function(evt) {
                    $('.nav-tab-wrapper a').removeClass('nav-tab-active');
                    $(this).addClass('nav-tab-active').blur();
                    var clicked_group = $(this).attr('href');
                    if (typeof(localStorage) != 'undefined' ) {
                        localStorage.setItem("activetab", $(this).attr('href'));
                    }
                    $('.group').hide();
                    $(clicked_group).fadeIn();
                    evt.preventDefault();
                });

            });
        </script>

        <style type="text/css">
            .form-table th { padding: 20px 10px; }
            #wpbody-content .metabox-holder { padding-top: 5px; min-height: 400px;}
        </style>
        <?php
    }
}

