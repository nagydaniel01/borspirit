<?php
    if ( ! function_exists( 'show_under_construction_page' ) ) {
        /**
         * Display a Coming Soon page to guests (non-logged-in users) while hiding the site.
         *
         * This function prevents access to the site frontend for guests by loading a
         * Coming Soon template part. It skips the behavior for logged-in users,
         * admin area, AJAX, REST API, CLI, cron jobs, and login pages.
         *
         * @return void Outputs the Coming Soon page and exits for guests.
         */
        function show_under_construction_page() {
            // Avoid running in WP CLI or during WP Cron jobs.
            if ( defined('WP_CLI') && WP_CLI ) return;
            if ( defined('DOING_CRON') && DOING_CRON ) return;

            // Only run if UNDER_CONSTRUCTION_MODE is defined and true
            if ( ! defined('UNDER_CONSTRUCTION_MODE') || ! UNDER_CONSTRUCTION_MODE ) {
                return;
            }

            // Skip for logged-in users, admin dashboard, AJAX requests, REST API requests, login page, and WP JSON requests.
            if (
                is_user_logged_in() || is_admin() || ( defined('DOING_AJAX') && DOING_AJAX ) || ( defined('REST_REQUEST') && REST_REQUEST ) || ( isset($_SERVER['REQUEST_URI']) && (strpos($_SERVER['REQUEST_URI'], '/wp-login.php') !== false || strpos($_SERVER['REQUEST_URI'], '/wp-json/') !== false) )
            ) {
                return;
            }

            // Prevent any output if headers have already been sent.
            if ( headers_sent() ) {
                // Headers already sent, cannot safely send Coming Soon page.
                // Log this or handle as needed.
                error_log('Cannot show Coming Soon page: headers already sent.');
                return;
            }

            // Send proper headers to avoid caching and set content type.
            status_header(200);
            nocache_headers();
            header('Content-Type: text/html; charset=UTF-8');

            // Load the Coming Soon template.
            $template_loaded = false;
            $template_file = locate_template('under-construction.php');

            if ( $template_file ) {
                load_template($template_file);
                $template_loaded = true;
            }

            if ( $template_loaded ) {
                exit;
            }
        }

        add_action('init', 'show_under_construction_page');
    }