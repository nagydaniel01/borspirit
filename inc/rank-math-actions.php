<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    
    if ( ! function_exists( 'my_noindex_woocommerce_system_pages' ) ) {
        /**
         * Modify Rank Math robots meta for WooCommerce technical/system pages.
         * 
         * Rank Math $robots array accepts these keys:
         * - index / noindex
         * - follow / nofollow
         * - noarchive
         * - nosnippet
         * - noimageindex
         * - notranslate
         * - max-snippet
         * - max-image-preview
         * - max-video-preview
         *
         * This forces NOINDEX on pages that should not appear in search engines:
         * - Cart
         * - Checkout (including all sub-steps)
         * - My Account (including all endpoints)
         * - WooCommerce endpoint URLs
         * - Add-to-cart redirect URLs (optional)
         *
         * @param array $robots Existing Rank Math robots settings.
         * @return array Modified robots settings.
         */
        function my_noindex_woocommerce_system_pages( $robots ) {

            // WooCommerce Cart page
            if ( function_exists( 'is_cart' ) && is_cart() ) {
                $robots['index'] = 'noindex';
            }

            // WooCommerce Checkout page
            if ( function_exists( 'is_checkout' ) && is_checkout() ) {
                $robots['index'] = 'noindex';
            }

            // WooCommerce My Account page (base + endpoints)
            if ( function_exists( 'is_account_page' ) && is_account_page() ) {
                $robots['index'] = 'noindex';
            }

            // WooCommerce endpoints (orders, view-order, edit-address, lost-password, etc.)
            if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url() ) {
                $robots['index'] = 'noindex';
            }

            // Add-to-cart URLs (?add-to-cart=123)
            if ( isset( $_GET['add-to-cart'] ) ) {
                $robots['index'] = 'noindex';
            }

            // Keep links followable
            $robots['follow'] = 'follow';

            return $robots;
        }
        add_filter( 'rank_math/frontend/robots', 'my_noindex_woocommerce_system_pages' );
    }
