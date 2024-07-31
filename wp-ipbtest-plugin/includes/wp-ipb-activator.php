<?php
class WP_IPB_Activator {
    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ipbdb';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            userId mediumint(9) NOT NULL,
            title text NOT NULL,
            completed boolean NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
