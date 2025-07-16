<?php
// funnel-admin.php oder einbinden in deinem Plugin/Adminbereich

if (!defined('ABSPATH')) exit;

global $wpdb;
$table = $wpdb->prefix . 'funnelforms_forms';
$form_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Speichern, wenn Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['funnel_data'])) {
    $data = wp_unslash($_POST['funnel_data']);
    $wpdb->update($table, ['data' => $data], ['id' => $form_id]);
    echo '<div class="updated notice is-dismissible"><p>Formular gespeichert.</p></div>';
}

// Formulardaten auslesen
$form_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table` WHERE id = %d", $form_id));
$json_data = $form_data ? $form_data->data : '[]';

add_action('wp_ajax_funnelforms_update_form', function () {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Keine Berechtigung']);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'funnelforms_forms';
    $id = isset($_POST['form_id']) ? (int) $_POST['form_id'] : 0;
    $data = isset($_POST['data']) ? wp_unslash($_POST['data']) : '';

    if ($id && $data) {
        $wpdb->update($table, ['data' => $data], ['id' => $id]);
        wp_send_json_success(['message' => 'Formular gespeichert']);
    }

    wp_send_json_error(['message' => 'Fehlende Daten']);
});


?>

<h1>Funnel Formular bearbeiten</h1>
<form method="post">
    <input type="hidden" name="form_id" value="<?php echo esc_attr($form_id); ?>">
    <textarea id="funnel-json" name="funnel_data" style="display:none;"><?php echo esc_textarea($json_data); ?></textarea>
    <div id="funnelformbuilderapp"></div>
    <br>
    <button type="submit" class="button button-primary">💾 Änderungen speichern</button>
</form>

<!-- Hier dein Template -->
<?php include plugin_dir_path(__FILE__) . 'formbuilder-template.html'; ?>

<!-- Dein Vue.js und Script laden -->
<script src="<?php echo plugins_url('assets/js/admin-formbuilder.js', __FILE__); ?>"></script>

<script>
    const ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
