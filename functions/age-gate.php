<?php
    /**
     * Settings Page
     */
    function ag_register_settings() {
        add_option('ag_min_age', 18);
        add_option('ag_cookie_days', 30);
        add_option('ag_redirect_url', 'https://www.google.com');

        register_setting('ag_options_group', 'ag_min_age', 'intval');
        register_setting('ag_options_group', 'ag_cookie_days', 'intval');
        register_setting('ag_options_group', 'ag_redirect_url', 'sanitize_text_field');
    }
    add_action('admin_init', 'ag_register_settings');

    function ag_register_options_page() {
        add_options_page('Age Gate Settings', 'Age Gate', 'manage_options', 'ag', 'ag_options_page');
    }
    add_action('admin_menu', 'ag_register_options_page');

    function ag_options_page() {
        ?>
        <div class="wrap">
            <h1>Age Gate Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('ag_options_group'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Minimum Age</th>
                        <td><input type="number" name="ag_min_age" value="<?php echo esc_attr(get_option('ag_min_age', 18)); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Cookie Expiration (days)</th>
                        <td><input type="number" name="ag_cookie_days" value="<?php echo esc_attr(get_option('ag_cookie_days', 30)); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Redirect URL (if underage)</th>
                        <td><input type="url" name="ag_redirect_url" value="<?php echo esc_attr(get_option('ag_redirect_url', 'https://www.google.com')); ?>" size="50" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }