<?php

add_action('admin_init', function () {
    register_setting('funnelforms_options', 'funnelforms_email');
});

function funnelforms_admin_page() {
    ?>
    <div class="wrap">
        <h1>FunnelForms Einstellungen</h1>
        <form method="post" action="options.php">
            <?php settings_fields('funnelforms_options'); ?>
            <?php do_settings_sections('funnelforms_options'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">E-Mail für Ergebnisse</th>
                    <td><input type="email" name="funnelforms_email" value="<?php echo esc_attr(get_option('funnelforms_email')); ?>" style="width:300px;"></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
