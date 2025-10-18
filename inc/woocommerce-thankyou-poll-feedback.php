<?php
    defined( 'ABSPATH' ) || exit;
    
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    if ( ! function_exists( 'thankyou_poll_feedback' ) ) {
        /**
         * Display poll form on the WooCommerce Thank You page,
         * but only if the order is paid.
         *
         * @param int $order_id WooCommerce order ID.
         */
        function thankyou_poll_feedback( $order_id ) {
            if ( ! $order_id ) {
                return;
            }

            $order = wc_get_order( $order_id );

            // Only allow poll if order is paid
            if ( ! $order || ! $order->is_paid() ) {
                //echo '<p>' . esc_html__( 'Poll is available only for paid orders.', TEXT_DOMAIN ) . '</p>';
                return;
            }

            // Check if feedback has already been submitted
            $feedback_submitted = $order->get_meta( '_poll_feedback' );

            if ( $feedback_submitted ) {
                // Optional: show a thank-you message instead
                echo '<p>' . esc_html__( 'Thank you for your feedback!', TEXT_DOMAIN ) . '</p>';
                return;
            }
            ?>
            
            <?php 
                $template_args = array(
                    'order_id' => $order_id
                );

                get_template_part('template-parts/forms/form', 'poll_form', $template_args );
            ?>

            <?php
        }
        add_action( 'woocommerce_thankyou', 'thankyou_poll_feedback', 4 );
    }
