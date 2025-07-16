<?php

add_action('wp_ajax_funnelforms_submit', 'funnelforms_submit');
add_action('wp_ajax_nopriv_funnelforms_submit', 'funnelforms_submit');

function funnelforms_submit()
{
    global $wpdb;

    // Eingabedaten parsen
    $raw = $_POST['answers'] ?? '';
    $data = json_decode(stripslashes($raw), true);

    if (!is_array($data)) {
        wp_send_json_error(['message' => 'Ungültige Formulardaten']);
        return;
    }

    // In Datenbank speichern
    $table = $wpdb->prefix . 'funnelforms_results';
    $inserted = $wpdb->insert($table, [
        'answers' => maybe_serialize($data),
        'submitted_at' => current_time('mysql'),
    ]);

    if ($inserted === false) {
        wp_send_json_error(['message' => 'Fehler beim Speichern in die Datenbank']);
        return;
    }

    // Empfängeradresse aus den Plugin-Einstellungen holen
    $to = get_option('funnelforms_email');
    if (!$to || !is_email($to)) {
        wp_send_json_error(['message' => 'Empfängeradresse ist ungültig oder fehlt']);
        return;
    }

    // HTML-E-Mail erzeugen
    $subject = 'Neue FunnelForms-Einreichung';
    $body = '<h2 style="font-family: sans-serif;">Neue FunnelForms-Einreichung</h2>';
    $body .= '<table style="border-collapse: collapse; width: 100%; font-family: sans-serif;">';
    $body .= '<thead><tr style="background-color: #f2f2f2;"><th style="padding: 8px; border: 1px solid #ddd;">Frage</th><th style="padding: 8px; border: 1px solid #ddd;">Antwort</th></tr></thead><tbody>';

    foreach ($data as $entry) {
        $frage = esc_html($entry['question'] ?? '');
        $antwort = nl2br(esc_html($entry['answer'] ?? ''));
        $body .= "<tr><td style=\"padding: 8px; border: 1px solid #ddd;\">{$frage}</td><td style=\"padding: 8px; border: 1px solid #ddd;\">{$antwort}</td></tr>";
    }

    $body .= '</tbody></table>';

    $headers = ['Content-Type: text/html; charset=UTF-8'];

    $mail_sent = wp_mail($to, $subject, $body, $headers);

    if (!$mail_sent) {
        wp_send_json_error(['message' => 'E-Mail-Versand fehlgeschlagen']);
        return;
    }

    wp_send_json_success(['message' => 'Formular erfolgreich verarbeitet']);
    wp_die();
}
