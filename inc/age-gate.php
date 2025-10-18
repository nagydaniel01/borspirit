<?php
    defined( 'ABSPATH' ) || exit;
    
    /**
     * Settings Page
     */
    if ( ! function_exists( 'ag_register_settings' ) ) {
        /**
         * Register Age Gate plugin settings.
         *
         * Adds default options and registers them for sanitization.
         * Hooks into 'admin_init'.
         */
        function ag_register_settings() {
            // Add default options
            add_option( 'ag_min_age', 18 );
            add_option( 'ag_cookie_days', 30 );
            add_option( 'ag_redirect_url', 'https://www.google.com' );

            // Register settings with sanitization callbacks
            register_setting( 'ag_options_group', 'ag_min_age', 'intval' );
            register_setting( 'ag_options_group', 'ag_cookie_days', 'intval' );
            register_setting( 'ag_options_group', 'ag_redirect_url', 'sanitize_text_field' );
        }
        add_action( 'admin_init', 'ag_register_settings' );
    }


    if ( ! function_exists( 'ag_register_options_page' ) ) {
        /**
         * Register Age Gate settings page under WordPress Settings menu.
         *
         * Hooks into 'admin_menu'.
         */
        function ag_register_options_page() {
            add_options_page(
                'Age Gate Settings',   // Page title
                'Age Gate',            // Menu title
                'manage_options',      // Capability
                'ag',                  // Menu slug
                'ag_options_page'      // Callback function to render the page
            );
        }
        add_action( 'admin_menu', 'ag_register_options_page' );
    }

    if ( ! function_exists( 'ag_options_page' ) ) {
        /**
         * Display the Age Gate plugin settings page.
         *
         * Renders a form with fields for minimum age, cookie expiration, and redirect URL.
         */
        function ag_options_page() {
            ?>
            <div class="wrap">
                <h1>Age Gate Settings</h1>
                <form method="post" action="options.php">
                    <?php settings_fields( 'ag_options_group' ); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Minimum Age</th>
                            <td>
                                <input type="number" name="ag_min_age" 
                                    value="<?php echo esc_attr( get_option( 'ag_min_age', 18 ) ); ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Cookie Expiration (days)</th>
                            <td>
                                <input type="number" name="ag_cookie_days" 
                                    value="<?php echo esc_attr( get_option( 'ag_cookie_days', 30 ) ); ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Redirect URL (if underage)</th>
                            <td>
                                <input type="url" name="ag_redirect_url" 
                                    value="<?php echo esc_attr( get_option( 'ag_redirect_url', 'https://www.google.com' ) ); ?>" 
                                    size="50" />
                            </td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
        }
    }