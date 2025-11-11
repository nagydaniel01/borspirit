<?php
    defined( 'ABSPATH' ) || exit;

    /**
     * Register plugin settings.
     */
    if ( ! function_exists( 'ag_register_settings' ) ) {
        function ag_register_settings() {
            // Default options
            add_option( 'ag_min_age', 18 );
            add_option( 'ag_cookie_days', 30 );
            add_option( 'ag_redirect_url', 'https://www.google.com' );

            // Modal defaults (translatable)
            add_option( 'ag_modal_title', __( 'Are you over %s years of age?', 'borspirit' ) );
            add_option( 'ag_modal_content', __( 'We are committed advocates and supporters of responsible, civilized drinking. Therefore, we do not recommend the consumption of alcoholic beverages to persons under the age of %s and cannot serve them.', 'borspirit' ) );
            add_option( 'ag_modal_btn_yes', __( 'I am old enough', 'borspirit' ) );
            add_option( 'ag_modal_btn_no', __( 'I am under %s', 'borspirit' ) );

            // Register with sanitization
            register_setting( 'ag_options_group', 'ag_min_age', 'intval' );
            register_setting( 'ag_options_group', 'ag_cookie_days', 'intval' );
            register_setting( 'ag_options_group', 'ag_redirect_url', 'sanitize_text_field' );

            register_setting( 'ag_options_group', 'ag_modal_title', 'sanitize_text_field' );
            register_setting( 'ag_options_group', 'ag_modal_content', 'sanitize_textarea_field' );
            register_setting( 'ag_options_group', 'ag_modal_btn_yes', 'sanitize_text_field' );
            register_setting( 'ag_options_group', 'ag_modal_btn_no', 'sanitize_text_field' );
        }
        add_action( 'admin_init', 'ag_register_settings' );
    }

    /**
     * Add settings page to admin menu.
     */
    if ( ! function_exists( 'ag_register_options_page' ) ) {
        function ag_register_options_page() {
            add_options_page(
                __( 'Age Gate Settings', 'borspirit' ),
                __( 'Age Gate', 'borspirit' ),
                'manage_options',
                'ag',
                'ag_options_page'
            );
        }
        add_action( 'admin_menu', 'ag_register_options_page' );
    }

    /**
     * Render the settings page.
     */
    if ( ! function_exists( 'ag_options_page' ) ) {
        function ag_options_page() {
            ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'Age Gate Settings', 'borspirit' ); ?></h1>

                <form method="post" action="options.php">
                    <?php settings_fields( 'ag_options_group' ); ?>

                    <h2><?php esc_html_e( 'General Settings', 'borspirit' ); ?></h2>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Minimum Age', 'borspirit' ); ?></th>
                            <td>
                                <input type="number" name="ag_min_age" value="<?php echo esc_attr( get_option( 'ag_min_age', 18 ) ); ?>">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Cookie Expiration (days)', 'borspirit' ); ?></th>
                            <td>
                                <input type="number" name="ag_cookie_days" value="<?php echo esc_attr( get_option( 'ag_cookie_days', 30 ) ); ?>">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Redirect URL (if underage)', 'borspirit' ); ?></th>
                            <td>
                                <input type="url" name="ag_redirect_url" value="<?php echo esc_attr( get_option( 'ag_redirect_url', 'https://www.google.com' ) ); ?>" size="50">
                            </td>
                        </tr>
                    </table>

                    <h2><?php esc_html_e( 'Modal Content Settings', 'borspirit' ); ?></h2>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Modal Title', 'borspirit' ); ?></th>
                            <td>
                                <input type="text" name="ag_modal_title" value="<?php echo esc_attr( get_option( 'ag_modal_title', __( 'Are you over %s years of age?', 'borspirit' ) ) ); ?>" size="50">
                                <p class="description"><?php esc_html_e( 'Use %s for the minimum age placeholder if needed.', 'borspirit' ); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Modal Content', 'borspirit' ); ?></th>
                            <td>
                                <textarea name="ag_modal_content" rows="5" cols="60"><?php
                                    echo esc_textarea(
                                        get_option(
                                            'ag_modal_content',
                                            __( 'We are committed advocates and supporters of responsible, civilized drinking. Therefore, we do not recommend the consumption of alcoholic beverages to persons under the age of %s and cannot serve them.', 'borspirit' )
                                        )
                                    );
                                ?></textarea>
                                <p class="description"><?php esc_html_e( 'You can include %s to dynamically show the minimum age.', 'borspirit' ); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( '"Yes" Button Text', 'borspirit' ); ?></th>
                            <td>
                                <input type="text" name="ag_modal_btn_yes" value="<?php echo esc_attr( get_option( 'ag_modal_btn_yes', __( 'I am old enough', 'borspirit' ) ) ); ?>" size="50">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( '"No" Button Text', 'borspirit' ); ?></th>
                            <td>
                                <input type="text" name="ag_modal_btn_no" value="<?php echo esc_attr( get_option( 'ag_modal_btn_no', __( 'I am under %s', 'borspirit' ) ) ); ?>" size="50">
                                <p class="description"><?php esc_html_e( 'Use %s for the minimum age placeholder if needed.', 'borspirit' ); ?></p>
                            </td>
                        </tr>
                    </table>

                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
        }
    }