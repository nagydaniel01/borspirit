<?php
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
                    'loading_text'    => __( 'Loading...', TEXT_DOMAIN ),
                    'post_comment'    => __( 'Post Comment' ),
                    'error_adding'    => __( 'Error while adding comment', TEXT_DOMAIN ),
                    'error_timeout'   => __( 'Error: Server doesn’t respond.', TEXT_DOMAIN )
                ) );
            } else {
                error_log( 'Script file does not exist: ' . $script_path );
            }
        }
        add_action( 'wp_enqueue_scripts', 'enqueue_comment_form_ajax_scripts' );
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
                    'msg_privacy_required' => __( 'You must agree to the privacy policy.', TEXT_DOMAIN ),
                    'msg_registering'      => __( 'Registering…', TEXT_DOMAIN ),
                    'msg_success'          => __( 'Successfully registered!', TEXT_DOMAIN ),
                    'msg_error_sending'    => __( 'There was an error while sending your registration.', TEXT_DOMAIN ),
                    'msg_unexpected'       => __( 'An unexpected error occurred.', TEXT_DOMAIN ),
                    'msg_network_error'    => __( 'A network error occurred.', TEXT_DOMAIN )
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
                    'msg_privacy_required' => __( 'You must agree to the privacy policy.', TEXT_DOMAIN ),
                    'msg_sending'          => __( 'Sending…', TEXT_DOMAIN ),
                    'msg_success'          => __( 'Message sent successfully!', TEXT_DOMAIN ),
                    'msg_error_sending'    => __( 'There was an error while sending your message.', TEXT_DOMAIN ),
                    'msg_unexpected'       => __( 'An unexpected error occurred.', TEXT_DOMAIN ),
                    'msg_network_error'    => __( 'A network error occurred.', TEXT_DOMAIN )
                ) );
            } else {
                error_log( 'Contact form script file does not exist: ' . $script_path );
            }
        }
        add_action( 'wp_enqueue_scripts', 'enqueue_contact_form_ajax_scripts' );
    }

    if ( ! function_exists( 'enqueue_testimonial_form_ajax_scripts' ) ) {
        function enqueue_testimonial_form_ajax_scripts() {
            $script_rel_path = '/ajax/js/testimonial_form_ajax.js'; // relative to theme root
            $script_path = get_template_directory() . $script_rel_path;
            $script_uri  = get_template_directory_uri() . $script_rel_path;

            // Only enqueue if the file exists
            if ( file_exists( $script_path ) ) {
                wp_enqueue_script( 'testimonial_form_ajax_script', $script_uri, array( 'jquery' ), null, true );

                // Pass dynamic data to JS
                wp_localize_script( 'testimonial_form_ajax_script', 'testimonial_form_ajax_object', array(
                    'ajax_url'             => admin_url( 'admin-ajax.php' ),
                    'user_id'              => get_current_user_id(),
                    'msg_privacy_required' => __( 'You must agree to the privacy policy.', TEXT_DOMAIN ),
                    'msg_sending'          => __( 'Sending…', TEXT_DOMAIN ),
                    'msg_success'          => __( 'Message sent successfully!', TEXT_DOMAIN ),
                    'msg_error_sending'    => __( 'There was an error while sending your message.', TEXT_DOMAIN ),
                    'msg_unexpected'       => __( 'An unexpected error occurred.', TEXT_DOMAIN ),
                    'msg_network_error'    => __( 'A network error occurred.', TEXT_DOMAIN )
                ) );
            } else {
                error_log( 'Testimonial form script file does not exist: ' . $script_path );
            }
        }
        //add_action( 'wp_enqueue_scripts', 'enqueue_testimonial_form_ajax_scripts' );
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
                    'msg_privacy_required' => __( 'You must agree to the privacy policy.', TEXT_DOMAIN ),
                    'msg_sending'          => __( 'Subscribing…', TEXT_DOMAIN ),
                    'msg_success'          => __( 'You have been subscribed successfully!', TEXT_DOMAIN ),
                    'msg_error_sending'    => __( 'There was an error while processing your subscription.', TEXT_DOMAIN ),
                    'msg_unexpected'       => __( 'An unexpected error occurred.', TEXT_DOMAIN ),
                    'msg_network_error'    => __( 'A network error occurred.', TEXT_DOMAIN )
                ) );
            } else {
                error_log( 'Mailchimp script file does not exist: ' . $script_path );
            }
        }
        add_action( 'wp_enqueue_scripts', 'enqueue_mailchimp_form_ajax_scripts' );
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