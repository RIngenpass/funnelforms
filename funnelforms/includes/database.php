<?php

register_activation_hook(__FILE__, 'funnelforms_create_table');

function funnelforms_create_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $results = $wpdb->prefix . 'funnelforms_results';
    $forms = $wpdb->prefix . 'funnelforms_forms';

    $sql1 = "CREATE TABLE $results (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        answers LONGTEXT NOT NULL,
        submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    $sql2 = "CREATE TABLE $forms (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        title VARCHAR(255) NOT NULL,
        data LONGTEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql1);
    dbDelta($sql2);
}
