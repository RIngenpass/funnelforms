<?php


// Admin Assets (Builder)
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'funnelforms_page_funnelforms_add_form') {
        wp_enqueue_script('vuejs', 'https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js');
        wp_enqueue_script('funnelformbuilder', plugins_url('../assets/js/admin-formbuilder.js', __FILE__), ['vuejs'], filemtime(plugin_dir_path(__DIR__) . 'assets/js/admin-formbuilder.js'), true);

        wp_enqueue_style('bulma-css', 'https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css', [], '0.9.4');
        wp_enqueue_style('funnelformbuilder-css', plugins_url('../assets/css/admin-formbuilder.css', __FILE__), [], filemtime(plugin_dir_path(__DIR__) . 'assets/css/admin-formbuilder.css'));

        add_action('admin_footer', function () {
            include plugin_dir_path(__DIR__) . 'templates/formbuilder-template.html';
        });
    }
});

// Frontend Assets
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('funnelforms-css', plugins_url('../assets/css/style.css', __FILE__));
    wp_enqueue_script('funnelforms-js', plugins_url('../assets/js/script.js', __FILE__), [], null, true);
});

function enqueue_funnel_flatpickr_assets() {
    wp_enqueue_style('flatpickr-css', plugin_dir_url(__FILE__) . 'plugins/cf7-flatpickr/flatpickr.min.css');
    wp_enqueue_script('flatpickr-js', plugin_dir_url(__FILE__) . 'plugins/cf7-flatpickr/flatpickr.min.js', [], null, true);
    wp_add_inline_script('flatpickr-js', 'flatpickr(".flatpickr", { enableTime: false, dateFormat: "Y-m-d" });');
}
add_action('wp_enqueue_scripts', 'enqueue_funnel_flatpickr_assets');