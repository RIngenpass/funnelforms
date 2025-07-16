<?php

add_action('wp_ajax_funnelforms_submit', 'funnelforms_submit');
add_action('wp_ajax_nopriv_funnelforms_submit', 'funnelforms_submit');

function funnelforms_submit()
{
    global $wpdb;

    $raw = $_POST['answers'] ?? '';
    $data = json_decode(stripslashes($raw), true);

    if (!is_array($data)) {
        wp_send_json_error(['message' => 'Ungültige Formulardaten']);
    }

    // Speichern in Datenbank
    $table = $wpdb->prefix . 'funnelforms_results';
    $inserted = $wpdb->insert($table, [
        'answers' => maybe_serialize($data),
        'submitted_at' => current_time('mysql'),
    ]);

    if ($inserted === false) {
        wp_send_json_error(['message' => 'Fehler beim Speichern in die Datenbank']);
    }

    // Mailversand
    $to = get_option('funnelforms_email');
    if (!$to || !is_email($to)) {
        wp_send_json_error(['message' => 'Empfängeradresse ist ungültig oder fehlt']);
    }

    $subject = 'Neue FunnelForms-Einreichung';
    $body = '';
    foreach ($data as $entry) {
        $frage = sanitize_text_field($entry['question'] ?? '');
        $antwort = sanitize_text_field($entry['answer'] ?? '');
        $body .= "$frage:\n$antwort\n\n";
    }

    $mail_sent = wp_mail($to, $subject, $body);

    if (!$mail_sent) {
        wp_send_json_error(['message' => 'E-Mail-Versand fehlgeschlagen']);
    }

    wp_send_json_success(['message' => 'Formular erfolgreich verarbeitet']);

    wp_die();
}
add_action('wp_ajax_funnelforms_save_json', function () {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Keine Berechtigung']);
    }

    $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
    $data = isset($_POST['data']) ? wp_unslash($_POST['data']) : '';

    if ($form_id && $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'exp_funnelforms_forms';

        $updated = $wpdb->update(
            $table,
            ['data' => $data],
            ['id' => $form_id],
            ['%s'],
            ['%d']
        );

        if ($updated !== false) {
            wp_send_json_success(['message' => 'Gespeichert']);
        } else {
            wp_send_json_error(['message' => 'DB Fehler']);
        }
    }

    wp_send_json_error(['message' => 'Ungültige Daten']);
});
