<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }
    
    if ( ! function_exists( 'wc_wine_store_sale_flash' ) ) {
        /**
         * Display custom badges (including sale).
         *
         * @param string     $html    Default sale HTML.
         * @param WC_Product $post    Post object.
         * @param WC_Product $product Product instance.
         * @return string Custom HTML with all badges.
         */
        function wc_wine_store_sale_flash( $html = '', $post = null, $product = null ) {
            global $product;

            if ( ! $product ) {
                return;
            }

            $selected_flashes = get_field( 'product_badge_flashes', 'option' ) ?? [];

            ob_start();

            echo '<div class="woocommerce-product-badge">';

            /*
            // Only show sale badge if product is on sale
            if ( $product->is_on_sale() ) {
                $text = esc_html__( 'Sale!', 'woocommerce' );
                echo '<span class="badge badge--onsale">' . $text . '</span>';
            }
            */

            // Check each badge separately
            if ( in_array( 'wc_wine_store_custom_sale_flash', $selected_flashes, true ) ) {
                wc_wine_store_custom_sale_flash( $product );
            }

            if ( in_array( 'wc_wine_store_new_flash', $selected_flashes, true ) ) {
                wc_wine_store_new_flash( $product );
            }

            if ( in_array( 'wc_wine_store_bestseller_flash', $selected_flashes, true ) ) {
                wc_wine_store_bestseller_flash( $product );
            }

            if ( in_array( 'wc_wine_store_limited_stock_flash', $selected_flashes, true ) ) {
                wc_wine_store_limited_stock_flash( $product );
            }

            if ( in_array( 'wc_wine_store_discount_flash', $selected_flashes, true ) ) {
                wc_wine_store_discount_flash( $product );
            }

            if ( in_array( 'wc_wine_store_award_flash', $selected_flashes, true ) ) {
                wc_wine_store_award_flash( $product );
            }

            if ( in_array( 'wc_wine_store_new_vintage_flash', $selected_flashes, true ) ) {
                wc_wine_store_new_vintage_flash( $product );
            }

            if ( in_array( 'wc_wine_store_category_flash', $selected_flashes, true ) ) {
                wc_wine_store_category_flash( $product, 'honap-bora' );
            }

            echo '</div>';

            return ob_get_clean();
        }

        // Runs when product IS on sale
        add_filter( 'woocommerce_sale_flash', 'wc_wine_store_sale_flash', 20, 3 );

        // Runs when product is NOT on sale (shop loop + single product)
        add_action( 'woocommerce_before_shop_loop_item_title', function() {
            global $product;

            if ( ! $product ) {
                return;
            }

            if ( $product && ! $product->is_on_sale() ) {
                echo wc_wine_store_sale_flash();
            }
        }, 5 );

        add_action( 'woocommerce_before_single_product_summary', function() {
            global $product;

            if ( ! $product ) {
                return;
            }

            if ( $product && ! $product->is_on_sale() ) {
                echo wc_wine_store_sale_flash();
            }
        }, 10 );
    }

    if ( ! function_exists( 'wc_wine_store_custom_sale_flash' ) ) {
        /**
         * Badge: Sale (default).
         *
         * @param WC_Product $product WooCommerce product.
         * @return void
         */
        function wc_wine_store_custom_sale_flash( $product ) {
            if ( ! $product instanceof WC_Product ) {
                return;
            }

            if ( $product->is_on_sale() ) {
                $text = esc_html__( 'Sale!', 'woocommerce' );
                echo '<span class="badge badge--onsale">' . $text . '</span>';
            }
        }
    }

    if ( ! function_exists( 'wc_wine_store_discount_flash' ) ) {
        /**
         * Badge: Discount (percentage off).
         *
         * @param WC_Product $product WooCommerce product.
         * @return void
         */
        function wc_wine_store_discount_flash( $product ) {
            if ( ! $product instanceof WC_Product ) {
                return;
            }

            if ( $product->is_on_sale() && $product->get_regular_price() > 0 ) {
                $percentage = round(
                    ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100
                );

                printf(
                    '<span class="badge badge--onsale">%s <span class="badge__discount">%d%%</span></span>',
                    esc_html__( 'Sale!', 'woocommerce' ),
                    esc_html( $percentage )
                );
            }
        }
    }

    if ( ! function_exists( 'wc_wine_store_award_flash' ) ) {
        /**
         * Badge: Award Winning (custom field).
         *
         * @param WC_Product $product WooCommerce product.
         * @return void
         */
        function wc_wine_store_award_flash( $product ) {
            if ( ! $product instanceof WC_Product ) {
                return;
            }

            $terms = get_the_terms( $product->get_id(), 'award' );

            if ( $terms && ! is_wp_error( $terms ) ) {
                /*
                foreach ( $terms as $term ) {
                    echo '<span class="badge badge--award">' . esc_html( $term->name ) . '</span>';
                }
                */

                echo '<span class="badge badge--award">' . esc_html__( 'Award Winner', 'borspirit' ) . '</span>';
            }
        }
    }

    if ( ! function_exists( 'wc_wine_store_bestseller_flash' ) ) {
        /**
         * Badge: Best Seller (sales threshold).
         *
         * @param WC_Product $product WooCommerce product.
         * @param int        $sales_threshold Minimum sales to qualify.
         * @return void
         */
        function wc_wine_store_bestseller_flash( $product, $sales_threshold = 100 ) {
            if ( ! $product instanceof WC_Product ) {
                return;
            }

            if ( $product->get_total_sales() >= $sales_threshold ) {
                echo '<span class="badge badge--bestseller">' . esc_html__( 'Best Seller', 'borspirit' ) . '</span>';
            }
        }
    }

    if ( ! function_exists( 'wc_wine_store_limited_stock_flash' ) ) {
        /**
         * Badge: Limited Stock.
         *
         * @param WC_Product $product WooCommerce product.
         * @param int        $stock_limit Max quantity to trigger badge.
         * @return void
         */
        function wc_wine_store_limited_stock_flash( $product, $stock_limit = 5 ) {
            if ( ! $product instanceof WC_Product ) {
                return;
            }

            if ( $product->managing_stock() && $product->get_stock_quantity() <= $stock_limit ) {
                echo '<span class="badge badge--limited">' . esc_html__( 'Limited Stock', 'borspirit' ) . '</span>';
            }
        }
    }

    if ( ! function_exists( 'wc_wine_store_new_flash' ) ) {
        /**
         * Badge: New Arrival (last X days).
         *
         * @param WC_Product $product WooCommerce product.
         * @param int        $days_new Days considered as "new".
         * @return void
         */
        function wc_wine_store_new_flash( $product, $days_new = 30 ) {
            if ( ! $product instanceof WC_Product ) {
                return;
            }
            
            $post_date = get_the_date( 'Y-m-d', $product->get_id() );
            $now       = date( 'Y-m-d' );
            $datediff  = strtotime( $now ) - strtotime( $post_date );

            if ( $datediff / DAY_IN_SECONDS <= $days_new ) {
                echo '<span class="badge badge--new">' . esc_html__( 'New Arrival', 'borspirit' ) . '</span>';
            }
        }
    }

    if ( ! function_exists( 'wc_wine_store_new_vintage_flash' ) ) {
        /**
         * Badge: New Vintage.
         *
         * Shows badge if product's pa_evjarat equals the current year.
         *
         * @param WC_Product $product WooCommerce product.
         * @return void
         */
        function wc_wine_store_new_vintage_flash( $product ) {
            if ( ! $product instanceof WC_Product ) {
                return;
            }

            // Get product terms for 'pa_evjarat'
            $terms = wc_get_product_terms( $product->get_id(), 'pa_evjarat', array( 'fields' => 'names' ) );

            if ( ! empty( $terms ) ) {
                $current_year = date( 'Y' );

                // If any of the terms matches the current year
                if ( in_array( $current_year, $terms, true ) ) {
                    echo '<span class="badge badge--new-vintage">' . esc_html__( 'New Vintage', 'borspirit' ) . '</span>';
                }
            }
        }
    }

    if ( ! function_exists( 'wc_wine_store_category_flash' ) ) {
        /**
         * Badge: Specific Product Category.
         *
         * @param WC_Product $product WooCommerce product.
         * @param string $target_category_slug Slug of the category to display badge for.
         * @return void
         */
        function wc_wine_store_category_flash( $product, $target_category_slug ) {
            if ( ! $product instanceof WC_Product ) {
                return;
            }

            // Get product categories
            $terms = get_the_terms( $product->get_id(), 'product_cat' );

            if ( $terms && ! is_wp_error( $terms ) ) {
                foreach ( $terms as $term ) {
                    // Only show badge if the category matches
                    if ( $term->slug === $target_category_slug ) {
                        echo '<span class="badge badge--' . esc_attr( $target_category_slug ) . '">' . esc_html( $term->name ) . '</span>';
                    }
                }
            }
        }
    }

    if ( ! function_exists( 'wc_wine_store_tag_flash' ) ) {
        /**
         * Badge: Specific Product Tag.
         *
         * @param WC_Product $product WooCommerce product.
         * @param string $target_category_slug Slug of the tag to display badge for.
         * @return void
         */
        function wc_wine_store_tag_flash( $product, $target_category_slug ) {
            if ( ! $product instanceof WC_Product ) {
                return;
            }

            // Get product tags
            $terms = get_the_terms( $product->get_id(), 'product_tag' );

            if ( $terms && ! is_wp_error( $terms ) ) {
                foreach ( $terms as $term ) {
                    // Only show badge if the tag matches
                    if ( $term->slug === $target_category_slug ) {
                        echo '<span class="badge badge--' . esc_attr( $target_category_slug ) . '">' . esc_html( $term->name ) . '</span>';
                    }
                }
            }
        }
    }

    if ( ! function_exists( 'wc_wine_store_winetype_flash' ) ) {
        /**
         * Badge: Wine Type (custom taxonomy).
         *
         * @param WC_Product $product WooCommerce product.
         * @return void
         */
        function wc_wine_store_winetype_flash( $product ) {
            if ( ! $product instanceof WC_Product ) {
                return;
            }

            $terms = get_the_terms( $product->get_id(), 'wine_type' );

            if ( $terms && ! is_wp_error( $terms ) ) {
                foreach ( $terms as $term ) {
                    echo '<span class="badge badge--winetype">' . esc_html( $term->name ) . '</span>';
                }
            }
        }
    }

    if ( ! function_exists( 'wc_wine_store_organic_flash' ) ) {
        /**
         * Badge: Organic (custom field).
         *
         * @param WC_Product $product WooCommerce product.
         * @return void
         */
        /*
        function wc_wine_store_organic_flash( $product ) {
            if ( ! $product instanceof WC_Product ) {
                return;
            }

            if ( 'yes' === get_post_meta( $product->get_id(), 'organic', true ) ) {
                echo '<span class="badge badge--organic">' . esc_html__( 'Organic', 'borspirit' ) . '</span>';
            }
        }
        */
    }

    if ( ! function_exists( 'acf_load_product_badge_flashes' ) ) {
        /**
         * Auto-populate ACF field with available WooCommerce product badge flashes.
         *
         * This will scan your theme/plugin for existing `wc_wine_store_*_flash` functions
         * and add them as selectable choices in an ACF field.
         *
         * Usage:
         * - Create an ACF field (Select or Checkbox).
         * - Set the field name to `product_badge_flashes` (or adjust filter below).
         * - Choices will automatically populate with badge flash functions.
         */
        function acf_load_product_badge_flashes( $field ) {
            
            // Reset choices
            $field['choices'] = array();

            // Define all badge flash functions you want available
            $badge_functions = array(
                'wc_wine_store_custom_sale_flash'   => __( 'Sale Badge', 'borspirit' ),
                'wc_wine_store_discount_flash'      => __( 'Discount Badge', 'borspirit' ),
                'wc_wine_store_award_flash'         => __( 'Award Winner Badge', 'borspirit' ),
                'wc_wine_store_bestseller_flash'    => __( 'Best Seller Badge', 'borspirit' ),
                'wc_wine_store_limited_stock_flash' => __( 'Limited Stock Badge', 'borspirit' ),
                'wc_wine_store_new_flash'           => __( 'New Arrival Badge', 'borspirit' ),
                'wc_wine_store_new_vintage_flash'   => __( 'New Vintage Badge', 'borspirit' ),
                'wc_wine_store_category_flash'      => __( 'Wine of the month Badge', 'borspirit' ),
            );

            // Loop through and only add existing functions (safety check)
            foreach ( $badge_functions as $function => $label ) {
                if ( function_exists( $function ) ) {
                    $field['choices'][ $function ] = $label;
                }
            }

            return $field;
        }

        // Hook into ACF field loading (adjust field name as needed)
        add_filter( 'acf/load_field/name=product_badge_flashes', 'acf_load_product_badge_flashes' );
    }
