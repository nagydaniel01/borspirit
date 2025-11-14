<?php
    defined( 'ABSPATH' ) || exit;

    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    if ( ! function_exists( 'custom_add_bookmark_endpoints' ) ) {
        /**
         * Register endpoints for product and post bookmarks.
         *
         * @since 1.0.0
         * @return void
         */
        function custom_add_bookmark_endpoints() {
            add_rewrite_endpoint( 'product-bookmarks', EP_ROOT | EP_PAGES );
            add_rewrite_endpoint( 'post-bookmarks', EP_ROOT | EP_PAGES );
            add_rewrite_endpoint( 'previously-purchased', EP_ROOT | EP_PAGES );
        }
        add_action( 'init', 'custom_add_bookmark_endpoints' );
    }

    if ( ! function_exists( 'custom_my_account_menu_items' ) ) {
        /**
         * Add Product and Post Bookmarks to the WooCommerce My Account menu.
         *
         * @since 1.0.0
         *
         * @param array $items Existing menu items.
         * @return array Modified menu items.
         */
        function custom_my_account_menu_items( $items ) {
            $new = array();

            foreach ( $items as $key => $value ) {
                $new[ $key ] = $value;

                if ( 'dashboard' === $key ) {
                    $new['product-bookmarks'] = __( 'Product Bookmarks', 'borspirit' );
                    $new['post-bookmarks']    = __( 'Post Bookmarks', 'borspirit' );
                    $new['previously-purchased'] = __( 'Previously Purchased', 'borspirit' );
                }
            }

            return $new;
        }
        add_filter( 'woocommerce_account_menu_items', 'custom_my_account_menu_items' );
    }

    if ( ! function_exists( 'custom_product_bookmarks_content' ) ) {
        /**
         * Load Product Bookmarks page template.
         *
         * Template hierarchy:
         *  - yourtheme/woocommerce/myaccount/product-bookmarks.php
         *  - yourtheme/myaccount/product-bookmarks.php
         *  - fallback to plugin's /templates/myaccount/product-bookmarks.php
         *
         * @since 1.0.0
         * @return void
         */
        function custom_product_bookmarks_content() {
            $section_name = __( 'Product Bookmarks', 'borspirit' );
            $section_file = 'woocommerce/myaccount/product-bookmarks.php';

            $template = locate_template( $section_file );

            if ( $template ) {
                load_template( $template, true );
            } else {
                printf(
                    '<div class="alert alert-danger" role="alert">%s</div>',
                    sprintf(
                        __(
                            'The template for <code>%s</code> page is missing. Please create the file: <code>%s</code>',
                            'borspirit'
                        ),
                        esc_html( $section_name ),
                        esc_html( $section_file )
                    )
                );
            }
        }
        add_action( 'woocommerce_account_product-bookmarks_endpoint', 'custom_product_bookmarks_content' );
    }

    if ( ! function_exists( 'custom_post_bookmarks_content' ) ) {
        /**
         * Load Post Bookmarks page template.
         *
         * Template hierarchy:
         *  - yourtheme/woocommerce/myaccount/previously-purchased.php
         *  - yourtheme/myaccount/previously-purchased.php
         *  - fallback to plugin's /templates/myaccount/previously-purchased.php
         *
         * @since 1.0.0
         * @return void
         */
        function custom_post_bookmarks_content() {
            $section_name = __( 'Previously Purchased', 'borspirit' );
            $section_file = 'woocommerce/myaccount/previously-purchased.php';

            $template = locate_template( $section_file );

            if ( $template ) {
                load_template( $template, true );
            } else {
                printf(
                    '<div class="alert alert-danger" role="alert">%s</div>',
                    sprintf(
                        __(
                            'The template for <code>%s</code> page is missing. Please create the file: <code>%s</code>',
                            'borspirit'
                        ),
                        esc_html( $section_name ),
                        esc_html( $section_file )
                    )
                );
            }
        }
        add_action( 'woocommerce_account_previously-purchased_endpoint', 'custom_post_bookmarks_content' );
    }

    if ( ! function_exists( 'custom_post_bookmarks_content' ) ) {
        /**
         * Load Post Bookmarks page template.
         *
         * Template hierarchy:
         *  - yourtheme/woocommerce/myaccount/post-bookmarks.php
         *  - yourtheme/myaccount/post-bookmarks.php
         *  - fallback to plugin's /templates/myaccount/post-bookmarks.php
         *
         * @since 1.0.0
         * @return void
         */
        function custom_post_bookmarks_content() {
            $section_name = __( 'Post Bookmarks', 'borspirit' );
            $section_file = 'woocommerce/myaccount/post-bookmarks.php';

            $template = locate_template( $section_file );

            if ( $template ) {
                load_template( $template, true );
            } else {
                printf(
                    '<div class="alert alert-danger" role="alert">%s</div>',
                    sprintf(
                        __(
                            'The template for <code>%s</code> page is missing. Please create the file: <code>%s</code>',
                            'borspirit'
                        ),
                        esc_html( $section_name ),
                        esc_html( $section_file )
                    )
                );
            }
        }
        add_action( 'woocommerce_account_post-bookmarks_endpoint', 'custom_post_bookmarks_content' );
    }

    if ( ! function_exists( 'custom_my_account_endpoint_titles' ) ) {
        /**
         * Change WooCommerce My Account page title dynamically.
         *
         * @since 1.0.0
         *
         * @param string $title The current post title.
         * @param int    $id    The queried post ID.
         * @return string Modified title for the My Account page.
         */
        function custom_my_account_endpoint_titles( $title, $id ) {
            if ( is_account_page() && ! is_admin() && get_queried_object_id() === $id && is_user_logged_in() ) {

                global $wp_query;

                $titles = array(
                    'dashboard'          => __( 'Dashboard', 'woocommerce' ),
                    'orders'             => __( 'Orders', 'woocommerce' ),
                    'downloads'          => __( 'Downloads', 'woocommerce' ),
                    'edit-address'       => __( 'Addresses', 'woocommerce' ),
                    'edit-account'       => __( 'Account details', 'woocommerce' ),
                    'customer-logout'    => __( 'Logout', 'woocommerce' ),
                    'subscriptions'      => __( 'My Subscription', 'woocommerce-subscriptions' ),
                    'view-subscription'  => __( 'Subscription Details', 'woocommerce-subscriptions' ),
                    'points-and-rewards' => __( 'Pontok', 'borspirit' ),
                    'product-bookmarks'  => __( 'Product Bookmarks', 'borspirit' ),
                    'post-bookmarks'     => __( 'Post Bookmarks', 'borspirit' ),
                );

                foreach ( $titles as $endpoint => $endpoint_title ) {
                    if ( isset( $wp_query->query_vars[ $endpoint ] ) ) {
                        return $endpoint_title;
                    }
                }

                $current_user = wp_get_current_user();
                $user_name    = $current_user->display_name ?: $current_user->first_name;

                return sprintf( __( 'Hello %s!', 'borspirit' ), esc_html( $user_name ) );
            }

            return $title;
        }
        add_filter( 'the_title', 'custom_my_account_endpoint_titles', 10, 2 );
    }

    if ( ! function_exists( 'conditionally_remove_downloads_tab' ) ) {
        /**
         * Remove the Downloads tab from WooCommerce My Account menu
         * if the current user has no downloadable products.
         *
         * @param array $items Array of My Account menu items.
         * @return array Modified array of My Account menu items.
         */
        function conditionally_remove_downloads_tab( $items ) {
            $user_id = get_current_user_id();

            if ( $user_id ) {
                $downloads = WC()->customer->get_downloadable_products();

                if ( empty( $downloads ) ) {
                    unset( $items['downloads'] );
                }
            }

            return $items;
        }
        add_filter( 'woocommerce_account_menu_items', 'conditionally_remove_downloads_tab', 999 );
    }