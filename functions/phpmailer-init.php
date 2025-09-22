<?php
    if ( ! function_exists( 'mailtrap' ) ) {
        /**
         * Configure PHPMailer to use Mailtrap SMTP for local email testing.
         *
         * @param PHPMailer $phpmailer The PHPMailer instance to configure.
         * 
         * @return void
         */
        function mailtrap( $phpmailer ) {
            $phpmailer->isSMTP();
            $phpmailer->Host       = 'sandbox.smtp.mailtrap.io';
            $phpmailer->SMTPAuth   = true;
            $phpmailer->Port       = 2525;
            $phpmailer->Username   = '03e7fa1f5124ab';
            $phpmailer->Password   = '4be51df465bc15';
        }
        // Uncomment to activate Mailtrap config
        //add_action( 'phpmailer_init', 'mailtrap' );
    }

    if ( ! function_exists( 'rd_mailtrap' ) ) {
        /**
         * Configure PHPMailer to use an alternative Mailtrap SMTP account for local email testing.
         *
         * @param PHPMailer $phpmailer The PHPMailer instance to configure.
         * 
         * @return void
         */
        function rd_mailtrap( $phpmailer ) {
            $phpmailer->isSMTP();
            $phpmailer->Host       = 'sandbox.smtp.mailtrap.io';
            $phpmailer->SMTPAuth   = true;
            $phpmailer->Port       = 2525;
            $phpmailer->Username   = 'e259dd697fd18f';
            $phpmailer->Password   = '67e22e4ddb9475';
        }
        // Uncomment to activate Mailtrap config
        add_action( 'phpmailer_init', 'rd_mailtrap' );
    }

    if ( ! function_exists( 'cpanel_mail' ) ) {
        /**
         * Configure PHPMailer to use cPanel default SMTP for sending emails.
         *
         * @param PHPMailer $phpmailer The PHPMailer instance to configure.
         * 
         * @return void
         */
        function cpanel_mail( $phpmailer ) {
            $phpmailer->isSMTP();

            // Replace these values with your cPanel email account info
            $phpmailer->Host       = 'mail.yourdomain.com'; // Usually mail.yourdomain.com
            $phpmailer->SMTPAuth   = true;
            $phpmailer->Port       = 465;                   // Use 465 for SSL, 587 for TLS
            $phpmailer->SMTPSecure = 'ssl';                 // 'ssl' or 'tls'
            $phpmailer->Username   = 'you@yourdomain.com';  // Your cPanel email address
            $phpmailer->Password   = 'your-email-password'; // Your email password
            $phpmailer->From       = 'you@yourdomain.com';  // Optional: set From address
            $phpmailer->FromName   = 'Your Name';           // Optional: set From name
        }

        //add_action( 'phpmailer_init', 'cpanel_mail' );
    }

    if ( ! function_exists( 'log_mailer_errors' ) ) {
        /**
         * Logs email sending errors to a log file.
         *
         * @param WP_Error $wp_error The WordPress error object.
         */
        function log_mailer_errors( WP_Error $wp_error ) {
            $log_file = ABSPATH . 'mail.log'; // Absolute path to log file
    
            // Format the error message with timestamp
            $error_message = sprintf(
                "[%s] Mailer Error: %s\n",
                date('d-M-Y H:i:s T'),
                $wp_error->get_error_message()
            );
    
            // Attempt to append the error message to the log file
            try {
                if (!file_put_contents($log_file, $error_message, FILE_APPEND | LOCK_EX)) {
                    error_log('Unable to write to mail log file: ' . $log_file);
                }
            } catch ( Exception $e ) {
                error_log('Exception caught while logging mailer error: ' . $e->getMessage());
            }
        }
        add_action('wp_mail_failed', 'log_mailer_errors', 10, 1);
    }

    if ( ! function_exists( 'wrap_wp_mail_with_php_templates' ) ) {
        /**
         * Wraps all WordPress emails with custom header and footer templates.
         *
         * This function hooks into 'wp_mail' and wraps the email message with
         * header and footer PHP templates located in the theme.
         *
         * @param array $args {
         *     Array of wp_mail() arguments.
         *
         *     @type string          $to          Email address.
         *     @type string          $subject     Email subject.
         *     @type string          $message     Email message.
         *     @type string|array    $headers     Optional. Headers.
         *     @type string|array    $attachments Optional. Attachments.
         * }
         * @return array Modified $args array with message wrapped in templates.
         */
        function wrap_wp_mail_with_php_templates( $args ) {
            // Disable wrapping for WooCommerce emails
            if (defined('WC_PLUGIN_FILE') && did_action('woocommerce_before_resend_order_emails') || doing_action('woocommerce_email')) {
                return $args;
            }

            // Define header and footer template paths
            $header_file = get_template_directory() . '/templates/emails/email-header.php';
            $footer_file = get_template_directory() . '/templates/emails/email-footer.php';

            $header = '';
            $footer = '';

            // Make the subject available to the templates
            $email_subject = isset( $args['subject'] ) ? $args['subject'] : '';

            // Include header if file exists
            if ( file_exists( $header_file ) ) {
                try {
                    ob_start();
                    include $header_file;
                    $header = ob_get_clean();
                } catch ( Exception $e ) {
                    error_log( 'Email header inclusion failed: ' . $e->getMessage() );
                    $header = '';
                }
            } else {
                //error_log( 'Email header template not found: ' . $header_file );
            }

            // Include footer if file exists
            if ( file_exists( $footer_file ) ) {
                try {
                    ob_start();
                    include $footer_file;
                    $footer = ob_get_clean();
                } catch ( Exception $e ) {
                    error_log( 'Email footer inclusion failed: ' . $e->getMessage() );
                    $footer = '';
                }
            } else {
                //error_log( 'Email footer template not found: ' . $footer_file );
            }

            // Ensure original message exists
            $original_message = isset( $args['message'] ) ? $args['message'] : '';

            // Wrap the message with header and footer
            $args['message'] = $header . $original_message . $footer;

            // Ensure content type is HTML
            add_filter( 'wp_mail_content_type', function() {
                return 'text/html';
            });

            return $args;
        }
        add_filter( 'wp_mail', 'wrap_wp_mail_with_php_templates' );
    }
