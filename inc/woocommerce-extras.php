<?php
    defined( 'ABSPATH' ) || exit;

    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    // ============================================================
    // 1. WOOCOMMERCE IMAGE LINK AND SIZES
    // ============================================================

    if ( ! function_exists( 'custom_woocommerce_image_sizes' ) ) {
        /**
         * Remove the <a> link around WooCommerce product thumbnails on the single product page.
         *
         * This function strips out any <a> tags from the product image HTML, effectively
         * disabling the link to the full-size product image when clicking the thumbnail.
         *
         * @param string $html    The HTML content of the product thumbnail.
         * @param int    $post_id The ID of the current product.
         * @return string         The modified HTML without <a> tags.
         */
        function custom_remove_product_image_link( $html, $post_id ) {
            return preg_replace( "!<(a|/a).*?>!", '', $html );
        }
        //add_filter( 'woocommerce_single_product_image_thumbnail_html', 'custom_remove_product_image_link', 10, 2 );
    }

    if ( ! function_exists( 'custom_woocommerce_image_sizes' ) ) {
        /**
         * Customize WooCommerce product image sizes via filters.
         *
         * This snippet adjusts the dimensions for:
         * - Gallery thumbnails (below main product image)
         * - Single product main image
         * - Shop/category thumbnails
         *
         * After making changes, remember to regenerate thumbnails so the new sizes take effect.
         */
        function custom_woocommerce_image_sizes() {

            // Shop/category thumbnails
            add_filter( 'woocommerce_get_image_size_thumbnail', function( $size ) {
                return array(
                    'width'  => 400,
                    'height' => 400,
                    'crop'   => 0,
                );
            });
            
            // Single product main image
            add_filter( 'woocommerce_get_image_size_single', function( $size ) {
                return array(
                    'width'  => 650,
                    'height' => 650,
                    'crop'   => 1,
                );
            });

            // Gallery thumbnails (below main image)
            add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function( $size ) {
                return array(
                    'width'  => 650,
                    'height' => 650,
                    'crop'   => 1,
                );
            });

        }
        add_action( 'after_setup_theme', 'custom_woocommerce_image_sizes', 10 );
    }

    // ============================================================
    // 2. ADDRESS FORMATS
    // ============================================================

    if ( ! function_exists( 'custom_hu_address_format' ) ) {
        /**
         * Modify the WooCommerce address format for Hungary (HU) 
         * to display the company name first.
         *
         * @param array $formats Associative array of country address formats.
         * @return array Modified address formats with HU customized.
         */
        function custom_hu_address_format( $formats ) {
            // Set Hungarian address format with company first
            $formats['HU'] = "{company}\n{name}\n{postcode} {city}\n{address_1} {address_2}\n{country}";
            return $formats;
        }
        add_filter( 'woocommerce_localisation_address_formats', 'custom_hu_address_format' );
    }

    // ============================================================
    // 3. QUANTITY BUTTONS
    // ============================================================

    if ( ! function_exists( 'quantity_plus_sign' ) ) {
        /**
         * Output the plus button after the quantity input field.
         */
        function quantity_plus_sign() {
            global $product;

            if ( ! $product ) {
                return;
            }

            // Skip if product is sold individually
            if ( $product && $product->is_sold_individually() ) {
                return;
            }

            echo get_quantity_plus_sign();
        }

        /**
         * Returns the HTML for the plus button.
         *
         * @return string
         */
        function get_quantity_plus_sign() {
            return '<button type="button" class="btn btn-primary btn-sm plus">
                        <svg class="icon icon-plus">
                            <use xlink:href="#icon-plus"></use>
                        </svg>
                    </button>';
        }
        add_action( 'woocommerce_after_quantity_input_field', 'quantity_plus_sign' );
    }

    if ( ! function_exists( 'quantity_minus_sign' ) ) {
        /**
         * Output the minus button before the quantity input field.
         */
        function quantity_minus_sign() {
            global $product;

            if ( ! $product ) {
                return;
            }

            // Skip if product is sold individually
            if ( $product && $product->is_sold_individually() ) {
                return;
            }

            echo get_quantity_minus_sign();
        }

        /**
         * Returns the HTML for the minus button.
         *
         * @return string
         */
        function get_quantity_minus_sign() {
            return '<button type="button" class="btn btn-primary btn-sm minus">
                        <svg class="icon icon-minus">
                            <use xlink:href="#icon-minus"></use>
                        </svg>
                    </button>';
        }
        add_action( 'woocommerce_before_quantity_input_field', 'quantity_minus_sign' );
    }

    // ============================================================
    // 4. SINGLE PRODUCT ELEMENTS
    // ============================================================

    if ( ! function_exists( 'woocommerce_template_single_rating' ) ) {
        /**
         * Display the single product rating section safely.
         *
         * Outputs the product's average rating, rating stars, and a link to the reviews section
         * on a WooCommerce single product page.
         *
         * @since 1.0.0
         * @return void
         */
        function woocommerce_template_single_rating() {
            global $product;

            if ( ! $product ) {
                return;
            }

            // Ensure product object exists and is valid
            if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
                return;
            }

            $rating_count = (int) $product->get_rating_count();
            $review_count = (int) $product->get_review_count();
            $average      = $product->get_average_rating();

            // Only display ratings if there are reviews
            if ( $review_count > 0 ) {
                echo '<div class="woocommerce-product-rating">';

                // Output rating stars if available
                $rating_html = wc_get_rating_html( $average, $rating_count );
                if ( $rating_html ) {
                    echo $rating_html;
                }

                // Display rating text and link to reviews section
                printf(
                    '<a href="#reviews" class="woocommerce-review-link" rel="nofollow">%s</a>',
                    sprintf(
                        esc_html__( 'Rated %s out of 5', 'woocommerce' ),
                        esc_html( $average )
                    )
                );

                echo '</div>';
            }
        }
    }

    if ( ! function_exists( 'add_sticky_product_block' ) ) {
        /**
         * Adds a sticky product block to the WooCommerce single product page.
         *
         * This function loads a template part located at
         * 'template-parts/blocks/block-product.php' and displays it after
         * the single product content.
         *
         * @return void
         */
        function add_sticky_product_block() {
            if ( ! is_product() ) {
                return;
            }

            get_template_part( 'template-parts/blocks/block', 'product' );
        }
        add_action( 'woocommerce_after_single_product', 'add_sticky_product_block', 5 );
    }

    // ============================================================
    // 5. UNIT PRICE AND DRS FEE
    // ============================================================

    if (!function_exists('calculate_unit_price_per_liter')) {
        /**
         * Calculate the unit price in Ft per liter from price and volume.
         *
         * This function can be used in WooCommerce to display unit price per liter.
         *
         * @param float|int $priceFt The price in Hungarian Forints (Ft).
         * @param float|int $volumeMl The volume in milliliters (ml).
         * @return float|string Unit price in Ft/L, rounded to 2 decimals, or error message on invalid input.
         */
        function calculate_unit_price_per_liter($priceFt, $volumeMl) {
            // Validate inputs
            if (!is_numeric($priceFt) || !is_numeric($volumeMl)) {
                return __("Error: Price and volume must be numeric.", 'borspirit');
            }

            if ($priceFt < 0) {
                return __("Error: Price cannot be negative.", 'borspirit');
            }

            if ($volumeMl <= 0) {
                return __("Error: Volume must be greater than zero.", 'borspirit');
            }

            // Calculate unit price
            $unitPrice = ($priceFt * 1000) / $volumeMl;

            return round($unitPrice, 0);
        }
    }

    if ( ! function_exists( 'display_unit_price_in_summary' ) ) {
        /**
         * Display unit price under the product price in WooCommerce single product summary.
         */
        function display_unit_price_in_summary() {
            global $product;

            if ( ! $product ) {
                return;
            }

            // Get product price
            $price = $product->get_price(); // WooCommerce price

            // Get product volume (ml) - store this as a custom field
            $volume = get_post_meta($product->get_id(), 'product_volume_ml', true);

            if ($volume) {
                $unit_price = calculate_unit_price_per_liter($price, $volume);
                
                if (is_numeric($unit_price)) {
                    // Format with WooCommerce currency
                    $formatted_price = wc_price($unit_price);

                    echo '<p class="unit-price">' . sprintf(__('Unit price: %s / liter', 'borspirit'), $formatted_price) . '</p>';
                } else {
                    // Show error message
                    echo '<p class="unit-price">' . esc_html($unit_price) . '</p>';
                }
            }
        }
        add_action('woocommerce_single_product_summary', 'display_unit_price_in_summary', 15);
    }

    if ( ! function_exists( 'display_drs_fee_in_summary' ) ) {
        /**
         * Display DRS fee notice under the product price in WooCommerce single product summary.
         */
        function display_drs_fee_in_summary() {
            global $product;

            if ( ! $product ) {
                return;
            }

            // Get ACF field (true/false field)
            $show_drs_fee = get_field( 'product_drs_fee', $product->get_id() );

            if ( ! $show_drs_fee ) {
                return;
            }

            // Fee amount (hardcoded here, but can be dynamic if needed)
            $drs_price = get_field( 'drs_price', 'option' );
            $drs_logo  = get_field( 'drs_logo', 'option' );
            $drs_link  = get_field( 'drs_link', 'option' );

            // Image (replace path with your actual icon)
            $image ='';
            if ( $drs_logo ) {
                $icon_url = is_array( $drs_logo ) ? $drs_logo['url'] : $drs_logo;
                $image = sprintf(
                    '<img width="60" height="60" src="%s" alt="%s" />',
                    esc_url( $icon_url ),
                    esc_attr__( 'DRS', 'borspirit' )
                );
            }

            if ( ! $drs_price ) {
                return;
            }

            // Translatable text (without HTML)
            $drs_price_text   = sprintf(
                __( 'DRS - mandatory redemption fee: %s / item.', 'borspirit' ),
                wc_price( $drs_price )
            );

            // Build link if available
            $details_link = '';
            if ( $drs_link && isset( $drs_link['url'], $drs_link['title'] ) ) {
                $details_link = sprintf(
                    '<a href="%s" target="%s">%s</a>',
                    esc_url( $drs_link['url'] ),
                    esc_attr( $drs_link['target'] ?: '_self' ),
                    esc_html( $drs_link['title'] )
                );
            }

            // Output
            echo '<div class="drs-fee">';
            echo $image;
            echo '<p>' . $drs_price_text;

            if ( $details_link ) {
                echo '<br/>' . $details_link;
            }

            echo '</p>';
            echo '</div>';
        }
        add_action( 'woocommerce_single_product_summary', 'display_drs_fee_in_summary', 16 );
    }

    // ============================================================
    // 6. PRODUCT AWARDS
    // ============================================================

    if ( ! function_exists( 'display_product_awards' ) ) {
        /**
         * Display product awards with images on the single product page.
         */
        function display_product_awards() {
            global $product;

            if ( ! $product ) {
                return;
            }

            // Get all 'award' terms for this product
            $awards = get_the_terms( $product->get_id(), 'award' );

            if ( $awards && ! is_wp_error( $awards ) ) {
                echo '<div class="product-awards">';
                echo '<strong>' . __( 'Awards', 'borspirit' ) . ': </strong>';
                echo '<ul class="product-awards__list">';

                foreach ( $awards as $award ) {
                    // Get term image ID
                    $image_id = get_term_meta( $award->term_id, '_thumbnail_id', true );
                    $image_html = '';

                    if ( $image_id ) {
                        $image_html = wp_get_attachment_image( $image_id, array( 60, 60 ), false, [ 'class' => esc_attr('product-awards__image'), 'alt' => esc_attr( $award->name ), 'loading' => 'lazy' ] );
                    }

                    echo '<li class="product-awards__listitem">';
                    if ( $image_html ) {
                        echo $image_html;
                    }
                    //echo '<span class="product-awards__text">' . esc_html( $award->name ) . '</span>';
                    echo '</li>';
                }

                echo '</ul>';
                echo '</div>';
            }
        }
        add_action( 'woocommerce_single_product_summary', 'display_product_awards', 10 );
    }

    // ============================================================
    // 7. RECENTLY VIEWED PRODUCTS
    // ============================================================

    if ( ! function_exists( 'custom_recently_viewed_products' ) ) {
        /**
         * Display recently viewed WooCommerce products on the single product page.
         *
         * Fetches the IDs of recently viewed products and outputs them in a WooCommerce product loop,
         * excluding the current product being viewed.
         *
         * Hooked to: woocommerce_after_single_product_summary
         *
         * @return void
         */
        function custom_recently_viewed_products() {
            global $post;

            $recently_viewed_ids = get_recently_viewed();

            // Remove current product ID
            $recently_viewed_ids = array_diff( $recently_viewed_ids, [ $post->ID ] );

            if ( empty( $recently_viewed_ids ) ) {
                return;
            }

            $recently_viewed_query = new WP_Query([
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => 4,
                'post__in'       => $recently_viewed_ids,
                'orderby'        => 'post__in',
            ]);

            if ( $recently_viewed_query->have_posts() ) {
                echo '<div class="section section--recently-viewed-products"><div class="container">';
                echo '<h2>' . __( 'Recently viewed products', 'borspirit' ) . '</h2>';
                
                woocommerce_product_loop_start();

                while ( $recently_viewed_query->have_posts() ) {
                    $recently_viewed_query->the_post();
                    wc_get_template_part( 'content', 'product' );
                }

                woocommerce_product_loop_end();

                echo '</div></div>';
            }

            wp_reset_postdata();
        }
        add_action( 'woocommerce_after_single_product_summary', 'custom_recently_viewed_products', 30 );
    }

    // ============================================================
    // 8. PRODUCT TABS
    // ============================================================

    if ( ! function_exists( 'rename_description_tab' ) ) {
        /**
         * Rename the WooCommerce product description tab.
         *
         * This function changes the default "Description" tab title to "Overview".
         * The new title is translatable.
         *
         * @param string $title The original tab title.
         * @return string The modified tab title.
         */
        function rename_description_tab( $title ) {
            $title = __( 'Learn more about the product!', 'borspirit' ); // Ismerd meg jobban a terméket
            return $title;
        }
        add_filter( 'woocommerce_product_description_heading', 'rename_description_tab' );
    }

    if ( ! function_exists( 'rename_additional_information_heading' ) ) {
        /**
         * Rename the heading inside the Additional Information tab.
         *
         * @param string $heading The original heading.
         * @return string Modified heading.
         */
        function rename_additional_information_heading( $heading ) {
            $heading = __( 'More product details', 'borspirit' ); // Suggested: További termékinformációk
            return $heading;
        }
        add_filter( 'woocommerce_product_additional_information_heading', 'rename_additional_information_heading' );
    }

    if ( ! function_exists( 'custom_product_icons_tab' ) ) {
        /**
         * Adds a custom 'Icons' tab to the WooCommerce product page.
         *
         * @param array $tabs Existing product tabs.
         * @return array Modified list of product tabs including the new 'icons' tab.
         */
        function custom_product_icons_tab($tabs) {
            global $product;

            if ( ! $product ) {
                return;
            }

            $tabs['icons'] = array(
                'title'    => __( 'Icons', 'borspirit' ), // Change "Icons" to your desired title
                'priority' => 5,
                'callback' => 'icons_tab_content'
            );

            return $tabs;
        }
        add_filter( 'woocommerce_product_tabs', 'custom_product_icons_tab' );

        /**
         * Callback function to render Icons tab content.
         *
         * @param string $slug The slug of the tab.
         * @param array  $tab The tab configuration.
         */
        function icons_tab_content($slug, $tab) {
            set_query_var('tab_title', $tab['title']);
            // Load external template from your theme: /woocommerce/single-product/tabs/tab-icons.php
            echo get_template_part('woocommerce/single-product/tabs/tab', 'icons');
        }
    }

    if ( ! function_exists( 'custom_product_winery_tab' ) ) {
        /**
         * Adds a custom 'Winery Info' tab to the WooCommerce product page if the product has a 'pa_boraszat' term.
         *
         * @param array $tabs Existing product tabs.
         * @return array Modified list of product tabs including the new 'winery' tab.
         */
        function custom_product_winery_tab($tabs) {
            global $product;

            if ( ! $product ) {
                return;
            }

            // Only add the 'Winery' tab for specific products if needed
            $boraszat_terms = wp_get_post_terms( $product->get_id(), 'pa_boraszat' ); // Get 'pa_boraszat' terms assigned to this product

            if ( ! is_wp_error( $boraszat_terms ) && ! empty( $boraszat_terms ) ) {
                $tabs['winery'] = array(
                    'title'    => __( 'Winery', 'borspirit' ),
                    'priority' => 20,
                    'callback' => 'winery_tab_content'
                );
            }

            return $tabs;
        }
        add_filter( 'woocommerce_product_tabs', 'custom_product_winery_tab' );

        /**
         * Callback function to render Winery tab content.
         *
         * @param string $slug The slug of the tab.
         * @param array  $tab The tab configuration.
         */
        function winery_tab_content($slug, $tab) {
            set_query_var('tab_title', $tab['title']);
            // Load external template from your theme: /woocommerce/single-product/tabs/tab-winery.php
            echo get_template_part('woocommerce/single-product/tabs/tab', 'winery');
        }
    }

    if ( ! function_exists( 'custom_product_faq_tab' ) ) {
        /**
         * Adds a custom 'FAQ' tab to the WooCommerce product page.
         *
         * @param array $tabs Existing product tabs.
         * @return array Modified list of product tabs including the new 'faq' tab.
         */
        function custom_product_faq_tab($tabs) {
            global $product;

            if ( ! $product ) {
                return;
            }

            // Only add the 'FAQ' tab for specific products if needed
            $product_id = $product->get_id();
            $faqs = get_post_meta($product_id, 'product_faqs', true) ?: []; // Replace with your actual meta key

            // Fallback: get FAQs from global "product_page_faq_items" option if product has none
            if ( empty( $faqs ) ) {
                $faqs = get_field( 'product_page_faq_items', 'option' ) ?: [];
            }

            if ( !empty($faqs) ) {
                $tabs['faq'] = array(
                    'title'    => __( 'Frequently Asked Questions', 'borspirit' ),
                    'priority' => 30,
                    'callback' => 'faq_tab_content'
                );
            }

            return $tabs;
        }
        add_filter( 'woocommerce_product_tabs', 'custom_product_faq_tab' );

        /**
         * Callback function to render FAQ tab content.
         *
         * @param string $slug The slug of the tab.
         * @param array  $tab The tab configuration.
         */
        function faq_tab_content($slug, $tab) {
            set_query_var('tab_title', $tab['title']);
            // Load external template from your theme: /woocommerce/single-product/tabs/tab-faq.php
            echo get_template_part('woocommerce/single-product/tabs/tab', 'faq');
        }
    }

    if ( ! function_exists( 'custom_product_related_posts_tab' ) ) {
        /**
         * Adds a custom 'Related posts' tab to the WooCommerce product page.
         *
         * @param array $tabs Existing product tabs.
         * @return array Modified list of product tabs including the new 'related posts' tab.
         */
        function custom_product_related_posts_tab($tabs) {
            global $product;

            if ( ! $product ) {
                return;
            }

            // Only add the 'Related posts' tab for specific products if needed
            $product_id = $product->get_id();
            $product_related_posts = get_post_meta($product_id, 'product_related_posts', true) ?: []; // Replace with your actual meta key

            if ( !empty($product_related_posts) ) {
                $tabs['related_posts'] = array(
                    'title'    => __( 'Related posts', 'borspirit' ),
                    'priority' => 40,
                    'callback' => 'related_posts_tab_content'
                );
            }

            return $tabs;
        }
        //add_filter( 'woocommerce_product_tabs', 'custom_product_related_posts_tab' );

        /**
         * Callback function to render Related posts tab content.
         *
         * @param string $slug The slug of the tab.
         * @param array  $tab The tab configuration.
         */
        function related_posts_tab_content($slug, $tab) {
            set_query_var('tab_title', $tab['title']);
            // Load external template from your theme: /woocommerce/single-product/tabs/tab-related_posts.php
            echo get_template_part('woocommerce/single-product/tabs/tab', 'related_posts');
        }
    }

    // ============================================================
    // 9. RELATED, UPSELLS
    // ============================================================

    if ( ! function_exists( 'rename_related_products_heading' ) ) {
        /**
         * Rename the WooCommerce related products section heading.
         *
         * This function changes the default "Related products" heading
         * to your custom text.
         *
         * @param string $heading The original related products heading.
         * @return string The modified heading.
         */
        function rename_related_products_heading( $heading ) {
            $heading = __( 'We also recommend…', 'borspirit' ); // Suggested translation - Ajánljuk még…
            return $heading;
        }
        add_filter( 'woocommerce_product_related_products_heading', 'rename_related_products_heading' );
    }

    if ( ! function_exists( 'rename_upsell_products_heading' ) ) {
        /**
         * Rename the WooCommerce upsell products section heading.
         *
         * This modifies the default "You may also like…" heading.
         *
         * @param string $heading The original upsell products heading.
         * @return string The modified heading.
         */
        function rename_upsell_products_heading( $heading ) {
            $heading = __( 'Customers also bought…', 'borspirit' ); // Suggested translation: Vásárlók még ezeket választották…
            return $heading;
        }
        add_filter( 'woocommerce_product_upsells_products_heading', 'rename_upsell_products_heading' );
    }

    if ( ! function_exists( 'custom_product_related_posts_after_upsells' ) ) {
        /**
         * Display "Related Posts" section on the WooCommerce single product page,
         * positioned after the Upsells section and before Related Products.
         *
         * This function retrieves custom related post IDs stored in product meta
         * and renders a template part if related posts exist.
         *
         * Expected meta field: `product_related_posts` (array of post IDs)
         *
         * @return void
         */
        function custom_product_related_posts_after_upsells() {
            global $product;

            if ( ! $product ) {
                return;
            }

            $product_id = $product->get_id();
            $product_related_posts = get_post_meta( $product_id, 'product_related_posts', true ) ?: [];

            // Only show if related posts exist
            if ( ! empty( $product_related_posts ) ) {
                echo '<div class="section section--related-posts"><div class="container">';

                set_query_var( 'tab_title', 'Related Posts' );

                // Load template if you have one
                get_template_part( 'woocommerce/single-product/tabs/tab', 'related_posts' );

                echo '</div></div>';
            }
        }
        add_action( 'woocommerce_after_single_product_summary', 'custom_product_related_posts_after_upsells', 25 );
    }

    // ============================================================
    // 10. SHOP LOOP MODIFICATIONS
    // ============================================================

    if ( ! function_exists( 'show_product_stock_in_loop' ) ) {
        /**
         * Display product stock status in the WooCommerce product loop.
         *
         * Uses WooCommerce's public get_availability() method.
         *
         * @return void
         */
        function show_product_stock_in_loop() {
            global $product;

            if ( ! $product ) {
                return;
            }

            // Get availability array (includes text + CSS class)
            $availability = $product->get_availability();

            if ( ! empty( $availability['availability'] ) ) {
                echo '<p class="product-stock">' . esc_html( $availability['availability'] ) . '</p>';
            }
        }
        //add_action( 'woocommerce_after_shop_loop_item_title', 'show_product_stock_in_loop', 20 );
    }

    if ( ! function_exists( 'show_product_attributes_in_loop' ) ) {
        /**
         * Display specific product attributes in the WooCommerce product loop.
         *
         * This function loops through a predefined list of attribute slugs (without the 'pa_' prefix)
         * and outputs their values under the product title in the shop/archive loop.
         * Only attributes marked as "Visible on product page" will be displayed.
         *
         * @return void
         */
        function show_product_attributes_in_loop() {
            global $product;

            if ( ! $product ) {
                return;
            }

            // Attributes to show (slugs without 'pa_' prefix for taxonomy attributes)
            $attributes_to_show = array( 'meret' ); // Add more slugs as needed

            $product_attributes = $product->get_attributes();

            echo '<div class="woocommerce-loop-product__attributes">';

            foreach ( $attributes_to_show as $slug ) {

                // Attempt with 'pa_' prefix first (for taxonomy attributes)
                $taxonomy_slug = 'pa_' . $slug;

                if ( isset( $product_attributes[ $taxonomy_slug ] ) ) {
                    $attribute = $product_attributes[ $taxonomy_slug ];
                } elseif ( isset( $product_attributes[ $slug ] ) ) { 
                    $attribute = $product_attributes[ $slug ]; // fallback to custom attribute
                } else {
                    continue; // attribute not found
                }

                // Only show if attribute is visible on the product page
                if ( ! $attribute->get_visible() ) {
                    continue;
                }

                $name = wc_attribute_label( $attribute->get_name() );

                // Get attribute values
                if ( $attribute->is_taxonomy() ) {
                    $values = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );
                    $values = implode( ', ', $values );
                } else {
                    $values = $attribute->get_options();
                    $values = implode( ', ', $values );
                }

                echo '<p class="product-attribute"><strong>' . esc_html( $name ) . ':</strong> ' . esc_html( $values ) . '</p>';
            }

            echo '</div>';
        }
        //add_action( 'woocommerce_after_shop_loop_item_title', 'show_product_attributes_in_loop', 25 );
    }

    // ============================================================
    // 11. PRICE MODIFICATIONS
    // ============================================================

    if ( ! function_exists( 'borspirit_add_label_before_price' ) ) {
        /**
         * Add a custom label before the WooCommerce product price.
         *
         * This function modifies the WooCommerce price HTML to prepend
         * a label before the product price on single product pages.
         *
         * @since 1.0.0
         * @param string $price The original WooCommerce price HTML.
         * @return string Modified price HTML with label.
         */
        function borspirit_add_label_before_price( $price, $product ) {
            // Exit early if in admin
            if ( is_admin() ) {
                return $price;
            }

            // Skip subscription products
            if ( $product && ( $product->is_type( 'subscription' ) || $product->is_type( 'variable-subscription' ) ) ) {
                return $price;
            }

            try {
                // Ensure price is a non-empty string
                if ( empty( $price ) || ! is_string( $price ) ) {
                    return $price;
                }

                // Only add label on single product pages
                $label = '<span class="price-label">' . esc_html__( 'Shelf price', 'borspirit' ) . ': </span>';
                return $label . '<span>' . $price . '</span>';

            } catch ( Exception $e ) {
                // In case of unexpected errors, return original price safely
                error_log( 'Error adding label before WooCommerce price: ' . $e->getMessage() );
                return $price;
            }
        }
        add_filter( 'woocommerce_get_price_html', 'borspirit_add_label_before_price', 10, 2 );
    }

    if ( ! function_exists( 'borspirit_display_club_price' ) ) {
        /**
         * Display both regular price and club price for everyone, and show sale difference if applicable.
         *
         * @since 1.1.0
         * @param string     $price   The original WooCommerce price HTML.
         * @param WC_Product $product The WooCommerce product object.
         * @return string Modified price HTML including club price and sale difference.
         */
        function borspirit_display_club_price( $price, $product ) {
            // Exit early if in admin
            if ( is_admin() ) {
                return $price;
            }
            
            try {
                // =========================
                // Wrap original price in span
                // =========================
                $price = '<span class="price__regular">' . $price . '</span>';
                
                // =========================
                // Club price
                // =========================
                $club_price = get_post_meta( $product->get_id(), '_club_price', true );
                if ( $club_price !== '' && is_numeric( $club_price ) ) {
                    $club_price_html = wc_price( $club_price );

                    $label = '<span class="price-label">' . esc_html__( 'Club price', 'borspirit' ) . ': </span>';
                    $price .= '<span class="price__club">' . $label . '<ins aria-hidden="true">' . $club_price_html . '</ins></span>';
                }

                // =========================
                // Sale price difference
                // =========================
                /*
                if ( $product->is_on_sale() ) {
                    $regular_price = (float) $product->get_regular_price();
                    $sale_price    = (float) $product->get_sale_price();

                    if ( $regular_price > $sale_price ) {
                        $amount_saved = $regular_price - $sale_price;
                        $percent_saved = round( ( $amount_saved / $regular_price ) * 100 );

                        $difference_html = ' <span class="price__savings"><span class="price-label">' . esc_html__( 'Kedvezmény', 'borspirit' ) . ': </span><span class="discount-amount">' . sprintf( esc_html__( '%s (-%s%%)', 'borspirit' ), wc_price( $amount_saved ), $percent_saved ) . '</span></span>';

                        // Append after price HTML
                        $price .= $difference_html;
                    }
                }
                */

                return $price;

            } catch ( Exception $e ) {
                error_log( 'Error displaying club/sale price: ' . $e->getMessage() );
                return $price;
            }
        }
        add_filter( 'woocommerce_get_price_html', 'borspirit_display_club_price', 20, 2 );
    }

    if ( ! function_exists( 'borspirit_add_club_price_field' ) ) {
        /**
         * Add "Club Member Price" custom field in product pricing options.
         *
         * @since 1.0.0
         * @return void
         */
        function borspirit_add_club_price_field() {
            try {
                woocommerce_wp_text_input(
                    array(
                        'id'          => '_club_price',
                        'label'       => __( 'Club price', 'borspirit' ),
                        'desc_tip'    => true,
                        'description' => __( 'Enter a special price for club members.', 'borspirit' ),
                        'type'        => 'text',
                        'data_type'   => 'price',
                    )
                );
            } catch ( Exception $e ) {
                error_log( 'Error adding club price field: ' . $e->getMessage() );
            }
        }
        add_action( 'woocommerce_product_options_pricing', 'borspirit_add_club_price_field' );
    }

    if ( ! function_exists( 'borspirit_save_club_price_field' ) ) {
        /**
         * Save the "Club Member Price" custom field.
         *
         * @since 1.0.0
         * @param int $post_id The product ID.
         * @return void
         */
        function borspirit_save_club_price_field( $post_id ) {
            try {
                $club_price = isset( $_POST['_club_price'] ) ? wc_clean( wp_unslash( $_POST['_club_price'] ) ) : '';
                if ( $club_price !== '' ) {
                    update_post_meta( $post_id, '_club_price', $club_price );
                } else {
                    delete_post_meta( $post_id, '_club_price' );
                }
            } catch ( Exception $e ) {
                error_log( 'Error saving club price field: ' . $e->getMessage() );
            }
        }
        add_action( 'woocommerce_process_product_meta', 'borspirit_save_club_price_field' );
    }

    if ( ! function_exists( 'borspirit_apply_club_price_in_cart' ) ) {
        /**
         * Apply club price to cart and checkout for members.
         *
         * @since 1.0.0
         * @param WC_Cart $cart The WooCommerce cart object.
         * @return void
         */
        function borspirit_apply_club_price_in_cart( $cart ) {
            try {
                if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
                    return;
                }

                if ( is_user_logged_in() /*&& current_user_can( 'club_member' )*/ ) {
                    foreach ( $cart->get_cart() as $cart_item ) {
                        $club_price = get_post_meta( $cart_item['product_id'], '_club_price', true );
                        if ( $club_price !== '' ) {
                            $cart_item['data']->set_price( $club_price );
                        }
                    }
                }
            } catch ( Exception $e ) {
                error_log( 'Error applying club price in cart: ' . $e->getMessage() );
            }
        }
        add_action( 'woocommerce_before_calculate_totals', 'borspirit_apply_club_price_in_cart' );
    }

    // Add Subtitle input under product title
    if ( ! function_exists( 'add_product_subtitle_input' ) ) {
        /**
         * Render the product subtitle input field in the product editor.
         *
         * @param WP_Post $post Current post object.
         * @return void
         */
        function add_product_subtitle_input( $post ) {
            if ( empty( $post ) || 'product' !== $post->post_type ) {
                return;
            }

            $subtitle = get_post_meta( $post->ID, '_product_subtitle', true );

            // Display input box
            printf(
                '<input type="text" id="product-subtitle" name="product-subtitle" value="%s" placeholder="%s" style="width:100%%; height:1.7em; margin:10px 0 20px 0; padding:3px 8px; font-size:1.7em; line-height:100%%;" />',
                esc_attr( $subtitle ),
                esc_attr__( 'Product subtitle', 'borspirit' )
            );
        }
        //add_action( 'edit_form_after_title', 'add_product_subtitle_input' );
    }

    // Save subtitle
    if ( ! function_exists( 'save_product_subtitle_input' ) ) {
        /**
         * Save the product subtitle input field to post meta.
         *
         * @param int $post_id The ID of the product being saved.
         * @return void
         */
        function save_product_subtitle_input( $post_id ) {
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }
            if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
                return;
            }

            $post_type = get_post_type( $post_id );
            if ( 'product' !== $post_type ) {
                return;
            }

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }

            // Update subtitle if field is set
            if ( isset( $_POST['product-subtitle'] ) ) {
                $subtitle = sanitize_text_field( wp_unslash( $_POST['product-subtitle'] ) );
                update_post_meta( $post_id, '_product_subtitle', $subtitle );
            }
        }
        //add_action( 'save_post_product', 'save_product_subtitle_input' );
    }

    if ( ! function_exists( 'append_product_subtitle_to_title' ) ) {
        /**
         * Prepend a product subtitle (stored in post meta) to the product title.
         *
         * Hooks into 'the_title' to modify product titles everywhere they are displayed
         * on the frontend (single product, shop loop, widgets, etc.).
         *
         * @param string $title   The original product title.
         * @param int    $post_id The ID of the current post.
         * @return string Modified product title with subtitle prepended (if exists).
         */
        function append_product_subtitle_to_title( $title, $post_id ) {
            // Only affect WooCommerce products
            if ( get_post_type( $post_id ) !== 'product' ) {
                return $title;
            }

            // Get subtitle from post meta
            $subtitle = get_post_meta( $post_id, '_product_subtitle', true );

            // Prepend subtitle if it exists and not in admin
            if ( ! empty( $subtitle ) && ! is_admin() ) {
                $title = '<span class="product-subtitle">' . esc_html( $subtitle ) . '</span> ' . $title;
            }

            return $title;
        }
        //add_filter( 'the_title', 'append_product_subtitle_to_title', 10, 2 );
    }

    if ( ! function_exists( 'add_product_subtitle_column' ) ) {
        /**
         * Add custom subtitle column to WooCommerce products admin table.
         *
         * @param array $columns The existing columns.
         * @return array Modified columns with subtitle added.
         */
        function add_product_subtitle_column( $columns ) {
            $new_columns = [];
            foreach ( $columns as $key => $value ) {
                $new_columns[ $key ] = $value;
                if ( 'name' === $key ) {
                    $new_columns['product_subtitle'] = __( 'Subtitle', 'borspirit' );
                }
            }
            return $new_columns;
        }
        //add_filter( 'manage_edit-product_columns', 'add_product_subtitle_column' );
    }

    if ( ! function_exists( 'render_product_subtitle_column' ) ) {
        /**
         * Render subtitle column content for WooCommerce products.
         *
         * @param string $column  Column name.
         * @param int    $post_id Post ID.
         */
        function render_product_subtitle_column( $column, $post_id ) {
            if ( 'product_subtitle' === $column ) {
                $subtitle = get_post_meta( $post_id, '_product_subtitle', true );
                echo $subtitle ? esc_html( $subtitle ) : '<span class="na">–</span>';
            }
        }
        //add_action( 'manage_product_posts_custom_column', 'render_product_subtitle_column', 10, 2 );
    }

    if ( ! function_exists( 'quick_edit_subtitle_field' ) ) {
        /**
         * Add Subtitle field to Quick Edit box in WooCommerce products.
         *
         * @param string $column    Current column name.
         * @param string $post_type Current post type.
         */
        function quick_edit_subtitle_field( $column, $post_type ) {
            if ( 'product' === $post_type && 'product_subtitle' === $column ) {
                ?>
                <fieldset class="inline-edit-col-right">
                    <div class="inline-edit-col">
                        <label>
                            <span class="title"><?php echo esc_html__( 'Subtitle', 'borspirit' ); ?></span>
                            <span class="input-text-wrap">
                                <input type="text" name="product_subtitle" class="ptitle" value="">
                            </span>
                        </label>
                    </div>
                </fieldset>
                <?php
            }
        }
        //add_action( 'quick_edit_custom_box', 'quick_edit_subtitle_field', 10, 2 );
    }

    if ( ! function_exists( 'save_quick_edit_subtitle' ) ) {
        /**
         * Save subtitle field data from Quick Edit for WooCommerce products.
         *
         * @param int $post_id Post ID.
         */
        function save_quick_edit_subtitle( $post_id ) {
            if ( isset( $_POST['product_subtitle'] ) ) {
                update_post_meta(
                    $post_id,
                    '_product_subtitle',
                    sanitize_text_field( wp_unslash( $_POST['product_subtitle'] ) )
                );
            }
        }
        //add_action( 'save_post_product', 'save_quick_edit_subtitle' );
    }

    if ( ! function_exists( 'quick_edit_subtitle_js' ) ) {
        /**
         * Pass subtitle values to Quick Edit JavaScript in WooCommerce product list.
         */
        function quick_edit_subtitle_js() {
            global $current_screen;
            if ( $current_screen->post_type !== 'product' ) {
                return;
            }
            ?>
            <script>
            jQuery(function($){
                // Extend quick edit
                var wp_inline_edit_function = inlineEditPost.edit;
                inlineEditPost.edit = function( id ) {
                    wp_inline_edit_function.apply( this, arguments );

                    var postId = 0;
                    if ( typeof(id) === 'object' ) {
                        postId = parseInt( this.getId( id ) );
                    }

                    if ( postId > 0 ) {
                        var $subtitleField = $('tr#post-' + postId).find('td.product_subtitle').text();
                        $(':input[name="product_subtitle"]', '.inline-edit-row').val(
                            $subtitleField !== '–' ? $subtitleField : ''
                        );
                    }
                }
            });
            </script>
            <?php
        }
        //add_action( 'admin_footer-edit.php', 'quick_edit_subtitle_js' );
    }

    // ============================================================
    // 12. SHIPPING
    // ============================================================

    if ( ! function_exists( 'show_free_shipping_notice' ) ) {
        /**
         * Display a notice showing how much more a customer needs to spend 
         * to qualify for free shipping.
         *
         * This function:
         * - Gets the customer's shipping country from their WooCommerce profile or geolocation.
         * - Finds the matching shipping zone and checks if "Free Shipping" is enabled.
         * - Determines the minimum cart amount required for free shipping.
         * - Displays a notice if the customer has not yet reached the free shipping threshold.
         *
         * Hooks into various WooCommerce locations:
         * - Before shop loop
         * - Before cart
         * - Before checkout form
         * - Custom bbloomer hooks for cart and checkout
         *
         * @return void
         */
        function show_free_shipping_notice() {
            if ( ! WC()->cart ) {
                return;
            }

            // Determine customer country
            $customer_country = WC()->customer->get_shipping_country();

            if ( empty( $customer_country ) ) {
                $geo = WC_Geolocation::geolocate_ip();
                $customer_country = $geo['country'] ?? '';
            }

            if ( empty( $customer_country ) ) {
                return;
            }

            // Get the zone
            $package = [
                'destination' => [
                    'country'  => $customer_country,
                    'state'    => '',
                    'postcode' => '',
                    'city'     => '',
                    'address'  => '',
                ],
            ];

            $zone    = WC_Shipping_Zones::get_zone_matching_package( $package );
            $methods = $zone->get_shipping_methods();

            $minimum_amount = 0;
            foreach ( $methods as $method ) {
                if ( $method->id === 'free_shipping' && $method->enabled === 'yes' ) {
                    $minimum_amount = $method->min_amount ?? 0;
                    break;
                }
            }

            if ( $minimum_amount <= 0 ) {
                return;
            }

            $current_amount = WC()->cart->subtotal;
            if ( $current_amount >= $minimum_amount ) {
                return;
            }

            $remaining_amount = $minimum_amount - $current_amount;

            $message = sprintf(
                __( 'Add %s more to your cart to qualify for free shipping!', 'woocommerce' ),
                wc_price( $remaining_amount )
            );

            wc_print_notice( $message, 'notice' );
        }
        add_action( 'woocommerce_archive_description', 'show_free_shipping_notice', 20 );
        add_action( 'woocommerce_before_cart', 'show_free_shipping_notice' );
        add_action( 'woocommerce_before_checkout_form', 'show_free_shipping_notice' );
        add_action( 'bbloomer_before_woocommerce/cart', 'show_free_shipping_notice' );
        add_action( 'bbloomer_before_woocommerce/checkout', 'show_free_shipping_notice' );
    }
