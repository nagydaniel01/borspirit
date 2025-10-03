<?php
    if ( ! function_exists('testimonial_form_handler') ) {
        /**
         * Handles AJAX submissions for the testimonial form.
         *
         * This function processes POST requests submitted via AJAX for the testimonial form.
         * It performs the following steps:
         *   1. Validates the request method and presence of form data.
         *   2. Parses and sanitizes form inputs.
         *   3. Verifies the security nonce.
         *   4. Validates required fields, email format, and privacy policy consent.
         *   5. Prepares and sends an HTML email to the site admin.
         *   6. Stores the message temporarily in a WordPress transient for 15 minutes.
         *   7. Returns a JSON response indicating success or failure.
         *
         * @return void Outputs a JSON response and terminates execution.
         */
        function testimonial_form_handler() {
            try {
                // Ensure the request method is POST
                if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
                    wp_send_json_error([
                        'message' => __('Invalid request method.', TEXT_DOMAIN)
                    ], 405);
                }

                // Check if form data is present
                if ( empty($_POST['form_data']) ) {
                    wp_send_json_error([
                        'message' => __('No form data received.', TEXT_DOMAIN)
                    ], 400);
                }

                // Parse serialized form data into an associative array
                $form = [];
                if ( isset($_POST['form_data']) ) {
                    parse_str($_POST['form_data'], $form);
                }

                // Nonce verification for security
                if ( ! isset($form['testimonial_form_nonce']) ||
                    ! wp_verify_nonce($form['testimonial_form_nonce'], 'testimonial_form_action') ) {
                    wp_send_json_error([
                        'message' => __('Invalid security token.', TEXT_DOMAIN)
                    ], 403);
                }

                // Extract and sanitize form fields
                $name    = isset($form['tf_name']) ? sanitize_text_field($form['tf_name']) : '';
                $email   = isset($form['tf_email']) ? sanitize_email($form['tf_email']) : '';
                $message = isset($form['tf_message']) ? sanitize_textarea_field($form['tf_message']) : '';
                $privacy = isset($form['tf_privacy_policy']) ? sanitize_text_field($form['tf_privacy_policy']) : '';

                // Validate required fields
                if ( empty($name) || empty($email) || empty($message) ) {
                    wp_send_json_error([
                        'message' => __('All required fields must be filled out.', TEXT_DOMAIN)
                    ], 422);
                }

                // Validate email format
                if ( ! is_email($email) ) {
                    wp_send_json_error([
                        'message' => __('Invalid email format.', TEXT_DOMAIN)
                    ], 422);
                }

                // Validate privacy policy consent
                if ( empty($privacy) || $privacy !== 'on' ) {
                    wp_send_json_error([
                        'message' => __('You must agree to the privacy policy.', TEXT_DOMAIN)
                    ], 422);
                }

                // Prevent duplicate testimonial
                $existing = get_posts([
                    'post_type'  => 'testimonial',
                    'post_status'=> 'publish',
                    'meta_query' => [
                        'relation' => 'AND',
                        [
                            'key'   => 'testimonial_email',
                            'value' => $email
                        ]
                    ]
                ]);

                if ( $existing ) {
                    wp_send_json_error([
                        'message' => __('You are already wrote a review.', TEXT_DOMAIN)
                    ], 409);
                }

                // Create testimonial post
                $testimonial_id = wp_insert_post([
                    'post_type'   => 'testimonial',
                    'post_status' => 'publish',
                    'post_title'  => sprintf(
                        __('%s (%s)', TEXT_DOMAIN),
                        $name,
                        $email
                    ),
                    'post_content' => wp_kses_post($message),
                    'meta_input'  => [
                        'testimonial_name'           => $name,
                        'testimonial_email'          => $email,
                        'testimonial_privacy_policy' => $privacy,
                    ]
                ]);

                // Handle insertion errors
                if ( is_wp_error($testimonial_id) ) {
                    wp_send_json_error([
                        'message' => sprintf(__('Submission failed: %s', TEXT_DOMAIN), $testimonial_id->get_error_message())
                    ], 500);
                }

                if ( $testimonial_id === false ) {
                    wp_send_json_error([
                        'message' => __('Submission failed, please try again.', TEXT_DOMAIN)
                    ], 500);
                }

                // Generate a unique message ID and store message in a transient for 15 minutes
                // Useful for debugging, logging, or displaying confirmation later: get_transient($message_id).
                $message_id = time() . wp_generate_password(8, false, false);
                set_transient( $message_id, [
                    'name'    => $name,
                    'email'   => $email,
                    'message' => $message,
                ], 15 * MINUTE_IN_SECONDS ); // expires after 15 mins
                
                // Success response
                wp_send_json_success([
                    'message'      => __('Your review has been sent successfully!', TEXT_DOMAIN),
                    'redirect_url' => esc_url( trailingslashit( home_url('/thank-you') ) ),
                    'message_id'   => $message_id
                ], 200);

            } catch ( Exception $e ) {
                // Catch any unexpected errors
                wp_send_json_error([
                    'message' => sprintf(__('Unexpected error: %s', TEXT_DOMAIN), $e->getMessage())
                ], 500);
            }
        }

        // Register AJAX handlers for logged-in and non-logged-in users
        add_action('wp_ajax_testimonial_form_handler', 'testimonial_form_handler');
        add_action('wp_ajax_nopriv_testimonial_form_handler', 'testimonial_form_handler');
    }
