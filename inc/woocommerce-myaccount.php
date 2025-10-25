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
                            'A(z) <code>%s</code> oldal sablonja hiányzik. Kérjük, hozza létre a fájlt: <code>%s</code>',
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
                            'A(z) <code>%s</code> oldal sablonja hiányzik. Kérjük, hozza létre a fájlt: <code>%s</code>',
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
                    'dashboard'         => __( 'Dashboard', 'woocommerce' ),
                    'orders'            => __( 'Orders', 'woocommerce' ),
                    'downloads'         => __( 'Downloads', 'woocommerce' ),
                    'edit-address'      => __( 'Address', 'woocommerce' ),
                    'edit-account'      => __( 'Account details', 'woocommerce' ),
                    'customer-logout'   => __( 'Logout', 'woocommerce' ),
                    'subscriptions'     => __( 'My Subscription', 'woocommerce-subscriptions' ),
                    'view-subscription' => __( 'Subscription Details', 'woocommerce-subscriptions' ),
                    'beauty-profile'    => __( 'Beauty profile', 'borspirit' ),
                    'bookmarks'         => __( 'Bookmarks', 'borspirit' ),
                    'email-marketing'   => __( 'Newsletter', 'borspirit' ),
                    'product-bookmarks' => __( 'Product Bookmarks', 'borspirit' ),
                    'post-bookmarks'    => __( 'Post Bookmarks', 'borspirit' ),
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
