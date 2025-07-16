<?php

function funnelforms_add_form_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'funnelforms_forms';

    $id = $_GET['id'] ?? null;
    $form = null;

    // Speichern
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = sanitize_text_field($_POST['name']);
        $title = sanitize_text_field($_POST['title']);
        $data = stripslashes($_POST['data']);

        if ($id) {
            $wpdb->update($table, [
                'name' => $name,
                'title' => $title,
                'data' => $data,
            ], ['id' => $id]);
        } else {
            $wpdb->insert($table, [
                'name' => $name,
                'title' => $title,
                'data' => $data,
            ]);
            $id = $wpdb->insert_id;
        }

        echo '<div class="notice notice-success"><p>Formular gespeichert!</p></div>';
        $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    }

    // Laden
    if ($id && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    }

    echo '<div class="wrap">';
    echo '<h1>Formular bearbeiten</h1>';

    echo '<div class="notice notice-info" style="padding: 20px; margin-top: 20px; line-height: 1.6;">
        <strong>📌 Was ist FunnelForms?</strong><br>
        Mit diesem Plugin kannst du <strong>mehrstufige Formulare (Funnels)</strong> mit individuellen Feldern erstellen.<br><br>
        <strong>Funktionen:</strong>
        <ul style="list-style: disc; padding-left: 20px;">
            <li>Visueller Schritt-für-Schritt-Builder mit Vue.js</li>
            <li>Feldtypen: Textfeld, E-Mail, Dropdown, Textarea, Bildauswahl, Contact7-Shortcode</li>
            <li>Jeder Schritt kann zu einem definierten nächsten führen</li>
            <li>Optionen mit Ziel-Schritten für bedingte Pfade</li>
            <li>JSON wird automatisch gespeichert</li>
            <li>Formular-Ausgabe über Shortcode: <code>[funnelform id="1"]</code></li>
        </ul>
    </div>';

    echo '<div class="wrap" style="background-color: #FFFFFF; border:dotted"><h1>' . ($id ? 'Formular bearbeiten' : 'Neues Formular') . '</h1>';
    echo '<form method="post">';
    echo '<table class="form-table">';
    echo '<tr><th><label for="name">Interner Name</label></th><td><input type="text" name="name" id="name" value="' . esc_attr($form->name ?? '') . '" required style="width: 300px;"></td></tr>';
    echo '<tr><th><label for="title">Anzeigetitel</label></th><td><input type="text" name="title" id="title" value="' . esc_attr($form->title ?? '') . '" required style="width: 300px;"></td></tr>';
    echo '<tr><th colspan="2"><label>Formbuilder</label></th></tr>';
    echo '<tr><td colspan="2">';
    echo '<div id="funnelformbuilderapp"></div>';
    echo '<textarea name="data" id="funnel-json" style="display:none;">' . esc_textarea($form->data ?? '') . '</textarea>';
    echo '</td></tr>';
    echo '</table>';
    echo '<button type="submit" class="button button-primary" onclick="generateFunnelJSON()">Speichern</button>';
    echo '</form></div>';
}
