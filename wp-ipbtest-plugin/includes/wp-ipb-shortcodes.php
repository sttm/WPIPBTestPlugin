<?php
class WP_IPB_Shortcodes {
    public static function init() {
        add_shortcode('wp_ipb_list', array(__CLASS__, 'render_task_list'));
    }

    public static function render_task_list() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ipbdb';
        $_tasks = $wpdb->get_results("SELECT * FROM $table_name WHERE completed = 0 ORDER BY id DESC LIMIT 5");

        ob_start();
        if ($_tasks) {
            echo '<ul>';
            foreach ($_tasks as $task) {
                echo '<li>' . esc_html($task->title) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No tasks found.</p>';
        }
        return ob_get_clean();
    }
}
