<?php
    if ( ! function_exists('contact_form_handler') ) {
        /**
         * Handles AJAX submissions for the contact form.
         *
         * This function processes POST requests submitted via AJAX for the contact form.
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
        function contact_form_handler() {
            try {
                // Ensure the request method is POST
                if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
                    wp_send_json_error([
                        'message' => __('Invalid request method.', 'borspirit')
                    ], 405);
                }

                // Check if form data is present
                if ( empty($_POST['form_data']) ) {
                    wp_send_json_error([
                        'message' => __('No form data received.', 'borspirit')
                    ], 400);
                }

                // Parse serialized form data into an associative array
                $form = [];
                if ( isset($_POST['form_data']) ) {
                    parse_str($_POST['form_data'], $form);
                }

                // Get reCAPTCHA token
                $recaptcha_token = isset($form['recaptcha_token']) ? sanitize_text_field($form['recaptcha_token']) : '';

                if (empty($recaptcha_token)) {
                    wp_send_json_error(['message' => __('reCAPTCHA verification failed (missing token).', 'borspirit')], 400);
                }

                // Send verification request to Google
                $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
                    'body' => [
                        'secret'   => RECAPTCHA_SECRET_KEY,
                        'response' => $recaptcha_token,
                    ],
                    'timeout' => 10
                ]);

                if (is_wp_error($response)) {
                    wp_send_json_error(['message' => __('Unable to verify reCAPTCHA (request failed).', 'borspirit')], 400);
                }

                // Decode Google API response
                $recaptcha = json_decode(wp_remote_retrieve_body($response), true);

                // Log score for debugging
                //error_log('reCAPTCHA score: ' . ($recaptcha['score'] ?? 'null'));

                // If reCAPTCHA fails OR score too low → possible bot
                if ( empty($recaptcha['success']) || ($recaptcha['score'] ?? 0) < 0.3 ) {
                    wp_send_json_error([
                        'message' => __('Suspicious activity detected. reCAPTCHA failed.', 'borspirit')
                    ], 403);
                }

                // Nonce verification for security
                if ( ! isset($form['contact_form_nonce']) || ! wp_verify_nonce($form['contact_form_nonce'], 'contact_form_action') ) {
                    wp_send_json_error([
                        'message' => __('Invalid security token.', 'borspirit')
                    ], 403);
                }

                // Extract and sanitize form fields
                $name    = isset($form['cf_name']) ? sanitize_text_field($form['cf_name']) : '';
                $email   = isset($form['cf_email']) ? sanitize_email($form['cf_email']) : '';
                $phone   = isset($form['cf_phone']) ? sanitize_text_field($form['cf_phone']) : '';
                $subject = isset($form['cf_subject']) ? sanitize_text_field($form['cf_subject']) : '';
                $message = isset($form['cf_message']) ? sanitize_textarea_field($form['cf_message']) : '';
                $privacy = isset($form['cf_privacy_policy']) ? sanitize_text_field($form['cf_privacy_policy']) : '';

                // Validate required fields
                if ( empty($name) || empty($email) || empty($subject) || empty($message) ) {
                    wp_send_json_error([
                        'message' => __('All required fields must be filled out.', 'borspirit')
                    ], 422);
                }

                // Validate email format
                if ( ! is_email($email) ) {
                    wp_send_json_error([
                        'message' => __('Invalid email format.', 'borspirit')
                    ], 422);
                }

                // Validate privacy policy consent
                if ( empty($privacy) || $privacy !== 'on' ) {
                    wp_send_json_error([
                        'message' => __('You must agree to the privacy policy.', 'borspirit')
                    ], 422);
                }

                // Get admin email and validate
                $admin_email = get_field( 'site_email', 'option' );
                if ( ! $admin_email || ! is_email($admin_email) ) {
                    wp_send_json_error([
                        'message' => __('Admin email is not configured properly.', 'borspirit')
                    ], 500);
                }

                // Email headers
                $headers = [
                    'From: ' . get_bloginfo('name') . ' <' . $admin_email . '>',
                    'Reply-To: ' . $name . ' <' . $email . '>',
                    'Content-Type: text/html; charset=UTF-8'
                ];

                // Email subject formatted with site name
                $mail_subject = sprintf(
                    __('[%1$s] New message: %2$s', 'borspirit'),
                    get_bloginfo('name'),
                    $subject
                );

                // Convert message lines into HTML paragraphs
                $message_lines = array_filter(preg_split("/\r\n|\n|\r/", $message), function($line) {
                    return trim($line) !== '';
                });

                $formatted_message = implode('', array_map(function($line) {
                    return wpautop( esc_html($line) );
                }, $message_lines));

                // Build final email body
                $mail_message = sprintf(
                    '<strong>Name:</strong> %s<br/>
                    <strong>Email:</strong> %s<br/>
                    <strong>Phone:</strong> %s<br/>
                    <strong>Subject:</strong> %s<br/>
                    %s',
                    esc_html($name),
                    esc_html($email),
                    esc_html($phone),
                    esc_html($subject),
                    $formatted_message
                );

                // Send the email
                $sent = wp_mail(
                    [
                        get_field( 'site_email', 'option' ), 
                        'hello@borspirit.hu'
                    ], 
                    $mail_subject, 
                    $mail_message, 
                    $headers
                );

                if ( ! $sent ) {
                    wp_send_json_error([
                        'message' => __('Message could not be sent. Please try again later.', 'borspirit')
                    ], 500);
                }

                // Generate a unique message ID and store message in a transient for 15 minutes
                // Useful for debugging, logging, or displaying confirmation later: get_transient($message_id).
                $message_id = time() . wp_generate_password(8, false, false);
                set_transient( $message_id, [
                    'name'    => $name,
                    'email'   => $email,
                    'phone'   => $phone,
                    'subject' => $subject,
                    'message' => $message,
                ], 15 * MINUTE_IN_SECONDS ); // expires after 15 mins
                
                // Success response
                wp_send_json_success([
                    'message'      => __('Your message has been sent successfully!', 'borspirit'),
                    'redirect_url' => esc_url( trailingslashit( get_the_permalink( THANK_YOU_PAGE_ID ) ) ),
                    'message_id'   => $message_id
                ], 200);

            } catch ( Exception $e ) {
                // Catch any unexpected errors
                wp_send_json_error([
                    'message' => sprintf(__('Unexpected error: %s', 'borspirit'), $e->getMessage())
                ], 500);
            }
        }

        // Register AJAX handlers for logged-in and non-logged-in users
        add_action('wp_ajax_contact_form_handler', 'contact_form_handler');
        add_action('wp_ajax_nopriv_contact_form_handler', 'contact_form_handler');
    }
