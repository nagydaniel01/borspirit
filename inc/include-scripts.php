<?php
    defined( 'ABSPATH' ) || exit;
    
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
            wp_enqueue_style( 'borspirit' . '-theme', TEMPLATE_DIR_URI . '/assets/dist/css/styles.css', array(), ASSETS_VERSION );
            wp_enqueue_script( 'borspirit' . '-theme', TEMPLATE_DIR_URI . '/assets/dist/js/scripts.js', array( 'jquery' ), ASSETS_VERSION, true );

            // Localize script for use in JS
            wp_localize_script( 'borspirit' . '-theme', 'localize', array(
                'ajaxurl'      => admin_url( 'admin-ajax.php' ),
                'resturl'      => esc_url( rest_url( 'wp/v2/posts' ) ),
                'themeurl'     => TEMPLATE_DIR_URI,
                'siteurl'      => SITE_URL,
                'ag_min_age'      => get_option('ag_min_age', 18),
                'ag_cookie_days'  => get_option('ag_cookie_days', 30),
                'ag_redirect_url' => get_option('ag_redirect_url', 'https://www.google.com'),
                //'start_time'   => current_time( 'c' ),
                'translations' => array(
                    'read_more' => __( 'Show more', 'borspirit' ),
                    'read_less' => __( 'Show less', 'borspirit' ),
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

    if ( ! function_exists( 'recaptcha_scripts' ) ) {
        /**
         * Enqueue Google reCAPTCHA v3 script.
         *
         * @return void
         */
        function recaptcha_scripts() {
            $recaptcha_site_key = '6LcOnQ4sAAAAAF_Mcnr5Adg4xtHC4sP46nC8LKjn';
    
            wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . esc_attr( $recaptcha_site_key ), [], null, true );
        }
        add_action( 'wp_enqueue_scripts', 'recaptcha_scripts', 110 );
    }

    if ( ! function_exists( 'fb_meta_pixel_script' ) ) {
        /**
         * Add Meta Pixel tracking code via wp_enqueue_scripts.
         *
         * This function registers a dummy script handle and attaches
         * the Meta Pixel initialization code as inline JavaScript.
         * It also outputs the <noscript> tracking image fallback.
         *
         * @return void
         */
        function fb_meta_pixel_script() {

            // Register an empty script to attach inline JavaScript to
            wp_register_script( 'meta-pixel', false );
            wp_enqueue_script( 'meta-pixel' );

            // Meta Pixel inline JavaScript
            $pixel_script = "
                !function(f,b,e,v,n,t,s){
                    if(f.fbq)return;n=f.fbq=function(){
                        n.callMethod ? n.callMethod.apply(n,arguments) : n.queue.push(arguments)
                    };
                    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                    n.queue=[];t=b.createElement(e);t.async=!0;
                    t.src=v;s=b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t,s)
                }(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');

                fbq('init', '1178515017580965');
                fbq('track', 'PageView');
            ";

            wp_add_inline_script( 'meta-pixel', $pixel_script );

            // Add the noscript pixel
            echo '<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1178515017580965&ev=PageView&noscript=1" /></noscript>';
        }
        add_action( 'wp_enqueue_scripts', 'fb_meta_pixel_script', 120 );
    }
