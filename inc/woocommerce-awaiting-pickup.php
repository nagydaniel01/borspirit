<?php
    defined( 'ABSPATH' ) || exit;

    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }
    
    /**
     * Register a custom WooCommerce order status: "Awaiting Pickup"
     */

    if ( ! function_exists( 'register_awaiting_pickup_order_status' ) ) {
        /**
         * Register the custom order status 'wc-awaiting-pickup'.
         *
         * Hooks into 'init' to register a post status that WooCommerce can recognize.
         */
        function register_awaiting_pickup_order_status() {
            register_post_status( 'wc-awaiting-pickup', array(
                'label'                     => __( 'Awaiting Pickup', 'borspirit' ),
                'public'                    => true,
                'exclude_from_search'       => true,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop(
                    __( 'Awaiting Pickup <span class="count">(%s)</span>', 'borspirit' ),
                    __( 'Awaiting Pickup <span class="count">(%s)</span>', 'borspirit' )
                ),
            ) );
        }
        add_action( 'init', 'register_awaiting_pickup_order_status' );
    }


    if ( ! function_exists( 'add_awaiting_pickup_to_order_statuses' ) ) {
        /**
         * Add "Awaiting Pickup" to the list of WooCommerce order statuses.
         *
         * @param array $order_statuses Existing WooCommerce order statuses.
         * @return array Modified order statuses including 'Awaiting Pickup'.
         */
        function add_awaiting_pickup_to_order_statuses( $order_statuses ) {
            $order_statuses['wc-awaiting-pickup'] = __( 'Awaiting Pickup', 'borspirit' );
            return $order_statuses;
        }
        add_filter( 'wc_order_statuses', 'add_awaiting_pickup_to_order_statuses' );
    }


    if ( ! function_exists( 'add_awaiting_pickup_to_bulk_actions' ) ) {
        /**
         * Add "Change status to Awaiting Pickup" to bulk actions dropdown.
         *
         * @param array $bulk_actions Existing bulk actions for orders.
         * @return array Modified bulk actions including Awaiting Pickup.
         */
        function add_awaiting_pickup_to_bulk_actions( $bulk_actions ) {
            $bulk_actions['mark_awaiting-pickup'] = __( 'Change status to Awaiting Pickup', 'borspirit' );
            return $bulk_actions;
        }
        add_filter( 'bulk_actions-edit-shop_order', 'add_awaiting_pickup_to_bulk_actions' );
        add_filter( 'bulk_actions-woocommerce_page_wc-orders', 'add_awaiting_pickup_to_bulk_actions' );
    }


    if ( ! function_exists( 'handle_awaiting_pickup_bulk_action' ) ) {
        /**
         * Handle bulk action to change orders to "Awaiting Pickup".
         *
         * @param string $redirect_to URL to redirect after action.
         * @param string $doaction The bulk action being performed.
         * @param array $order_ids Array of order IDs selected for the bulk action.
         * @return string Redirect URL with bulk action count parameter.
         */
        function handle_awaiting_pickup_bulk_action( $redirect_to, $doaction, $order_ids ) {
            if ( 'mark_awaiting-pickup' === $doaction ) {
                foreach ( $order_ids as $order_id ) {
                    $order = wc_get_order( $order_id );
                    $order->update_status( 'wc-awaiting-pickup' ); // Update status
                }
                $redirect_to = add_query_arg( 'bulk_awaiting_pickup', count( $order_ids ), $redirect_to );
            }
            return $redirect_to;
        }
        add_filter( 'handle_bulk_actions-edit-shop_order', 'handle_awaiting_pickup_bulk_action', 20, 3 );
        add_filter( 'handle_bulk_actions-woocommerce_page_wc-orders', 'handle_awaiting_pickup_bulk_action', 20, 3 );
    }


    if ( ! function_exists( 'awaiting_pickup_bulk_action_message' ) ) {
        /**
         * Display success message after performing bulk "Awaiting Pickup" action.
         *
         * @param array $messages Existing bulk action messages.
         * @return array Modified messages including Awaiting Pickup success notice.
         */
        function awaiting_pickup_bulk_action_message( $messages ) {
            if ( ! empty( $_GET['bulk_awaiting_pickup'] ) ) {
                $messages['updated'] = array(
                    0 => sprintf(
                        __( 'Successfully changed the status of %s order(s) to Awaiting Pickup.', 'borspirit' ),
                        $_GET['bulk_awaiting_pickup']
                    ),
                );
            }
            return $messages;
        }
        add_filter( 'bulk_action_updated_messages', 'awaiting_pickup_bulk_action_message', 10, 1 );
    }


    if ( ! function_exists( 'include_awaiting_pickup_order_status_to_reports' ) ) {
        /**
         * Include the "Awaiting Pickup" custom order status in WooCommerce sales reports.
         *
         * @param array $statuses Existing order statuses used in reports.
         * @return array Modified order statuses including 'awaiting-pickup'.
         */
        function include_awaiting_pickup_order_status_to_reports( $statuses ){
            // Add the custom status to default reporting statuses
            return array( 'processing', 'on-hold', 'completed', 'awaiting-pickup' );
        }
        add_filter( 'woocommerce_reports_order_statuses', 'include_awaiting_pickup_order_status_to_reports', 20, 1 );
    }

    if ( ! function_exists( 'send_awaiting_pickup_wc_email' ) ) {
        /**
         * Sends a customer email for orders that are marked as "Awaiting Pickup".
         *
         * The email uses WooCommerce HTML styling by leveraging the WC_Mailer instance.
         * It wraps the message in the WooCommerce template and styles it inline.
         *
         * @param int $order_id The WooCommerce order ID.
         */
        function send_awaiting_pickup_wc_email( $order_id ) {

            // Validate order ID
            if ( ! $order_id ) {
                return;
            }

            // Get the order object
            $order = wc_get_order( $order_id );
            if ( ! $order ) {
                return;
            }

            // Get the customer's billing email
            $to = $order->get_billing_email();
            if ( ! $to ) {
                return;
            }

            // Prepare email subject and heading
            $subject = sprintf(
                /* translators: %1$s: site title, %2$s: order number */
                __( '[%1$s] Your order #%2$s is ready for pickup', 'borspirit' ),
                get_bloginfo( 'name' ),
                $order->get_order_number()
            );

            $heading = __( 'Your Order is Ready for Pickup!', 'borspirit' );

            // Prepare email body content
            $message = sprintf(
                /* translators: %1$s: customer first name, %2$s: order number */
                __( "Hi %1\$s,\n\nGood news! Your order #%2\$s is now ready for pickup at our store.\n\nThank you for shopping with us!", 'borspirit' ),
                $order->get_billing_first_name(),
                $order->get_order_number()
            );

            // Get WooCommerce mailer instance
            $mailer = WC()->mailer();

            // Wrap message using WooCommerce HTML email template
            $wrapped_message = $mailer->wrap_message( $heading, nl2br( $message ) );

            // Create temporary WC_Email instance to apply inline styles
            $wc_email = new WC_Email();
            $html_message = $wc_email->style_inline( $wrapped_message );

            // Prepare email headers for HTML content
            $headers = array( 'Content-Type: text/html; charset=UTF-8' );

            // Send the email
            $mailer->send( $to, $subject, $html_message, $headers );
        }
        add_action( 'woocommerce_order_status_awaiting-pickup', 'send_awaiting_pickup_wc_email', 10, 1 );
    }
