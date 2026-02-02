<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    /**
     * WooCommerce Delete Account Feature
     * Adds a "Delete Account" button to the Edit Account page,
     * handles deletion safely, and allows translation.
     * 
     * @package YourThemeOrPlugin
     */

    if ( ! function_exists( 'wc_add_delete_account_button' ) ) {
        /**
         * Display "Delete Account" button on the WooCommerce Edit Account page.
         */
        function wc_add_delete_account_button() {
            ?>
            <form class="woocommerce-EditAccountForm delete-account" action="">
                <fieldset>
                    <legend><?php echo esc_html__( 'Delete account', 'borspirit' ); ?></legend>
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide woocommerce-form-row--delete-account-message">
                        <?php echo esc_html__( 'Would you like to delete your account? Then your purchase data and lists of favorite wines will also be lost.', 'borspirit' ); ?>
                    </p>
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide woocommerce-form-row--delete-account-button">
                        <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'delete-account' ) ); ?>" class="btn btn-danger delete-account-button" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to delete your account? This action cannot be undone.', 'borspirit' ) ); ?>');">
                            <?php echo esc_html__( 'Delete account', 'borspirit' ); ?>
                        </a>
                    </p>
                </fieldset>
            </form>
        <?php
        }
        add_action( 'woocommerce_after_edit_account_form', 'wc_add_delete_account_button' );
    }

    if ( ! function_exists( 'wc_add_delete_account_endpoint' ) ) {
        /**
         * Register "delete-account" endpoint for WooCommerce My Account pages.
         */
        function wc_add_delete_account_endpoint() {
            add_rewrite_endpoint( 'delete-account', EP_PAGES );
        }
        add_action( 'init', 'wc_add_delete_account_endpoint' );
    }

    if ( ! function_exists( 'wc_handle_delete_account' ) ) {
        /**
         * Handle account deletion when user visits the delete-account endpoint.
         * Deletes the user, logs them out, and redirects to home page with a message.
         */
        function wc_handle_delete_account() {
            if ( ! is_user_logged_in() ) {
                wp_redirect( home_url() );
                exit;
            }

            $user_id = get_current_user_id();

            // Delete the user and all their content
            require_once( ABSPATH . 'wp-admin/includes/user.php' );
            wp_delete_user( $user_id );

            // Log out the user
            wp_logout();

            // Redirect to homepage with confirmation
            wp_redirect( home_url( '/?account_deleted=1' ) );
            exit;
        }
        add_action( 'woocommerce_account_delete-account_endpoint', 'wc_handle_delete_account' );
    }

    if ( ! function_exists( 'wc_account_deleted_notice' ) ) {
        /**
         * Show a JavaScript alert if the account was successfully deleted.
         */
        function wc_account_deleted_notice() {
            if ( isset( $_GET['account_deleted'] ) && $_GET['account_deleted'] == 1 ) {
                ?>
                <script type="text/javascript">
                    alert('<?php echo esc_js( __( 'Your account has been successfully deleted.', 'borspirit' ) ); ?>');
                </script>
                <?php
            }
        }
        add_action( 'wp_head', 'wc_account_deleted_notice' );
    }

    if ( ! function_exists( 'wc_delete_account_endpoint_title' ) ) {
        /**
         * Change the title of the delete-account endpoint in the My Account menu.
         *
         * @param string $title Original title.
         * @return string Translated title.
         */
        function wc_delete_account_endpoint_title( $title ) {
            return __( 'Delete account', 'borspirit' );
        }
        add_filter( 'woocommerce_endpoint_delete-account_title', 'wc_delete_account_endpoint_title' );
    }
