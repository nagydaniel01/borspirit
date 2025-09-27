<?php
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

            if ( ! $product instanceof WC_Product ) {
                return;
            }

            ob_start();

            echo '<div class="woocommerce-product-badge">';

            // Only show sale badge if product is on sale
            if ( $product->is_on_sale() ) {
                $text = esc_html__( 'Sale!', 'woocommerce' );
                echo '<span class="badge badge--onsale">' . $text . '</span>';
            }

            // Always show custom badges
            wc_wine_store_new_flash( $product );
            wc_wine_store_bestseller_flash( $product );
            wc_wine_store_limited_stock_flash( $product );
            //wc_wine_store_discount_flash( $product );
            wc_wine_store_award_flash( $product );
            //wc_wine_store_organic_flash( $product );
            //wc_wine_store_winetype_flash( $product );

            echo '</div>';

            return ob_get_clean();
        }

        // Runs when product IS on sale
        add_filter( 'woocommerce_sale_flash', 'wc_wine_store_sale_flash', 20, 3 );

        // Runs when product is NOT on sale (shop loop + single product)
        add_action( 'woocommerce_before_shop_loop_item_title', function() {
            global $product;
            if ( $product && ! $product->is_on_sale() ) {
                echo wc_wine_store_sale_flash();
            }
        }, 5 );

        add_action( 'woocommerce_before_single_product_summary', function() {
            global $product;
            if ( $product && ! $product->is_on_sale() ) {
                echo wc_wine_store_sale_flash();
            }
        }, 10 );
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
            $post_date = get_the_date( 'Y-m-d', $product->get_id() );
            $now       = date( 'Y-m-d' );
            $datediff  = strtotime( $now ) - strtotime( $post_date );

            if ( $datediff / DAY_IN_SECONDS <= $days_new ) {
                echo '<span class="badge badge--new">' . esc_html__( 'New Arrival', TEXT_DOMAIN ) . '</span>';
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
        function wc_wine_store_bestseller_flash( $product, $sales_threshold = 20 ) {
            if ( $product->get_total_sales() >= $sales_threshold ) {
                echo '<span class="badge badge--bestseller">' . esc_html__( 'Best Seller', TEXT_DOMAIN ) . '</span>';
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
        function wc_wine_store_limited_stock_flash( $product, $stock_limit = 10 ) {
            if ( $product->managing_stock() && $product->get_stock_quantity() <= $stock_limit ) {
                echo '<span class="badge badge--limited">' . esc_html__( 'Limited Stock', TEXT_DOMAIN ) . '</span>';
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
            if ( $product->is_on_sale() && $product->get_regular_price() > 0 ) {
                $percentage = round(
                    ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100
                );
                echo '<span class="badge badge--discount">' . sprintf( esc_html__( '%s%% Off', TEXT_DOMAIN ), $percentage ) . '</span>';
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
            $terms = get_the_terms( $product->get_id(), 'award' );

            if ( $terms && ! is_wp_error( $terms ) ) {
                /*
                foreach ( $terms as $term ) {
                    echo '<span class="badge badge--winetype">' . esc_html( $term->name ) . '</span>';
                }
                */

                echo '<span class="badge badge--award">' . esc_html__( 'Award Winner', 'TEXT_DOMAIN' ) . '</span>';
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
        function wc_wine_store_organic_flash( $product ) {
            if ( 'yes' === get_post_meta( $product->get_id(), 'organic', true ) ) {
                echo '<span class="badge badge--organic">' . esc_html__( 'Organic', TEXT_DOMAIN ) . '</span>';
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
            $terms = get_the_terms( $product->get_id(), 'wine_type' );

            if ( $terms && ! is_wp_error( $terms ) ) {
                foreach ( $terms as $term ) {
                    echo '<span class="badge badge--winetype">' . esc_html( $term->name ) . '</span>';
                }
            }
        }
    }
