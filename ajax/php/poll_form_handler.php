<?php
    if ( ! function_exists( 'poll_feedback_form_handler' ) ) {
        /**
         * AJAX handler to process feedback submission.
         */
        function poll_feedback_form_handler() {

            check_ajax_referer( 'poll' . $_POST['order_id'], 'poll_nonce' );

            $order_id = intval( $_POST['order_id'] );
            $order    = wc_get_order( $order_id );

            if ( ! $order ) {
                wp_send_json_error( 'Invalid order.' );
                wp_die();
            }

            $rating   = isset( $_POST['rating'] ) ? intval( $_POST['rating'] ) : 0;
            $like     = isset( $_POST['like'] ) ? sanitize_text_field( $_POST['like'] ) : '';
            $feedback = isset( $_POST['feedback_text'] ) ? sanitize_textarea_field( $_POST['feedback_text'] ) : '';

            // Create structured feedback array
            $feedback_data = array(
                'user_id'  => get_current_user_id(),
                'date'     => current_time( 'mysql' ),
                'rating'   => $rating,
                'like'     => $like,
                'feedback' => $feedback,
            );

            // Save the feedback data in order meta
            $order->update_meta_data( '_poll_feedback', $feedback_data );
            $order->save();

            // Add a human-readable note
            $note  = $order->get_formatted_billing_full_name() . " left feedback:\n";
            if ( $like ) {
                $note .= "🗣️ Opinion: {$like}\n";
            }
            if ( $rating ) {
                $note .= "⭐ Rating: {$rating}/5\n";
            }
            if ( $feedback ) {
                $note .= "💬 Comment: {$feedback}\n";
            }

            $order->add_order_note( $note, 0, true );

            wp_send_json_success( 'Feedback saved successfully.' );
            wp_die();
        }
        add_action( 'wp_ajax_collect_feedback', 'poll_feedback_form_handler' );
        add_action( 'wp_ajax_nopriv_collect_feedback', 'poll_feedback_form_handler' );
    }
