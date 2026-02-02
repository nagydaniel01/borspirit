<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! function_exists( 'detect_environment' ) ) {
        /**
         * Detects the current environment based on the domain/host.
         *
         * Rules:
         *  - *.hu         → 'production'
         *  - staging.*.hu → 'staging'
         *  - *.test       → 'development'
         *  - fallback     → 'development'
         *
         * @return string One of 'production', 'staging', or 'development'
         */
        function detect_environment() {
            $host = $_SERVER['HTTP_HOST'] ?? '';

            // Normalize host (lowercase, remove port)
            $host = strtolower( preg_replace( '/:.*/', '', $host ) );

            // PRODUCTION: all *.hu except staging.*.hu
            if ( str_ends_with( $host, '.hu' ) ) {
                return 'production';
            }

            // STAGING: staging.domain.hu or staging.anything.hu
            if ( preg_match( '/^staging\..+\.hu$/', $host ) ) {
                return 'staging';
            }

            // DEVELOPMENT: *.test domains
            if ( str_ends_with( $host, '.test' ) ) {
                return 'development';
            }

            // Fallback
            return 'development';
        }
    }

    // Define Environment Constants
    if ( ! defined( 'WP_ENV' ) ) {
        define( 'WP_ENV', detect_environment() );
    }

    // Define Theme Constants
    if ( ! defined( 'THEME_NAME' ) ) {
        define( 'THEME_NAME', get_bloginfo( 'name' ) );
    }

    if ( ! defined( 'TEMPLATE_PATH' ) ) {
        define( 'TEMPLATE_PATH', get_template_directory() );
    }

    if ( ! defined( 'TEMPLATE_DIR_URI' ) ) {
        define( 'TEMPLATE_DIR_URI', esc_url( get_template_directory_uri() ) );
    }

    // Define Asset Versioning
    if ( ! defined( 'ASSETS_VERSION' ) ) {
        $style_path = TEMPLATE_PATH . '/assets/dist/css/styles.css';
        $version    = file_exists( $style_path ) ? filemtime( $style_path ) : '1.0.0';
        define( 'ASSETS_VERSION', $version );
    }

    if ( ! defined( 'ASSETS_URI' ) ) {
        define( 'ASSETS_URI', TEMPLATE_DIR_URI . '/assets/img/' );
    }

    if ( ! defined( 'ASSETS_URI_JS' ) ) {
        define( 'ASSETS_URI_JS', TEMPLATE_DIR_URI . '/assets/src/js/' );
    }

    if ( ! defined( 'ASSETS_URI_CSS' ) ) {
        define( 'ASSETS_URI_CSS', TEMPLATE_DIR_URI . '/assets/src/css/' );
    }

    if ( ! defined( 'AJAX_URI' ) ) {
        define( 'AJAX_URI', TEMPLATE_DIR_URI . '/ajax/js/' );
    }

    // Define URLs Constants

    if ( ! defined( 'HOME_URL' ) ) {
        define( 'HOME_URL', esc_url( home_url() ) );
    }

    if ( ! defined( 'SITE_URL' ) ) {
        define( 'SITE_URL', esc_url( site_url() ) );
    }

    if ( ! defined( 'ADMIN_AJAX' ) ) {
        define( 'ADMIN_AJAX', esc_url( admin_url( 'admin-ajax.php' ) ) );
    }

    // Define Page IDs Constants
    if ( ! defined( 'HOME_PAGE_ID' ) ) {
        define( 'HOME_PAGE_ID', get_option( 'page_on_front' ) );
    }

    if ( ! defined( 'BLOG_PAGE_ID' ) ) {
        define( 'BLOG_PAGE_ID', get_option( 'page_for_posts' ) );
    }

    if ( ! defined( 'PRIVACY_POLICY_PAGE_ID' ) ) {
        define( 'PRIVACY_POLICY_PAGE_ID', get_option( 'wp_page_for_privacy_policy' ) );
    }

    if ( ! defined( 'TERMS_PAGE_ID' ) ) {
        define( 'TERMS_PAGE_ID', get_option( 'wp_page_for_terms' ) );
    }

    // Define 404 Page Constants
    $page_404 = get_pages( array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => '404.php',
    ) );

    if ( ! defined( 'ERROR_404_PAGE_ID' ) ) {
        define( 'ERROR_404_PAGE_ID', ! empty( $page_404 ) ? $page_404[0]->ID : 0 );
    }

    // Define Custom "Thank you" Page Constants
    $page_thank_you = get_pages( array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'templates/page-thank-you.php',
    ) );

    if ( ! defined( 'THANK_YOU_PAGE_ID' ) ) {
        define( 'THANK_YOU_PAGE_ID', ! empty( $page_thank_you ) ? $page_thank_you[0]->ID : 0 );
    }

    // Define ACF Fields Constants
    if ( function_exists( 'get_field' ) ) {
        $under_construction_mode = get_field( 'under_construction_mode', 'option' ) ?? false;
        $placeholder_img         = get_field( 'placeholder_img', 'option' ) ?? [];
        
        if ( ! defined( 'UNDER_CONSTRUCTION_MODE' ) ) {
            define( 'UNDER_CONSTRUCTION_MODE', $under_construction_mode );
        }
        
        if ( ! defined( 'PLACEHOLDER_IMG_SRC' ) ) {

            // Check if a custom placeholder image is set and has a URL
            $placeholder_img_src = is_array( $placeholder_img ) && isset( $placeholder_img['url'] ) ? $placeholder_img['url'] : null;

            // Fallback: use WooCommerce placeholder image if WooCommerce is active
            if ( empty( $placeholder_img_src ) && class_exists( 'WooCommerce' ) ) {
                $placeholder_img_src = wc_placeholder_img_src();
            }

            // Optional: fallback to a local theme image if neither ACF nor WooCommerce provide one
            if ( empty( $placeholder_img_src ) ) {
                $placeholder_img_src = get_template_directory_uri() . '/assets/src/images/placeholder.png';
            }

            define( 'PLACEHOLDER_IMG_SRC', $placeholder_img_src );
        }
    }

    // Define WooCommerce Page IDs Constants
    if ( class_exists( 'WooCommerce' ) ) {
        if ( ! defined( 'SHOP_PAGE_ID' ) ) {
            define( 'SHOP_PAGE_ID', wc_get_page_id( 'shop' ) );
        }
        if ( ! defined( 'CART_PAGE_ID' ) ) {
            define( 'CART_PAGE_ID', wc_get_page_id( 'cart' ) );
        }
        if ( ! defined( 'CHECKOUT_PAGE_ID' ) ) {
            define( 'CHECKOUT_PAGE_ID', wc_get_page_id( 'checkout' ) );
        }
        if ( ! defined( 'MY_ACCOUNT_PAGE_ID' ) ) {
            define( 'MY_ACCOUNT_PAGE_ID', wc_get_page_id( 'myaccount' ) );
        }
    }

    // Define Google reCAPTCHA Constants
    if ( ! defined( 'RECAPTCHA_SITE_KEY' ) ) {
        define( 'RECAPTCHA_SITE_KEY', '6LcOnQ4sAAAAAF_Mcnr5Adg4xtHC4sP46nC8LKjn' );
    }

    if ( ! defined( 'RECAPTCHA_SECRET_KEY' ) ) {
        define( 'RECAPTCHA_SECRET_KEY', '6LcOnQ4sAAAAAEvEqeGkNE6b9X4rWxGMVhdR6CNU' );
    }

    // Define Facebook Pixel and Google Tag Manager Constants
    if ( ! defined( 'FB_PIXEL_ID' ) ) {
        define( 'FB_PIXEL_ID', '1178515017580965' );
    }

    if ( ! defined( 'GTM_ID' ) ) {
        define( 'GTM_ID', 'GTM-NNHDJK5K' );
    }
