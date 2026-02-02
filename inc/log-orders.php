<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! function_exists( 'log_failed_orders_wc_status' ) ) {
        /**
         * Logs failed WooCommerce orders to a custom log file.
         *
         * @param int $order_id The WooCommerce order ID.
         * @return void
         */
        function log_failed_orders_wc_status( $order_id ) {
            // Ensure the order ID is valid.
            if ( empty( $order_id ) || ! is_numeric( $order_id ) ) {
                return;
            }

            // Retrieve the order object.
            $order = wc_get_order( $order_id );

            // Exit if the order does not exist or is not a WC_Order object.
            if ( ! $order || ! is_a( $order, 'WC_Order' ) ) {
                return;
            }

            // Only proceed if the order has failed.
            if ( ! $order->has_status( 'failed' ) ) {
                return;
            }

            // Load the WooCommerce logger.
            $logger = wc_get_logger();

            // Sanitize order data before logging.
            // Use `wc_print_r()` safely to avoid revealing sensitive data (like payment info).
            $log_message = sprintf(
                'Failed Order #%d: %s',
                absint( $order_id ),
                wc_print_r( $order->get_data(), true )
            );

            // Log the failed order details to a custom log source.
            $logger->info( $log_message, array( 'source' => 'failed-orders' ) );
        }
        add_action( 'woocommerce_before_thankyou', 'log_failed_orders_wc_status' );
    }
