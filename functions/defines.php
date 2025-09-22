<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    // Theme Constants
    define( 'THEME_NAME', get_bloginfo( 'name' ) );
    define( 'TEXT_DOMAIN', basename( get_template_directory() ) );

    define( 'TEMPLATE_PATH', get_template_directory() );
    define( 'TEMPLATE_DIR_URI', esc_url( get_template_directory_uri() ) );

    define( 'ASSETS_URI', TEMPLATE_DIR_URI . '/assets/img/' );
    define( 'ASSETS_URI_JS', TEMPLATE_DIR_URI . '/assets/src/js/' );
    define( 'ASSETS_URI_CSS', TEMPLATE_DIR_URI . '/assets/src/css/' );

    define( 'AJAX_URI', TEMPLATE_DIR_URI . '/ajax/js/' );

    define( 'HOME_URL', esc_url( home_url() ) );
    define( 'SITE_URL', esc_url( site_url() ) );
    define( 'ADMIN_AJAX', esc_url( admin_url( 'admin-ajax.php' ) ) );

    // Asset Versioning
    $style_path = TEMPLATE_PATH . '/assets/dist/css/styles.css';
    $version    = file_exists( $style_path ) ? filemtime( $style_path ) : '1.0.0';
    define( 'ASSETS_VERSION', $version );

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

    // ACF Fields (Logo, Placeholder)
    if ( function_exists( 'get_field' ) ) {
        $under_construction_mode = get_field( 'under_construction_mode', 'option' );
        $placeholder_image       = get_field( 'placeholder_image', 'option' );
        
        if ( ! defined( 'UNDER_CONSTRUCTION_MODE' ) ) {
            define( 'UNDER_CONSTRUCTION_MODE', $under_construction_mode );
        }
        
        if ( ! defined( 'PLACEHOLDER_IMAGE_ID' ) ) {
            define( 'PLACEHOLDER_IMAGE_ID', $placeholder_image['ID'] );
        }
    }

    // WooCommerce Page IDs
    if ( class_exists( 'WooCommerce' ) ) {
        define( 'SHOP_PAGE_ID', wc_get_page_id( 'shop' ) );
        define( 'CART_PAGE_ID', wc_get_page_id( 'cart' ) );
        define( 'CHECKOUT_PAGE_ID', wc_get_page_id( 'checkout' ) );
        define( 'MY_ACCOUNT_PAGE_ID', wc_get_page_id( 'myaccount' ) );
    }
