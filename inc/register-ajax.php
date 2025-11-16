<?php
    defined( 'ABSPATH' ) || exit;
    
    $ajax_dir = get_template_directory() . '/ajax/php';

    if ( file_exists( $ajax_dir ) && is_dir( $ajax_dir ) ) {
        include_files_recursively( $ajax_dir );
    } else {
        error_log( 'Directory does not exist: ' . $ajax_dir );
    }

    if ( ! function_exists( 'enqueue_comment_form_ajax_scripts' ) ) {
        function enqueue_comment_form_ajax_scripts() {
            $script_rel_path = '/ajax/js/comment_form_ajax.js'; // relative to theme root
            $script_path = get_template_directory() . $script_rel_path;
            $script_uri = get_template_directory_uri() . $script_rel_path;

            // Check if the script file exists before enqueuing
            if ( file_exists( $script_path ) ) {
                // Enqueue the script with jQuery as a dependency, to be loaded in the footer
                wp_enqueue_script( 'comment_form_ajax_script', $script_uri, array( 'jquery' ), null, true );

                // Localize script for use in JS
                wp_localize_script( 'comment_form_ajax_script', 'comment_form_ajax_object', array(
                    'ajax_url'        => admin_url( 'admin-ajax.php' ),
                    'loading_text'    => __( 'Loading...', 'borspirit' ),
                    'post_comment'    => __( 'Post Comment' ),
                    'error_adding'    => __( 'Error while adding comment', 'borspirit' ),
                    'error_timeout'   => __( 'Error: Server doesn’t respond.', 'borspirit' )
                ) );
            } else {
                error_log( 'Script file does not exist: ' . $script_path );
            }
        }
        add_action( 'wp_enqueue_scripts', 'enqueue_comment_form_ajax_scripts' );
    }

    if ( ! function_exists( 'enqueue_poll_form_ajax_scripts' ) ) {
        function enqueue_poll_form_ajax_scripts() {
            // Only load on WooCommerce Thank You (order received) page.
            if ( ! is_wc_endpoint_url( 'order-received' ) ) {
                return;
            }

            $script_rel_path = '/ajax/js/poll_form_ajax.js'; // relative to theme root.
            $script_path     = get_template_directory() . $script_rel_path;
            $script_uri      = get_template_directory_uri() . $script_rel_path;

            // Only enqueue if the file exists
            if ( file_exists( $script_path ) ) {
                wp_enqueue_script( 'poll_form_ajax_script', $script_uri, array( 'jquery' ), null, true );

                // Pass dynamic data to JS
                wp_localize_script( 'poll_form_ajax_script', 'poll_form_ajax_object', array(
                    'ajax_url'           => admin_url( 'admin-ajax.php' ),
                    'msg_select_rating'  => __( 'Please select a rating.', 'borspirit' ),
                    'msg_select_opinion' => __( 'Please select your opinion about the shop.', 'borspirit' ),
                    'msg_enter_feedback' => __( 'Please write your feedback.', 'borspirit' ),
                    'msg_sending'        => __( 'Submitting your feedback…', 'borspirit' ),
                    'msg_success'        => __( 'Thank you! Your feedback has been sent.', 'borspirit' ),
                    'msg_error'          => __( 'There was an error sending your feedback.', 'borspirit' ),
                    'msg_network_error'  => __( 'A network error occurred. Please try again.', 'borspirit' ),
                ) );
            } else {
                error_log( 'Thank You feedback script not found: ' . $script_path );
            }
        }
        add_action( 'wp_enqueue_scripts', 'enqueue_poll_form_ajax_scripts' );
    }

    if ( ! function_exists( 'enqueue_event_registration_form_ajax_scripts' ) ) {
        function enqueue_event_registration_form_ajax_scripts() {
            $script_rel_path = '/ajax/js/event_registration_form_ajax.js'; // relative to theme root
            $script_path = get_template_directory() . $script_rel_path;
            $script_uri  = get_template_directory_uri() . $script_rel_path;

            // Only enqueue if the file exists
            if ( file_exists( $script_path ) ) {
                wp_enqueue_script( 'event_registration_form_ajax_script', $script_uri, array( 'jquery' ), null, true );

                // Pass dynamic data to JS
                wp_localize_script( 'event_registration_form_ajax_script', 'event_registration_form_ajax_object', array(
                    'ajax_url'             => admin_url( 'admin-ajax.php' ),
                    'user_id'              => get_current_user_id(),
                    'recaptcha_site_key'   => RECAPTCHA_SITE_KEY,
                    'msg_privacy_required' => __( 'You must agree to the privacy policy.', 'borspirit' ),
                    'msg_sending'          => __( 'Registering…', 'borspirit' ),
                    'msg_success'          => __( 'Successfully registered!', 'borspirit' ),
                    'msg_error_sending'    => __( 'There was an error while processing your registration.', 'borspirit' ),
                    'msg_unexpected'       => __( 'An unexpected error occurred.', 'borspirit' ),
                    'msg_network_error'    => __( 'A network error occurred.', 'borspirit' )
                ) );
            } else {
                error_log( 'Event registration form script file does not exist: ' . $script_path );
            }
        }
        //add_action( 'wp_enqueue_scripts', 'enqueue_event_registration_form_ajax_scripts' );
    }

    if ( ! function_exists( 'enqueue_contact_form_ajax_scripts' ) ) {
        function enqueue_contact_form_ajax_scripts() {
            $script_rel_path = '/ajax/js/contact_form_ajax.js'; // relative to theme root
            $script_path = get_template_directory() . $script_rel_path;
            $script_uri  = get_template_directory_uri() . $script_rel_path;

            // Only enqueue if the file exists
            if ( file_exists( $script_path ) ) {
                wp_enqueue_script( 'contact_form_ajax_script', $script_uri, array( 'jquery' ), null, true );

                // Pass dynamic data to JS
                wp_localize_script( 'contact_form_ajax_script', 'contact_form_ajax_object', array(
                    'ajax_url'             => admin_url( 'admin-ajax.php' ),
                    'user_id'              => get_current_user_id(),
                    'recaptcha_site_key'   => RECAPTCHA_SITE_KEY,
                    'msg_privacy_required' => __( 'You must agree to the privacy policy.', 'borspirit' ),
                    'msg_sending'          => __( 'Sending…', 'borspirit' ),
                    'msg_success'          => __( 'Message sent successfully!', 'borspirit' ),
                    'msg_error_sending'    => __( 'There was an error while sending your message.', 'borspirit' ),
                    'msg_unexpected'       => __( 'An unexpected error occurred.', 'borspirit' ),
                    'msg_network_error'    => __( 'A network error occurred.', 'borspirit' )
                ) );
            } else {
                error_log( 'Contact form script file does not exist: ' . $script_path );
            }
        }
        add_action( 'wp_enqueue_scripts', 'enqueue_contact_form_ajax_scripts' );
    }

    if ( ! function_exists( 'enqueue_mailchimp_form_ajax_scripts' ) ) {
        function enqueue_mailchimp_form_ajax_scripts() {
            $script_rel_path = '/ajax/js/mc_form_ajax.js'; // relative to theme root
            $script_path     = get_template_directory() . $script_rel_path;
            $script_uri      = get_template_directory_uri() . $script_rel_path;

            // Only enqueue if the file exists
            if ( file_exists( $script_path ) ) {
                wp_enqueue_script( 'mc_form_ajax_script', $script_uri, array( 'jquery' ), null, true );

                // Pass dynamic data to JS
                wp_localize_script( 'mc_form_ajax_script', 'mc_form_ajax_object', array(
                    'ajax_url'             => admin_url( 'admin-ajax.php' ),
                    'user_id'              => get_current_user_id(),
                    'recaptcha_site_key'   => RECAPTCHA_SITE_KEY,
                    'msg_privacy_required' => __( 'You must agree to the privacy policy.', 'borspirit' ),
                    'msg_sending'          => __( 'Subscribing…', 'borspirit' ),
                    'msg_success'          => __( 'You have been subscribed successfully!', 'borspirit' ),
                    'msg_error_sending'    => __( 'There was an error while processing your subscription.', 'borspirit' ),
                    'msg_unexpected'       => __( 'An unexpected error occurred.', 'borspirit' ),
                    'msg_network_error'    => __( 'A network error occurred.', 'borspirit' )
                ) );
            } else {
                error_log( 'Mailchimp script file does not exist: ' . $script_path );
            }
        }
        add_action( 'wp_enqueue_scripts', 'enqueue_mailchimp_form_ajax_scripts' );
    }

    if ( ! function_exists( 'enqueue_prq_quiz_ajax_scripts' ) ) {
        function enqueue_prq_quiz_ajax_scripts() {
            $script_rel_path = '/ajax/js/prq_quiz_ajax.js'; // relative to your theme root
            $script_path     = get_template_directory() . $script_rel_path;
            $script_uri      = get_template_directory_uri() . $script_rel_path;

            // Only enqueue if the file exists
            if ( file_exists( $script_path ) ) {
                wp_enqueue_script( 'prq_quiz_ajax_script', $script_uri, array( 'jquery' ), null, true );

                // Pass dynamic data to JS
                wp_localize_script( 'prq_quiz_ajax_script', 'prq_quiz_ajax_object', array(
                    'ajax_url'          => admin_url( 'admin-ajax.php' ),
                    'nonce'             => wp_create_nonce('prq_quiz_action'),
                    'user_id'           => get_current_user_id(),
                    'msg_sending'       => __( 'Processing…', 'borspirit' ),
                    'msg_success'       => __( 'The recommendation is complete!', 'borspirit' ),
                    'msg_error_sending' => __( 'There was an error while processing your request.', 'borspirit' ),
                    'msg_unexpected'    => __( 'An unexpected error occurred.', 'borspirit' ),
                    'msg_network_error' => __( 'A network error occurred.', 'borspirit' )
                ) );
            } else {
                error_log( 'BorSpirit quiz script file does not exist: ' . $script_path );
            }
        }
        //add_action( 'wp_enqueue_scripts', 'enqueue_prq_quiz_ajax_scripts' );
    }

    if ( ! function_exists( 'enqueue_save_post_ajax_scripts' ) ) {
        function enqueue_save_post_ajax_scripts() {
            $script_rel_path = '/ajax/js/save_post_ajax.js';
            $script_path = get_template_directory() . $script_rel_path;
            $script_uri = get_template_directory_uri() . $script_rel_path;

            // Check if the script file actually exists before enqueuing
            if ( file_exists( $script_path ) ) {
                // Enqueue the script with jQuery as a dependency, to be loaded in the footer
                wp_enqueue_script( 'save_post_ajax_script', $script_uri, array( 'jquery' ), null, true );

                // Localize script for use in JS
                wp_localize_script( 'save_post_ajax_script', 'save_post_ajax_object', array( 
                    'ajax_url' => admin_url( 'admin-ajax.php' ) 
                ) );
            } else {
                // Log an error if the script file doesn't exist
                error_log( 'Script file does not exist: ' . $script_path );
            }
        }
        add_action( 'wp_enqueue_scripts', 'enqueue_save_post_ajax_scripts' );
    }