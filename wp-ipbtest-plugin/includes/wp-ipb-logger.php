<?php
class WP_IPB_Logger {
    public static function log($level, $message, array $context = array()) {
        if (WP_DEBUG) {
            error_log(strtoupper($level) . ': ' . $message);
        }
    }
}
