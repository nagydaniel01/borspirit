<?php
    defined( 'ABSPATH' ) || exit;

    // Theme Constants
    define( 'THEME_NAME', get_bloginfo( 'name' ) );

    define( 'TEMPLATE_PATH', get_template_directory() );
    define( 'TEMPLATE_DIR_URI', esc_url( get_template_directory_uri() ) );

    // Asset Versioning
    $style_path = TEMPLATE_PATH . '/assets/dist/css/styles.css';
    $version    = file_exists( $style_path ) ? filemtime( $style_path ) : '1.0.0';
    define( 'ASSETS_VERSION', $version );

    define( 'ASSETS_URI', TEMPLATE_DIR_URI . '/assets/img/' );
    define( 'ASSETS_URI_JS', TEMPLATE_DIR_URI . '/assets/src/js/' );
    define( 'ASSETS_URI_CSS', TEMPLATE_DIR_URI . '/assets/src/css/' );

    define( 'AJAX_URI', TEMPLATE_DIR_URI . '/ajax/js/' );

    define( 'HOME_URL', esc_url( home_url() ) );
    define( 'SITE_URL', esc_url( site_url() ) );
    define( 'ADMIN_AJAX', esc_url( admin_url( 'admin-ajax.php' ) ) );

    // Page IDs
    define( 'HOME_PAGE_ID', get_option( 'page_on_front' ) );
    define( 'BLOG_PAGE_ID', get_option( 'page_for_posts' ) );
    define( 'PRIVACY_POLICY_PAGE_ID', get_option( 'wp_page_for_privacy_policy' ) );
    define( 'TERMS_PAGE_ID', get_option( 'wp_page_for_terms' ) );

    // 404 Page
    $page_404 = get_pages( array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => '404.php',
    ) );

    if ( ! empty( $page_404 ) ) {
        define( 'ERROR_404_PAGE_ID', $page_404[0]->ID );
    }

    // Custom "Thank you" Page
    $page_thank_you = get_pages( array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'templates/page-thank-you.php',
    ) );

    if ( ! empty( $page_thank_you ) ) {
        define( 'THANK_YOU_PAGE_ID', $page_thank_you[0]->ID );
    }

    // ACF Fields
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

    // WooCommerce Page IDs
    if ( class_exists( 'WooCommerce' ) ) {
        define( 'SHOP_PAGE_ID', wc_get_page_id( 'shop' ) );
        define( 'CART_PAGE_ID', wc_get_page_id( 'cart' ) );
        define( 'CHECKOUT_PAGE_ID', wc_get_page_id( 'checkout' ) );
        define( 'MY_ACCOUNT_PAGE_ID', wc_get_page_id( 'myaccount' ) );
    }

    // Google reCAPTCHA
    define( 'RECAPTCHA_SITE_KEY', '6LcOnQ4sAAAAAF_Mcnr5Adg4xtHC4sP46nC8LKjn' );
    define( 'RECAPTCHA_SECRET_KEY', '6LcOnQ4sAAAAAEvEqeGkNE6b9X4rWxGMVhdR6CNU' );

    // Mailchimp
    if ( class_exists( 'MailchimpService' ) ) {
        define( 'MAILCHIMP_API_KEY', 'ae0da8ad3bcffc67ee33226e942bc534-us3' ); // For 'daniel.nagy0125@gmail.com' account
        define( 'MAILCHIMP_AUDIENCE_ID', '409e8ec6a8' );                       // For 'daniel.nagy0125@gmail.com' account
    }
