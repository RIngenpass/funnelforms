<?php

function funnelforms_list_forms() {
    global $wpdb;
    $table = $wpdb->prefix . 'funnelforms_forms';

    // Löschen
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $wpdb->delete($table, ['id' => $id]);
        echo '<div class="notice notice-success"><p>Formular mit ID ' . $id . ' wurde gelöscht.</p></div>';
    }

    $forms = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");

    echo '<div class="wrap"><h1>FunnelForms – Formulare</h1>';
    echo '<table class="widefat fixed striped"><thead><tr><th>ID</th><th>Name</th><th>Titel</th><th>Aktionen</th></tr></thead><tbody>';

    foreach ($forms as $form) {
        $edit_url = admin_url('admin.php?page=funnelforms_add_form&id=' . $form->id);
        $delete_url = admin_url('admin.php?page=funnelforms_forms&delete=' . $form->id);

        echo '<tr>';
        echo '<td>' . esc_html($form->id) . '</td>';
        echo '<td>' . esc_html($form->name) . '</td>';
        echo '<td>' . esc_html($form->title) . '</td>';
        echo '<td>
            <a href="' . esc_url($edit_url) . '" class="button">Bearbeiten</a>
            <a href="' . esc_url($delete_url) . '" class="button button-danger" onclick="return confirm(\'Wirklich löschen?\')">Löschen</a>
        </td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '<a href="?page=funnelforms_add_form" class="button button-primary mt-3">+ Neues Formular</a>';
    echo '</div>';
}
