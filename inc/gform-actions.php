<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! function_exists( 'custom_gform_html_email_template' ) ) {
        /**
         * Customize the HTML email template for Gravity Forms notifications.
         *
         * This function modifies the email body to include a custom header and footer
         * for HTML-formatted emails triggered by form submissions (admin notifications).
         *
         * @param array  $email          The email details to be sent.
         * @param string $message_format The format of the message. Can be 'text' or 'html'.
         * @param array  $notification   The full notification object from Gravity Forms.
         * @param array  $entry          The form entry associated with the notification.
         *
         * @return array Modified email array with a custom HTML template if applicable.
         */
        function custom_gform_html_email_template( $email, $message_format, $notification, $entry ) {
            // Only apply to HTML-formatted messages
            if ( $message_format != 'html' ) {
                return $email;
            }

            // Only apply to admin notifications triggered by form submissions
            if ( ! isset( $notification['event'] ) || $notification['event'] !== 'form_submission' ) {
                return $email;
            }

            $subject = $email['subject'];
            $message = $email['message'];

            // Start output buffering to capture header HTML
            ob_start();

            $header_path = locate_template('templates/emails/email-header.php');
            if ( $header_path ) {
                include $header_path;
            }
            
            $email_header = ob_get_clean();

            // Start output buffering to capture footer HTML
            ob_start();
            
            $footer_path = locate_template('templates/emails/email-footer.php');
            if ( $footer_path ) {
                include $footer_path;
            }

            $email_footer = ob_get_clean();

            // Build full email template with placeholders
            $template = $email_header . '{message}' . $email_footer;

            // Replace {message} with the actual content
            $email['message'] = str_replace('{message}', $message, $template);
            $email['message_format'] = 'html';

            return $email;
        }
        add_filter( 'gform_pre_send_email', 'custom_gform_html_email_template', 10, 4 );
    }
