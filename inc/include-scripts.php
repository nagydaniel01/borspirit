<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    
    if ( ! function_exists( 'theme_scripts' ) ) {
        /**
         * Dequeues unnecessary styles and enqueues theme-specific CSS and JS assets.
         *
         * Also localizes script data for use in JavaScript (e.g., ajax URL, theme URL, translations).
         *
         * @return void
         */
        function theme_scripts() {
            // Enqueue theme CSS and JS
            wp_enqueue_style( TEXT_DOMAIN . '-theme', TEMPLATE_DIR_URI . '/assets/dist/css/styles.css', array(), ASSETS_VERSION );
            wp_enqueue_script( TEXT_DOMAIN . '-theme', TEMPLATE_DIR_URI . '/assets/dist/js/scripts.js', array( 'jquery' ), ASSETS_VERSION, true );

            // Localize script for use in JS
            wp_localize_script( TEXT_DOMAIN . '-theme', 'localize', array(
                'ajaxurl'      => admin_url( 'admin-ajax.php' ),
                'resturl'      => esc_url( rest_url( 'wp/v2/posts' ) ),
                'themeurl'     => TEMPLATE_DIR_URI,
                'siteurl'      => SITE_URL,
                'ag_min_age'      => get_option('ag_min_age', 18),
                'ag_cookie_days'  => get_option('ag_cookie_days', 30),
                'ag_redirect_url' => get_option('ag_redirect_url', 'https://www.google.com'),
                //'start_time'   => current_time( 'c' ),
                'translations' => array(
                    'read_more' => __( 'Show more', TEXT_DOMAIN ),
                    'read_less' => __( 'Show less', TEXT_DOMAIN ),
                ),
            ) );

            //wp_enqueue_style('dashicons');

            if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
                wp_enqueue_script( 'comment-reply' );
            }

            /*
            // Check if the current page uses a given template
            $target_templates = array(
                'templates/page-felhivasok.php',
                'templates/page-rendezvenyek.php',
                'templates/page-sidebar.php',
                'templates/page-hirek-es-esemenyek.php'
            );

            if ( is_home() || is_page_template( $target_templates ) ) {
                // Pass event post data to MomentJS
                $event_data = get_upcoming_events_data();
                if ( ! empty( $event_data ) ) {
                    wp_add_inline_script(
                        'theme',
                        'var MomentData = ' . wp_json_encode( $event_data ) . ';',
                        'after'
                    );
                }
            }
            */

            // Disable WooCommerce brands CSS (handle may vary depending on plugin/theme)
            wp_dequeue_style( 'brands-styles' );
            wp_deregister_style( 'brands-styles' );
        }
        add_action( 'wp_enqueue_scripts', 'theme_scripts', 100 );
    }
