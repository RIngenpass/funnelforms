<?php


// [funnelform id="1"]
add_shortcode('funnelform', function ($atts) {
    global $wpdb;
    $atts = shortcode_atts(['id' => 0], $atts);
    $form_id = (int)$atts['id'];
    if ($form_id <= 0) return 'Formular-ID fehlt.';

    $table = $wpdb->prefix . 'funnelforms_forms';
    $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $form_id));
    if (!$form) return 'Formular nicht gefunden.';

    $json = stripslashes(html_entity_decode($form->data));
    $funnel_data = json_decode($json, true);

    if (!$funnel_data || !is_array($funnel_data)) {
        return '<pre style="background:#fdd;color:#000;padding:1em;">'
            . '❌ Formulardaten konnten nicht gelesen werden.<br><br>'
            . '📦 Rohdaten:<br>' . htmlspecialchars($json) . '<br><br>'
            . '⚠️ Fehlercode: ' . json_last_error() . '<br>'
            . '🧾 Fehlertext: ' . json_last_error_msg()
            . '</pre>';
    }

    $funnel = $funnel_data;

    ob_start();
    include plugin_dir_path(__DIR__) . 'templates/funnel-template.php';
    return ob_get_clean();
});

// [dynamic-select datei="spezialisierung"]
add_shortcode('dynamic-select', function ($atts = []) {
    $slug = isset($atts['datei']) ? sanitize_file_name($atts['datei']) : null;
    $filepath = WP_PLUGIN_DIR . '/cf7_dynamic-select/options/' . $slug . '.txt';

    if (!file_exists($filepath)) {
        return '<div style="color: red;">⚠️ Datei nicht gefunden: ' . esc_html($slug) . '</div>';
    }

    $options = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $output = '<select name="' . esc_attr($slug) . '">';
    foreach ($options as $opt) {
        $output .= '<option value="' . esc_attr($opt) . '">' . esc_html($opt) . '</option>';
    }
    $output .= '</select>';
    return $output;
});
