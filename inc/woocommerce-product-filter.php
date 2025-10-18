<?php
    defined( 'ABSPATH' ) || exit;

    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    if ( ! function_exists( 'product_filter' ) ) {
        /**
         * Handles AJAX request to filter products.
         *
         * Expects a POST parameter 'filter_object' containing filter criteria.
         * Loads the product query template and outputs the results.
         *
         * @return void
         */
        function product_filter() {
            $filter_object = isset( $_POST['filter_object'] ) ? $_POST['filter_object'] : '';

            // Locate query template
            $query_template = get_template_directory() . '/template-parts/queries/query-product.php';
            if ( ! file_exists( $query_template ) ) {
                wp_send_json_error( [
                    'message' => __( 'Query template not found.', 'your-text-domain' )
                ], 500 );
            }

            include $query_template;

            wp_die();
        }
        //add_action( 'wp_ajax_product_filter', 'product_filter' );
        //add_action( 'wp_ajax_nopriv_product_filter', 'product_filter' );
    }

    if ( ! function_exists( 'product_attributes_filter' ) ) {
        /**
         * Handles AJAX request to filter product attributes.
         *
         * Expects a POST parameter 'filter_object' containing filter criteria.
         * Loads the product attributes query template and outputs the results.
         *
         * @return void
         */
        function product_attributes_filter() {
            $filter_object = isset( $_POST['filter_object'] ) ? $_POST['filter_object'] : '';

            // Locate query template
            $query_template = get_template_directory() . '/template-parts/queries/query-product-attributes.php';
            if ( ! file_exists( $query_template ) ) {
                wp_send_json_error( [
                    'message' => __( 'Query template not found.', 'your-text-domain' )
                ], 500 );
            }

            include $query_template;

            wp_die();
        }
        //add_action( 'wp_ajax_product_attributes_filter', 'product_attributes_filter' );
        //add_action( 'wp_ajax_nopriv_product_attributes_filter', 'product_attributes_filter' );
    }
