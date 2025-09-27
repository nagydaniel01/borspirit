<?php
    /**
     * WooCommerce Customizations
     * This file contains customizations for WooCommerce.
     */

    if ( ! class_exists( 'WooCommerce' ) ) return;

    // Optional: Disable all default WooCommerce stylesheets
    add_filter('woocommerce_enqueue_styles', '__return_empty_array');

    // Remove default WooCommerce wrappers
    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

    // Remove WooCommerce breadcrumb
    //remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

    // Remove WooCommerce sidebar
    //remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

    // Remove WooCommerce sidebar on single product pages
    function remove_woocommerce_sidebar_single_product() {
        if ( is_product() ) {
            remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
        }
    }
    add_action( 'template_redirect', 'remove_woocommerce_sidebar_single_product' );
    // ---------------------------------------------
    // Add blocks to shop and product single
    // ---------------------------------------------

    if ( ! function_exists( 'custom_woocommerce_output_content_wrapper' ) ) {
        /**
         * Output the opening wrapper for WooCommerce content.
         */
        function custom_woocommerce_output_content_wrapper() {
            if (is_shop() || is_product_category() || is_tax() ) {
                echo '<main class="page page--default page--archive page--archive-product"><section class="section section--default"><div class="container">';
            } elseif (is_singular('product' ) ) {
                echo '<main class="page page--default page--single page--single-product"><section class="section section--single section--single-product">';
            }
        }
        add_action( 'woocommerce_before_main_content', 'custom_woocommerce_output_content_wrapper', 10 );
    }

    if ( ! function_exists( 'custom_woocommerce_output_content_wrapper_end' ) ) {
        /**
         * Output the closing wrapper for WooCommerce content.
         */
        function custom_woocommerce_output_content_wrapper_end() {
            if (is_shop() || is_product_category() || is_tax() ) {
                echo '</div></section></main>';
            } elseif (is_singular('product' ) ) {
                echo '</section></main>';
            }
        }
        add_action( 'woocommerce_after_main_content', 'custom_woocommerce_output_content_wrapper_end', 10 );
    }

    if ( ! function_exists( 'custom_shop_layout_open' ) ) {
        /**
         * Open layout before products loop.
         *
         * Uses Bootstrap flex utilities + column widths.
         * Sidebar will appear on the left (desktop) and stack under products on mobile.
         *
         * @hooked woocommerce_before_shop_loop - 5
         */
        function custom_shop_layout_open() {
            echo '<div class="shop-layout d-flex flex-column flex-lg-row">';

            // Products (right on desktop, above on mobile)
            echo '<div class="shop-layout__content col-lg-9 col-md-8 order-0 order-lg-1">';
        }
        add_action( 'woocommerce_before_shop_loop', 'custom_shop_layout_open', 5 );
    }

    if ( ! function_exists( 'custom_shop_layout_close' ) ) {
        /**
         * Close layout after products loop.
         *
         * Closes the flex container opened in custom_shop_layout_open().
         *
         * @hooked woocommerce_after_shop_loop - 50
         */
        function custom_shop_layout_close() {
            echo '</div>'; // Close .shop-layout__content

            // Sidebar (left on desktop, below on mobile)
            echo '<aside class="shop-layout__sidebar col-lg-3 col-md-4 order-1 order-lg-0">';
            do_action( 'woocommerce_sidebar' ); // Loads WooCommerce sidebar
            echo '</aside>';

            echo '</div>'; // Close .shop-layout
        }
        add_action( 'woocommerce_after_shop_loop', 'custom_shop_layout_close', 50 );
    }

    if ( ! function_exists( 'custom_woocommerce_single_product_main_wrapper' ) ) {
        /**
         * Wraps the single product main content in a custom section and container.
         */
        function custom_woocommerce_single_product_main_wrapper() {
            echo '<div class="section section--product-main"><div class="container"><div class="section__inner">';
        }
        add_action( 'woocommerce_before_single_product_summary', 'custom_woocommerce_single_product_main_wrapper', 5 );
    }

    if ( ! function_exists( 'custom_woocommerce_single_product_main_wrapper_end' ) ) {
        /**
         * Closes the custom section wrapper added around the single product main content.
         */
        function custom_woocommerce_single_product_main_wrapper_end() {
            echo '</div></div></div>';
        }
        add_action( 'woocommerce_after_single_product_summary', 'custom_woocommerce_single_product_main_wrapper_end', 5 );
    }

    if ( ! function_exists( 'custom_wrap_woocommerce_breadcrumbs' ) ) {
        /**
         * Output the opening wrapper for WooCommerce breadcrumbs
         *
         * @return void
         */
        function custom_breadcrumb_wrapper_start() {
            if (is_shop() || is_product_category( ) ) {
                echo '<div class="woocommerce-breadcrumb-wrapper">';
            } elseif (is_singular('product' ) ) {
                echo '<div class="woocommerce-breadcrumb-wrapper"><div class="container">';
            }
        }
        add_action( 'woocommerce_before_main_content', 'custom_breadcrumb_wrapper_start', 15 );

        /**
         * Output the closing wrapper for WooCommerce breadcrumbs
         *
         * @return void
         */
        function custom_breadcrumb_wrapper_end() {
            if (is_shop() || is_product_category( ) ) {
                echo '</div>';
            } elseif (is_singular('product' ) ) {
                echo '</div></div>';
            }
        }
        add_action( 'woocommerce_before_main_content', 'custom_breadcrumb_wrapper_end', 20 );
    }

    if ( ! function_exists( 'custom_woocommerce_notices_wrapper' ) ) {
        /**
         * Output opening wrapper for WooCommerce notices
         *
         * @return void
         */
        function custom_woocommerce_notices_wrapper() {
            echo '<div class="container">';
        }
        //add_action( 'woocommerce_before_shop_loop', 'custom_woocommerce_notices_wrapper', 5 );
        add_action( 'woocommerce_before_single_product', 'custom_woocommerce_notices_wrapper', 5 );
    }

    if ( ! function_exists( 'custom_woocommerce_notices_wrapper_end' ) ) {
        /**
         * Output closing wrapper for WooCommerce notices
         *
         * @return void
         */
        function custom_woocommerce_notices_wrapper_end() {
            echo '</div>';
        }
        //add_action( 'woocommerce_before_shop_loop', 'custom_woocommerce_notices_wrapper_end', 15 );
        add_action( 'woocommerce_before_single_product', 'custom_woocommerce_notices_wrapper_end', 15 );
    }

    add_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 ); // Default notices
    add_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 ); // Default notices

    if ( ! function_exists( 'custom_woocommerce_catalog_ordering_wrapper' ) ) {
        /**
         * Output opening wrapper for WooCommerce result count and ordering dropdown
         *
         * @return void
         */
        function custom_woocommerce_catalog_ordering_wrapper() {
            echo '<div class="woocommerce-tools">';
        }
        add_action('woocommerce_before_shop_loop', 'custom_woocommerce_catalog_ordering_wrapper', 10);
    }

    if ( ! function_exists( 'custom_woocommerce_catalog_ordering_wrapper_end' ) ) {
        /**
         * Output closing wrapper for WooCommerce result count and ordering dropdown
         *
         * @return void
         */
        function custom_woocommerce_catalog_ordering_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_before_shop_loop', 'custom_woocommerce_catalog_ordering_wrapper_end', 35 );
    }

    // ---------------------------------------------
    // Add blocks to product card
    // ---------------------------------------------

    // Product wrapper

    if ( ! function_exists( 'custom_product_wrapper' ) ) {
        /**
         * Open a wrapper around each WooCommerce product in the loop
         */
        function custom_product_wrapper() {
            echo '<div class="woocommerce-loop-product">';
        }
        add_action( 'woocommerce_before_shop_loop_item', 'custom_product_wrapper', 5 );
    }

    if ( ! function_exists( 'custom_product_wrapper_end' ) ) {
        /**
         * Close the wrapper around each WooCommerce product in the loop
         */
        function custom_product_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_after_shop_loop_item', 'custom_product_wrapper_end', 20 );
    }

    // Body wrapper

    if ( ! function_exists( 'custom_woocommerce_loop_body_wrapper' ) ) {
        /**
         * Open a wrapper around the WooCommerce product body.
         */
        function custom_woocommerce_loop_body_wrapper() {
            echo '<div class="woocommerce-loop-product__body">';
        }
        add_action( 'woocommerce_shop_loop_item_title', 'custom_woocommerce_loop_body_wrapper', 1 );
    }

    if ( ! function_exists( 'custom_woocommerce_loop_body_wrapper_end' ) ) {
        /**
         * Close the wrapper around the WooCommerce product body.
         */
        function custom_woocommerce_loop_body_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_after_shop_loop_item', 'custom_woocommerce_loop_body_wrapper_end', 1 );
    }

    // Image wrapper

    if ( ! function_exists( 'custom_woocommerce_loop_image_wrapper' ) ) {
        /**
         * Open a wrapper around the WooCommerce product image.
         */
        function custom_woocommerce_loop_image_wrapper() {
            echo '<div class="woocommerce-loop-product__image">';
        }
        add_action( 'woocommerce_before_shop_loop_item_title', 'custom_woocommerce_loop_image_wrapper', 1 );
    }

    if ( ! function_exists( 'custom_woocommerce_loop_image_wrapper_end' ) ) {
        /**
         * Close the wrapper around the WooCommerce product image.
         */
        function custom_woocommerce_loop_image_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_before_shop_loop_item_title', 'custom_woocommerce_loop_image_wrapper_end', 20 );
    }

    // Rating wrapper

    if ( ! function_exists( 'custom_woocommerce_loop_rating_wrapper' ) ) {
        /**
         * Outputs opening wrapper div for rating in WooCommerce product loop.
         */
        function custom_woocommerce_loop_rating_wrapper() {
            echo '<div class="woocommerce-loop-product__rating-wrapper">';
        }
        add_action( 'woocommerce_after_shop_loop_item_title', 'custom_woocommerce_loop_rating_wrapper', 4 );
    }

    if ( ! function_exists( 'custom_woocommerce_loop_rating_wrapper_end' ) ) {
        /**
         * Outputs closing wrapper div for rating in WooCommerce product loop.
         */
        function custom_woocommerce_loop_rating_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_after_shop_loop_item_title', 'custom_woocommerce_loop_rating_wrapper_end', 6 );
    }

    // Add to cart wrapper

    if ( ! function_exists( 'custom_woocommerce_loop_add_to_cart_wrapper' ) ) {
        /**
         * Open a wrapper around the WooCommerce add-to-cart button.
         */
        function custom_woocommerce_loop_add_to_cart_wrapper() {
            echo '<div class="woocommerce-loop-product__add-to-cart-wrapper">';
        }
        add_action( 'woocommerce_after_shop_loop_item', 'custom_woocommerce_loop_add_to_cart_wrapper', 9 );
    }

    if ( ! function_exists( 'custom_woocommerce_loop_add_to_cart_wrapper_end' ) ) {
        /**
         * Close the wrapper around the WooCommerce add-to-cart button.
         */
        function custom_woocommerce_loop_add_to_cart_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_after_shop_loop_item', 'custom_woocommerce_loop_add_to_cart_wrapper_end', 11 );
    }

    // ---------------------------------------------
    // Add blocks to product single
    // ---------------------------------------------

    if ( ! function_exists( 'move_short_description_under_title' ) ) {
        /**
         * Move the WooCommerce short description to appear directly under the product title.
         *
         * By default, WooCommerce displays the short description at priority 20 in the single product summary.
         * This function removes it from the default position and re-adds it immediately after the title (priority 6).
         *
         * Hook this function to 'woocommerce_single_product_summary'.
         *
         * @return void
         */
        function move_short_description_under_title() {
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
            add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 6);
        }
        add_action('woocommerce_single_product_summary', 'move_short_description_under_title', 1);
    }

    if ( ! function_exists( 'custom_woocommerce_single_product_gallery_wrapper' ) ) {
        /**
         * Output opening wrapper for the product gallery
         *
         * @return void
         */
        function custom_woocommerce_single_product_gallery_wrapper() {
            echo '<div class="gallery entry-gallery">';
        }
        add_action( 'woocommerce_before_single_product_summary', 'custom_woocommerce_single_product_gallery_wrapper', 5 );
    }

    if ( ! function_exists( 'custom_woocommerce_single_product_gallery_wrapper_end' ) ) {
        /**
         * Output closing wrapper for the product gallery
         *
         * @return void
         */
        function custom_woocommerce_single_product_gallery_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_before_single_product_summary', 'custom_woocommerce_single_product_gallery_wrapper_end', 20 );
    }

    if ( ! function_exists( 'custom_woocommerce_move_product_tabs' ) ) {
        /**
         * Setup function for moving WooCommerce product tabs.
         *
         * Removes the default tabs output and re-adds them wrapped in a container.
         *
         * @return void
         */
        function custom_woocommerce_move_product_tabs() {
            if ( function_exists( 'woocommerce_output_product_data_tabs' ) ) {
                remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
                add_action( 'woocommerce_after_single_product_summary', 'custom_woocommerce_move_product_tabs_inner', 10 );
            }
        }

        if ( ! function_exists( 'custom_woocommerce_move_product_tabs_inner' ) ) {
            /**
             * Outputs WooCommerce product tabs inside a Bootstrap container.
             *
             * @return void
             */
            function custom_woocommerce_move_product_tabs_inner() {
                echo '<section class="section section--product-tabs"><div class="container">';
                woocommerce_output_product_data_tabs();
                echo '</div></section>';
            }
        }
        //add_action( 'after_setup_theme', 'custom_woocommerce_move_product_tabs', 15 );
    }

    if ( ! function_exists( 'custom_woocommerce_move_upsells' ) ) {
        /**
         * Setup function for moving WooCommerce upsell products.
         *
         * Removes the default upsells output and re-adds them wrapped
         * in a Bootstrap container.
         *
         * @return void
         */
        function custom_woocommerce_move_upsells() {
            if ( function_exists( 'woocommerce_upsell_display' ) ) {
                remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
                add_action( 'woocommerce_after_single_product_summary', 'custom_woocommerce_move_upsells_inner', 15 );
            }
        }

        if ( ! function_exists( 'custom_woocommerce_move_upsells_inner' ) ) {
            /**
             * Outputs WooCommerce upsell products inside a Bootstrap container.
             *
             * @return void
             */
            function custom_woocommerce_move_upsells_inner() {
                global $product;

                if (!$product) {
                    return;
                }

                $upsells = $product->get_upsell_ids();

                if ( !empty( $upsells ) ) {
                    echo '<section class="section section--upsells"><div class="container">';
                    woocommerce_upsell_display();
                    echo '</div></section>';
                }
            }
        }
        add_action( 'after_setup_theme', 'custom_woocommerce_move_upsells', 15 );
    }

    if ( ! function_exists( 'custom_woocommerce_move_related_products' ) ) {
        /**
         * Setup function for moving WooCommerce related products.
         *
         * Removes the default related products output and re-adds them wrapped
         * in a Bootstrap container.
         *
         * @return void
         */
        function custom_woocommerce_move_related_products() {
            if ( function_exists( 'woocommerce_output_related_products' ) ) {
                remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
                add_action('woocommerce_after_single_product_summary', 'custom_woocommerce_move_related_products_inner', 20);
            }
        }

        if ( ! function_exists( 'custom_woocommerce_move_related_products_inner' ) ) {
            /**
             * Outputs WooCommerce related products inside a Bootstrap container.
             *
             * @return void
             */
            function custom_woocommerce_move_related_products_inner() {
                // Capture the output of WooCommerce function
                ob_start();
                woocommerce_output_related_products();
                $content = trim(ob_get_clean());

                // Only render wrapper if WooCommerce actually produced HTML
                if ( ! empty( $content ) ) {
                    echo '<div class="section section--related-products"><div class="container">';
                    echo $content;
                    echo '</div></div>';
                }
            }
        }
        add_action( 'after_setup_theme', 'custom_woocommerce_move_related_products', 15 );
    }

    if ( ! function_exists( 'custom_woocommerce_pagination_icons' ) ) {
        /**
         * Replaces WooCommerce pagination arrows with custom SVG icons.
         *
         * @param array $args Pagination arguments.
         * @return array Modified pagination arguments.
         */
        function custom_woocommerce_pagination_icons($args) {
            $args['prev_text'] = '<svg class="icon icon-chevron-left"><use xlink:href="#icon-chevron-left"></use></svg>';
            $args['next_text'] = '<svg class="icon icon-chevron-right"><use xlink:href="#icon-chevron-right"></use></svg>';
            return $args;
        }
        add_filter( 'woocommerce_pagination_args', 'custom_woocommerce_pagination_icons' );
        add_filter( 'woocommerce_comment_pagination_args', 'custom_woocommerce_pagination_icons' );
    }

    if ( ! function_exists('custom_woocommerce_single_product_sections') ) {
        /**
         * Displays flexible content sections on the single product page.
         * Checks for ACF 'sections' field for the current product,
         * then falls back to 'product_page_sections_section' options field.
         */
        function custom_woocommerce_single_product_sections() {
            try {
                $page_id = get_the_ID();

                if ( empty($page_id) || !is_numeric($page_id) ) {
                    throw new Exception( __('Az oldalazonosító hiányzik vagy érvénytelen.', TEXT_DOMAIN) );
                }

                // Define the base directory for template section files
                $template_dir = trailingslashit(get_template_directory()) . 'template-parts/sections/';
                if ( ! is_dir($template_dir) ) {
                    throw new Exception( sprintf( __('A szükséges sablonkönyvtár nem létezik: %s', TEXT_DOMAIN), $template_dir ) );
                }

                // Check for ACF
                if ( ! function_exists('get_field') ) {
                    throw new Exception( __('Az Advanced Custom Fields bővítmény nincs aktiválva. Telepítse vagy aktiválja az ACF-et a szekciók használatához.', TEXT_DOMAIN) );
                }

                // First try product-specific sections
                $sections = get_field('sections', $page_id);

                // Fallback: get option page sections
                if ( empty($sections) || !is_array($sections) ) {
                    $sections = get_field('product_single_sections', 'option');
                }

                // Process sections
                if ( ! empty($sections) && is_array($sections) ) {
                    $section_num = 0;

                    foreach ( $sections as $index => $section ) {
                        $section_num++;

                        if ( ! is_array($section) || empty($section['acf_fc_layout']) ) {
                            printf(
                                '<div class="alert alert-warning" role="alert">%s</div>',
                                esc_html( sprintf( __('A(z) #%d szekció hibásan van formázva és nem jeleníthető meg.', TEXT_DOMAIN), $section_num ) )
                            );
                            continue;
                        }

                        $section_name = sanitize_file_name($section['acf_fc_layout']);
                        $section_file = $template_dir . 'section-' . $section_name . '.php';

                        if ( file_exists($section_file) ) {
                            require $section_file;
                        } else {
                            printf(
                                '<div class="alert alert-danger" role="alert">%s</div>',
                                sprintf(
                                    __('A(z) <code>%s</code> szekció sablonja hiányzik. Kérjük, hozza létre a fájlt: <code>%s</code>', TEXT_DOMAIN),
                                    esc_html( $section_name ),
                                    esc_html( $section_file )
                                )
                            );
                        }
                    }
                }

            } catch ( Exception $e ) {
                printf(
                    '<div class="alert alert-danger" role="alert">%s</div>',
                    esc_html( $e->getMessage() )
                );
            }
        }
        add_action( 'woocommerce_after_single_product_summary', 'custom_woocommerce_single_product_sections', 30 );
    }
    
    if ( ! function_exists( 'refresh_offcanvas_minicart_fragments' ) ) {
        /**
         * Refresh minicart and cart count via AJAX fragments.
         *
         * This function ensures that both the minicart contents and the cart item count
         * are refreshed dynamically after products are added to the cart via AJAX.
         *
         * @param array $fragments An array of HTML fragments to refresh with AJAX.
         * @return array Modified fragments array including cart count and minicart wrapper.
         */
        function refresh_offcanvas_minicart_fragments( $fragments ) {

            // Cart count fragment
            ob_start();
            ?>
            <span class="cart_contents_count">
                <?php echo WC()->cart->get_cart_contents_count(); ?>
            </span>
            <?php
            $fragments['.cart_contents_count'] = ob_get_clean();

            // Minicart wrapper fragment
            ob_start();
            ?>
            <div class="woocommerce-mini-cart__wrapper">
                <?php woocommerce_mini_cart(); ?>
            </div>
            <?php
            $fragments['.woocommerce-mini-cart__wrapper'] = ob_get_clean();

            return $fragments;
        }
        add_filter( 'woocommerce_add_to_cart_fragments', 'refresh_offcanvas_minicart_fragments' );
    }

    if ( ! function_exists( 'custom_cart_item_remove_link' ) ) {
        /**
         * Override WooCommerce cart item remove link with custom attributes and SVG icon.
         *
         * @param string $link          Original remove link HTML.
         * @param string $cart_item_key The cart item key.
         * @return string               Modified remove link HTML.
         */
        function custom_cart_item_remove_link( $link, $cart_item_key ) {
            $cart_item = WC()->cart->get_cart()[$cart_item_key];
            $product   = $cart_item['data'];
            $product_id   = $product->get_id();
            $product_name = $product->get_name();
            $product_sku  = $product->get_sku();

            // Custom SVG icon
            $svg_icon = '<svg class="icon icon-trash-can"><use xlink:href="#icon-trash-can"></use></svg>';

            $new_link = sprintf(
                '<a role="button" href="%s" class="remove remove_from_cart_button custom-remove" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" data-success_message="%s">%s<span class="visually-hidden">%s</span></a>',
                esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
                esc_attr( $product_id ),
                esc_attr( $cart_item_key ),
                esc_attr( $product_sku ),
                esc_attr( sprintf( __( '&ldquo;%s&rdquo; has been removed from your cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
                $svg_icon,
                esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) )
            );

            return $new_link;
        }
        add_filter( 'woocommerce_cart_item_remove_link', 'custom_cart_item_remove_link', 10, 2 );
    }
