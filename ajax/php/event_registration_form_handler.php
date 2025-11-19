<?php
    if ( ! function_exists('event_registration_form_handler') ) {
        /**
         * Handles AJAX submissions for the event registration form.
         *
         * This function processes POST requests submitted via AJAX for event registration.
         * It performs the following steps:
         *   1. Validates the request method and presence of form data.
         *   2. Parses and sanitizes form inputs.
         *   3. Verifies the security nonce.
         *   4. Validates required fields and prevents duplicate registrations.
         *   5. Creates a new 'attendee' custom post type entry.
         *   6. Sends a confirmation email to the registrant.
         *   7. Returns a JSON response indicating success or failure.
         *
         * @return void Outputs a JSON response and terminates execution.
         */
        function event_registration_form_handler() {
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
                if ( ! isset($form['event_registration_form_nonce']) || ! wp_verify_nonce($form['event_registration_form_nonce'], 'event_registration_form_action') ) {
                    wp_send_json_error([
                        'message' => __('Invalid security token.', 'borspirit')
                    ], 403);
                }

                // Extract and sanitize form fields
                $event_id = isset($form['event_id']) ? intval($form['event_id']) : 0;
                $user_id  = get_current_user_id();
                $name     = isset($form['reg_name']) ? sanitize_text_field($form['reg_name']) : '';
                $email    = isset($form['reg_email']) ? sanitize_email($form['reg_email']) : '';
                $privacy  = isset($form['reg_privacy_policy']) ? sanitize_text_field($form['reg_privacy_policy']) : '';

                /*
                // Validate user
                if ( ! $user_id ) {
                    wp_send_json_error([
                        'message' => __('You must be logged in to register.', 'borspirit')
                    ], 401);
                }
                */

                // Validate required fields
                if ( empty($name) || empty($email) ) {
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

                // Validate event ID
                if ( ! $event_id ) {
                    wp_send_json_error([
                        'message' => __('Invalid event ID.', 'borspirit')
                    ], 400);
                }

                // Validate privacy policy consent
                if ( empty($privacy) || $privacy !== 'on' ) {
                    wp_send_json_error([
                        'message' => __('You must agree to the privacy policy.', 'borspirit')
                    ], 422);
                }

                // Prevent duplicate registration
                $existing = get_posts([
                    'post_type'  => 'attendee',
                    'post_status'=> 'publish',
                    'meta_query' => [
                        'relation' => 'AND',
                        [
                            'key'   => 'event_id',
                            'value' => $event_id
                        ],
                        [
                            'key'   => 'attendee_email',
                            'value' => $email
                        ]
                    ]
                ]);

                if ( $existing ) {
                    wp_send_json_error([
                        'message' => __('You are already registered for this event.', 'borspirit')
                    ], 409);
                }

                // Create attendee post
                $registration_id = wp_insert_post([
                    'post_type'   => 'attendee',
                    'post_status' => 'publish',
                    'post_title'  => sprintf(
                        __('[Event ID: %d] %s (%s)', 'borspirit'),
                        $event_id,
                        $name,
                        $email
                    ),
                    'post_author' => $user_id,
                    'meta_input'  => [
                        'event_id'                => $event_id,
                        'attendee_name'           => $name,
                        'attendee_email'          => $email,
                        'attendee_privacy_policy' => $privacy,
                    ]
                ]);

                // Handle insertion errors
                if ( is_wp_error($registration_id) ) {
                    wp_send_json_error([
                        'message' => sprintf(__('Registration failed: %s', 'borspirit'), $registration_id->get_error_message())
                    ], 500);
                }

                if ( $registration_id === false ) {
                    wp_send_json_error([
                        'message' => __('Registration failed. Please try again later.', 'borspirit')
                    ], 500);
                }

                // Get admin email and validate
                $admin_email = get_option('admin_email');
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
                $subject = sprintf(__('Registration confirmation for #%d', 'borspirit'), get_the_title($event_id));

                // Build final email body
                $message = sprintf(
                    __("Hi %s,\n\nThank you for registering for Event #%d.\n\nWe’ve reserved your spot and will contact you with more details soon.\n\nBest regards,\n%s", 'borspirit'),
                    $name,
                    get_the_title($event_id),
                    get_bloginfo('name')
                );

                // Send the email
                $sent = wp_mail($email, $subject, $message, $headers);

                if ( ! $sent ) {
                    wp_send_json_error([
                        'message' => __('Message could not be sent. Please try again later.', 'borspirit')
                    ], 500);
                }

                // Success response
                wp_send_json_success([
                    'message'      => __('Successfully registered!', 'borspirit'),
                    'redirect_url' => esc_url( trailingslashit( home_url('/thank-you') ) ),
                    'attendee_id'  => $registration_id
                ], 200);

            } catch ( Exception $e ) {
                // Catch unexpected errors
                wp_send_json_error([
                    'message' => sprintf(__('Unexpected error: %s', 'borspirit'), $e->getMessage())
                ], 500);
            }
        }

        // Register AJAX handlers for logged-in and non-logged-in users
        add_action('wp_ajax_event_registration_form_handler', 'event_registration_form_handler');
        add_action('wp_ajax_nopriv_event_registration_form_handler', 'event_registration_form_handler');
    }
