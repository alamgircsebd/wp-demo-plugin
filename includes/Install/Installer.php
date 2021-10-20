<?php

namespace Alamgir\DemoPlugin\Install;

/**
 * Installer class
 *
 * @since 1.0.0
 */
class Installer {

    /**
     * Prepare for install when activated plugin
     *
     * @since 1.0.0
     */
    public function prepare_install() {
        $this->create_tables();
        $this->update_version();
    }

    /**
     * Create necessary tables
     *
     * @since 1.0.0
     */
    public function create_tables() {
        global $wpdb;

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}demo_plugin_db` (
               `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
               `user_id` bigint(20) unsigned NOT NULL,
               `blog_id` bigint(20) unsigned NOT NULL,
               `notice` longtext NOT NULL,
               `type` varchar(20) NOT NULL DEFAULT '',
               `status` varchar(20) DEFAULT NULL,
               `created_at` DATETIME NOT NULL,
              PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

        include_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /**
     * Update plugin version
     *
     * @since 1.0.0
     */
    public function update_version() {
        update_option( 'wp_demo_plugin_version', WP_DEMO_PLUGIN_VERSION );
    }
}
