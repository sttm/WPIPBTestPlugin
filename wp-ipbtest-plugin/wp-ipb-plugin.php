<?php
/*
Plugin Name: IP-B Test Plugin
Description: Плагин для интеграции с внешним API https://jsonplaceholder.typicode.com/todos.
Version: 1.0
Author: Nikita Savenkov
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Включение необходимых файлов
include_once(plugin_dir_path(__FILE__) . 'includes/wp-ipb-activator.php');
include_once(plugin_dir_path(__FILE__) . 'includes/wp-ipb-deactivator.php');
include_once(plugin_dir_path(__FILE__) . 'includes/wp-ipb-admin.php');
include_once(plugin_dir_path(__FILE__) . 'includes/wp-ipb-shortcodes.php');
include_once(plugin_dir_path(__FILE__) . 'includes/wp-ipb-logger.php');

// Регистрация хуков активации и деактивации
register_activation_hook(__FILE__, array('WP_IPB_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('WP_IPB_Deactivator', 'deactivate'));

// Инициализация плагина
function wp_ipb_plugin_init() {
    WP_IPB_Admin::init();
    WP_IPB_Shortcodes::init();
}
add_action('plugins_loaded', 'wp_ipb_plugin_init');
