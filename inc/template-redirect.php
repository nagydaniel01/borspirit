<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    
    if ( ! function_exists( 'borspirit_register_attribute_rewrites' ) ) {
        /**
         * Registers rewrite rules for all WooCommerce product attribute base slugs.
         * This allows URLs like /boraszat/ to be recognized by WordPress.
         *
         * @return void
         */
        function borspirit_register_attribute_rewrites() {
            if ( ! class_exists( 'WooCommerce' ) ) {
                return;
            }

            $attributes = wc_get_attribute_taxonomies();

            if ( empty( $attributes ) ) {
                return;
            }

            foreach ( $attributes as $attribute ) {
                if ($attribute->attribute_public === 1) {
                    $base_slug = sanitize_title( $attribute->attribute_name ); // e.g., boraszat
                    add_rewrite_rule(
                        "^{$base_slug}/?$",
                        'index.php?borspirit_product_attribute=' . $base_slug,
                        'top'
                    );
                }
            }
        }
        add_action( 'init', 'borspirit_register_attribute_rewrites' );
    }

    if ( ! function_exists( 'borspirit_register_attribute_query_var' ) ) {
        /**
         * Registers a query variable so WordPress can recognize custom attribute requests.
         *
         * @param array $vars Existing query variables.
         * @return array Modified query variables.
         */
        function borspirit_register_attribute_query_var( $vars ) {
            $vars[] = 'borspirit_product_attribute';
            return $vars;
        }
        add_filter( 'query_vars', 'borspirit_register_attribute_query_var' );
    }

    if ( ! function_exists('product_attribute_base_template_redirect') ) {
        /**
         * Dynamically intercepts requests to WooCommerce attribute base slugs
         * and serves custom archive templates instead of 404.
         *
         * Example: /boraszat/ → loads pa_boraszat list template
         *
         * @return void
         */
        function product_attribute_base_template_redirect() {
            $request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

            $custom_attr = get_query_var('borspirit_product_attribute');
            if ( ! $custom_attr ) {
                return;
            }

            if ( ! class_exists( 'WooCommerce' ) ) {
                return;
            }

            // Fetch all registered WooCommerce product attributes
            $attributes = wc_get_attribute_taxonomies();
            if ( empty( $attributes ) ) {
                return;
            }

            foreach ( $attributes as $attribute ) {
                if ( $attribute->attribute_public === 1 ) {
                    $taxonomy  = wc_attribute_taxonomy_name( $attribute->attribute_name ); // e.g. "pa_boraszat"
                    $base_slug = sanitize_title( $attribute->attribute_name );             // e.g. "boraszat"

                    if ( taxonomy_exists( $taxonomy ) && $request_uri === $base_slug ) {
                        // Path to custom template for this attribute
                        $attribute_template = get_template_directory() . "/templates/listings/{$taxonomy}-list.php";

                        if ( file_exists( $attribute_template ) ) {
                            set_query_var( 'taxonomy', $taxonomy );
                            include $attribute_template;
                            exit;
                        } else {
                            // Fallback to generic attribute list template
                            $fallback_template = get_template_directory() . "/templates/listings/pa_taxonomy-list.php";
                            if ( file_exists( $fallback_template ) ) {
                                set_query_var( 'taxonomy', $taxonomy );
                                include $fallback_template;
                                exit;
                            }
                        }
                    }
                }
            }
        }

        add_action( 'template_redirect', 'product_attribute_base_template_redirect' );
    }

    /**
     * Intercepts requests to WooCommerce attribute base slugs
     * and serves custom archive templates instead of 404.
     *
     * Example: /szin/ → loads pa_color list template
     *
     * @return void
     */
    /*
    if ( ! function_exists('product_attribute_base_template_redirect') ) {
        function product_attribute_base_template_redirect() {
            $request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

            // Map attribute taxonomies to "base slugs"
            // Key = WooCommerce taxonomy (always prefixed with pa_), value = slug you want to use in URL
            $attribute_mappings = [
                'pa_boraszat' => 'boraszat',
            ];

            foreach ( $attribute_mappings as $taxonomy => $base_slug ) {
                if ( taxonomy_exists( $taxonomy ) && $request_uri === $base_slug ) {
                    // Path to custom template for this attribute
                    $attribute_template = get_template_directory() . "/templates/listings/{$taxonomy}-list.php";

                    if ( file_exists( $attribute_template ) ) {
                        set_query_var( 'taxonomy', $taxonomy );
                        include $attribute_template;
                        exit;
                    } else {
                        // Fallback to generic attribute list template
                        $fallback_template = get_template_directory() . "/templates/listings/pa_taxonomy-list.php";
                        if ( file_exists( $fallback_template ) ) {
                            set_query_var( 'taxonomy', $taxonomy );
                            include $fallback_template;
                            exit;
                        }
                    }
                }
            }
        }
        //add_action( 'template_redirect', 'product_attribute_base_template_redirect' );
    }
    */