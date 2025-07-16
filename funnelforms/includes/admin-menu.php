<?php
add_action('admin_menu', function () {
    add_options_page(
        'FunnelForms Einstellungen',
        'FunnelForms',
        'manage_options',
        'funnelforms',
        'funnelforms_admin_page'
    );

    add_menu_page(
        'FunnelForms Formulare',
        'FunnelForms',
        'manage_options',
        'funnelforms_forms',
        'funnelforms_list_forms',
        'dashicons-feedback',
        30
    );

    add_submenu_page(
        'funnelforms_forms',
        'Neues Formular',
        'Neu hinzufügen',
        'manage_options',
        'funnelforms_add_form',
        'funnelforms_add_form_page'
    );

    // ✅ HIER Formular-Bearbeitung einfügen:
    add_submenu_page(
        null, // Kein Menüpunkt sichtbar, nur per Direktlink erreichbar
        'Formular bearbeiten',
        'Formular bearbeiten',
        'manage_options',
        'funnelforms_edit_form',
        function () {
            include plugin_dir_path(__FILE__) . 'form-edit.php';
        }
    );
});
