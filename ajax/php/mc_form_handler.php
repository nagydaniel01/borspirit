<?php
    if ( ! function_exists('mc_form_handler') ) {
        /**
         * Handles AJAX submissions for the Mailchimp subscription form.
         *
         * This function processes POST requests submitted via AJAX for subscribing users to a Mailchimp list.
         * It performs the following steps:
         *   1. Validates that the request method is POST.
         *   2. Checks for the presence of form data.
         *   3. Parses serialized form data into an associative array.
         *   4. Verifies the security nonce.
         *   5. Sanitizes and validates form fields (name, email, privacy consent).
         *   6. Subscribes the user to Mailchimp using the MailchimpService class.
         *   7. Returns a JSON response indicating success or failure.
         *
         * @return void Outputs a JSON response and terminates execution.
         */
        function mc_form_handler() {
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
                if ( ! isset($form['subscribe_form_nonce']) ||
                    ! wp_verify_nonce($form['subscribe_form_nonce'], 'subscribe_form_action') ) {
                    wp_send_json_error([
                        'message' => __('Invalid security token.', TEXT_DOMAIN)
                    ], 403);
                }

                // Sanitize form fields
                $user_id = get_current_user_id();
                $name    = isset($form['mc_name']) ? sanitize_text_field($form['mc_name']) : '';
                $email   = isset($form['mc_email']) ? sanitize_email($form['mc_email']) : '';
                $privacy = isset($form['mc_privacy_policy']) ? sanitize_text_field($form['mc_privacy_policy']) : '';

                // Validate required fields
                if ( empty($name) || empty($email) ) {
                    wp_send_json_error(['message' => __('All required fields must be filled out.', TEXT_DOMAIN)], 422);
                }

                // Validate email
                if ( ! is_email($email) ) {
                    wp_send_json_error(['message' => __('Invalid email format.', TEXT_DOMAIN)], 422);
                }

                // Validate privacy consent
                if ( empty($privacy) || $privacy !== 'on' ) {
                    wp_send_json_error(['message' => __('You must agree to the privacy policy.', TEXT_DOMAIN)], 422);
                }

                // Mailchimp subscription
                $mailchimp = new MailchimpService(
                    get_field('mailchimp_api_key', 'option'),
                    get_field('mailchimp_audience_id', 'option')
                );

                $subscribe = $mailchimp->subscribe($email, $name, '', ['WordPress'], 'subscribed');

                // Handle Mailchimp errors safely
                if ( is_wp_error($subscribe) ) {
                    error_log('Mailchimp WP_Error: ' . $subscribe->get_error_message());
                    wp_send_json_error([
                        'message' => __('Mailchimp request failed. Please try again later.', TEXT_DOMAIN)
                    ], 500);
                }

                if ( empty($subscribe['success']) ) {
                    wp_send_json_error([
                        'message' => $subscribe['message'] ?? __('User could not be subscribed. Please try again later.', TEXT_DOMAIN)
                    ], 500);
                }

                // Success response
                wp_send_json_success([
                    'message'      => $subscribe['message'],
                    'redirect_url' => esc_url(trailingslashit(home_url('/thank-you'))),
                    'email'        => $email
                ], 200);

            } catch ( Exception $e ) {
                // Catch any unexpected errors
                wp_send_json_error([
                    'message' => sprintf(__('Unexpected error: %s', TEXT_DOMAIN), $e->getMessage())
                ], 500);
            }
        }

    // Register AJAX handlers
    add_action('wp_ajax_mc_form_handler', 'mc_form_handler');
    add_action('wp_ajax_nopriv_mc_form_handler', 'mc_form_handler');
}
