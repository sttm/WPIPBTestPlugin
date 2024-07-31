<?php
class WP_IPB_Admin {
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        add_action('admin_post_sync_ipb', array(__CLASS__, 'sync_ipb'));
        add_action('init', array(__CLASS__, 'register_ipb_taxonomy'));
    }

    public static function add_admin_menu() {
        add_menu_page(
            'IP-B Test',
            'IP-B ',
            'manage_options',
            'wp-ipb',
            array(__CLASS__, 'admin_page'),
            'dashicons-list-view'
        );
    }

    public static function admin_page() {
        ?>
        <div class="wrap">
            <h1>WP IP-B Test</h1>
            <p>Добавить шорт-код: [wp_ipb_list]</p>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="sync_ipb">
                <?php submit_button('Синхронизировать задачи'); ?>
            </form>
            <form method="get" action="" style="margin-bottom: 20px;">
                <input type="hidden" name="page" value="wp-ipb">
                <input type="text" name="s" placeholder="Поиск по названию">

                <?php submit_button('🔍', 'primary', '', false); ?>
            </form>
            <?php self::display_ipb(); ?>
        </div>
        <?php
    }

    public static function sync_ipb() {
        $response = wp_remote_get('https://jsonplaceholder.typicode.com/todos');
        if (is_wp_error($response)) {
            WP_IPB_Logger::log('error', 'Error fetching data from API.');
            wp_redirect(admin_url('admin.php?page=wp-ipb&error=1'));
            exit;
        }

        $tasks = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($tasks)) {
            WP_IPB_Logger::log('error', 'No data retrieved from API.');
            wp_redirect(admin_url('admin.php?page=wp-ipb&error=1'));
            exit;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'ipbdb';

        // Clear the table before inserting new data
        $wpdb->query("TRUNCATE TABLE $table_name");

        foreach ($tasks as $task) {
            $wpdb->insert($table_name, array(
                'id' => $task['id'],
                'userId' => $task['userId'],
                'title' => $task['title'],
                'completed' => $task['completed']
            ));
        }

        WP_IPB_Logger::log('info', 'Data synchronized successfully.');
        wp_redirect(admin_url('admin.php?page=wp-ipb&success=1'));
        exit;
    }

    public static function display_ipb() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ipbdb';

        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

        $query = "SELECT * FROM $table_name";
        if (!empty($search)) {
            $query .= $wpdb->prepare(" WHERE title LIKE %s", '%' . $wpdb->esc_like($search) . '%');
        }

        $tasks = $wpdb->get_results($query);

        if ($tasks) {
            echo '<table class="widefat">';
            echo '<thead><tr><th>ID</th><th>User ID</th><th>Title</th><th>Completed</th></tr></thead>';
            echo '<tbody>';
            foreach ($tasks as $task) {
                echo '<tr>';
                echo '<td>' . esc_html($task->id) . '</td>';
                echo '<td>' . esc_html($task->userId) . '</td>';
                echo '<td>' . esc_html($task->title) . '</td>';
                echo '<td>' . ($task->completed ? 'Yes' : 'No') . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No tasks found.</p>';
        }
    }

    public static function register_ipb_taxonomy() {
        $labels = array(
            'name' => _x('Task Categories', 'taxonomy general name'),
            'singular_name' => _x('Task Category', 'taxonomy singular name'),
            'search_items' => __('Search Task Categories'),
            'all_items' => __('All Task Categories'),
            'parent_item' => __('Parent Task Category'),
            'parent_item_colon' => __('Parent Task Category:'),
            'edit_item' => __('Edit Task Category'),
            'update_item' => __('Update Task Category'),
            'add_new_item' => __('Add New Task Category'),
            'new_item_name' => __('New Task Category Name'),
            'menu_name' => __('Task Categories'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'task-category'),
        );

        register_taxonomy('task_category', array('task'), $args);
    }
}
