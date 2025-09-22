<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
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
                    'crop'   => 1,
                );
            });
            
            // Single product main image
            add_filter( 'woocommerce_get_image_size_single', function( $size ) {
                return array(
                    'width'  => 800,
                    'height' => 800,
                    'crop'   => 1,
                );
            });

            // Gallery thumbnails (below main image)
            add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function( $size ) {
                return array(
                    'width'  => 600,
                    'height' => 600,
                    'crop'   => 1,
                );
            });

        }
        add_action( 'after_setup_theme', 'custom_woocommerce_image_sizes', 10 );
    }

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

    if ( ! function_exists( 'quantity_plus_sign' ) ) {
        /**
         * Output the plus button after the quantity input field.
         */
        function quantity_plus_sign() {
            global $product;

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

    if (!function_exists('calculate_unit_price_ft_per_liter')) {
        /**
         * Calculate the unit price in Ft per liter from price and volume.
         *
         * This function can be used in WooCommerce to display unit price per liter.
         *
         * @param float|int $priceFt The price in Hungarian Forints (Ft).
         * @param float|int $volumeMl The volume in milliliters (ml).
         * @return float|string Unit price in Ft/L, rounded to 2 decimals, or error message on invalid input.
         */
        function calculate_unit_price_ft_per_liter($priceFt, $volumeMl) {
            // Validate inputs
            if (!is_numeric($priceFt) || !is_numeric($volumeMl)) {
                return __("Error: Price and volume must be numeric.", TEXT_DOMAIN);
            }

            if ($priceFt < 0) {
                return __("Error: Price cannot be negative.", TEXT_DOMAIN);
            }

            if ($volumeMl <= 0) {
                return __("Error: Volume must be greater than zero.", TEXT_DOMAIN);
            }

            // Calculate unit price
            $unitPrice = ($priceFt * 1000) / $volumeMl;

            return round($unitPrice, 0);
        }
    }

    function display_unit_price_in_summary() {
        /**
         * Display unit price under the product price in WooCommerce single product summary.
         */
        global $product;

        // Get product price
        $price = $product->get_price(); // WooCommerce price

        // Get product volume (ml) - store this as a custom field
        $volume = get_post_meta($product->get_id(), 'product_volume_ml', true);

        if ($volume) {
            $unit_price = calculate_unit_price_ft_per_liter($price, $volume);
            
            if (is_numeric($unit_price)) {
                // Format with WooCommerce currency
                $formatted_price = wc_price($unit_price);

                echo '<p class="unit-price">' . sprintf(__("Egységár: %s / liter", TEXT_DOMAIN), $formatted_price) . '</p>';
            } else {
                // Show error message
                echo '<p class="unit-price">' . esc_html($unit_price) . '</p>';
            }
        }
        add_action('woocommerce_single_product_summary', 'display_unit_price_in_summary', 15);
    }

    if ( ! function_exists( 'misha_rename_description_tab' ) ) {

        /**
         * Rename the WooCommerce product description tab.
         *
         * This function changes the default "Description" tab title to "Overview".
         * The new title is translatable.
         *
         * @param string $title The original tab title.
         * @return string The modified tab title.
         */
        function misha_rename_description_tab( $title ) {
            $title = __( 'Ismerd meg jobban a terméket!', TEXT_DOMAIN );
            return $title;
        }

        // Apply the filter to rename the description tab.
        add_filter( 'woocommerce_product_description_tab_title', 'misha_rename_description_tab' );
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

            if ( $product && is_object($product) ) {
                // Get 'pa_boraszat' terms assigned to this product
                $boraszat_terms = wp_get_post_terms( $product->get_id(), 'pa_boraszat' );

                if ( ! is_wp_error( $boraszat_terms ) && ! empty( $boraszat_terms ) ) {
                    $tabs['winery'] = array(
                        'title'    => __( 'Borászat', TEXT_DOMAIN ),
                        'priority' => 20,
                        'callback' => 'winery_tab_content'
                    );
                }
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

            // Only add the 'FAQ' tab for specific products if needed
            if ( $product && is_object($product) ) {
                $product_id = $product->get_id();
                //$faqs = get_post_meta($product_id, '_product_faqs', true); // Replace with your actual meta key

                // Only add the tab if FAQs exist
                //if ( !empty($faqs) ) {
                    $tabs['faq'] = array(
                        'title'    => __( 'Gyakran ismételt kérdések', TEXT_DOMAIN ),
                        'priority' => 25,
                        'callback' => 'faq_tab_content'
                    );
                //}
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

    if ( ! function_exists( 'my_wc_add_boraszat_gallery_in_shop_loop_header' ) ) {
        /**
         * Output the "gallery" term field in the shop loop header
         * when viewing a "pa_boraszat" taxonomy archive.
         *
         * @return void
         */
        function my_wc_add_boraszat_gallery_in_shop_loop_header() {
            // Only run on taxonomy archive pages.
            if ( ! is_tax( 'pa_boraszat' ) ) {
                return;
            }

            $term = get_queried_object();

            // Ensure it's a valid term object.
            if ( empty( $term ) || ! isset( $term->term_id, $term->taxonomy ) ) {
                return;
            }

            // Get the "gallery" field for this term (ACF).
            $gallery = get_field( 'gallery', $term->taxonomy . '_' . $term->term_id );

            if ( empty( $gallery ) || ! is_array( $gallery ) ) {
                return; // No gallery found.
            }

            echo '<div class="woocommerce-products-header__gallery">';

            foreach ( $gallery as $key => $image ) {
                $image_id = null;

                // Handle ACF gallery return format.
                if ( is_numeric( $image ) ) {
                    $image_id = $image;
                } elseif ( is_array( $image ) && ! empty( $image['ID'] ) ) {
                    $image_id = $image['ID'];
                }

                if ( $image_id ) {
                    // Get existing alt text.
                    $alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

                    // Fallback alt text if none is set.
                    if ( empty( $alt ) ) {
                        $alt = sprintf(
                            /* translators: %s: taxonomy term name */
                            __( '%s image (%s)', 'textdomain' ),
                            $term->name,
                            $key + 1
                        );
                    }

                    echo '<div class="woocommerce-products-header__gallery-item">';
                    echo wp_get_attachment_image( $image_id, 'medium_large', false, [ 'class' => esc_attr( 'woocommerce-products-header__image' ), 'alt' => esc_attr( $alt ) ] );
                    echo '</div>';
                }
            }

            echo '</div>';
        }
        add_action( 'woocommerce_archive_description', 'my_wc_add_boraszat_gallery_in_shop_loop_header', 5 );
    }

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

            if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
                return;
            }

            // Get availability array (includes text + CSS class)
            $availability = $product->get_availability();

            if ( ! empty( $availability['availability'] ) ) {
                echo '<p class="product-stock">' . esc_html( $availability['availability'] ) . '</p>';
            }
        }
        add_action( 'woocommerce_after_shop_loop_item_title', 'show_product_stock_in_loop', 20 );
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

            if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
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
        add_action( 'woocommerce_after_shop_loop_item_title', 'show_product_attributes_in_loop', 25 );
    }

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
        function borspirit_add_label_before_price( $price ) {
            try {
                // Ensure price is a non-empty string
                if ( empty( $price ) || ! is_string( $price ) ) {
                    return $price;
                }

                // Only add label on single product pages
                if ( is_product() ) {
                    $label = '<span class="price-label">' . esc_html__( 'Polci ár', TEXT_DOMAIN ) . ': </span>';
                    return $label . $price;
                }

                return $price;

            } catch ( Exception $e ) {
                // In case of unexpected errors, return original price safely
                error_log( 'Error adding label before WooCommerce price: ' . $e->getMessage() );
                return $price;
            }
        }
        add_filter( 'woocommerce_get_price_html', 'borspirit_add_label_before_price' );
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
                        'label'       => __( 'Klub ár', TEXT_DOMAIN ),
                        'desc_tip'    => true,
                        'description' => __( 'Enter a special price for club members.', TEXT_DOMAIN ),
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

                    $label = '<span class="price-label">' . esc_html__( 'Klub ár', TEXT_DOMAIN ) . ': </span>';
                    $price .= '<span class="price__club">' . $label . $club_price_html . '</span>';
                }

                // =========================
                // Sale price difference
                // =========================
                if ( $product->is_on_sale() ) {
                    $regular_price = (float) $product->get_regular_price();
                    $sale_price    = (float) $product->get_sale_price();

                    if ( $regular_price > $sale_price ) {
                        $amount_saved = $regular_price - $sale_price;
                        $percent_saved = round( ( $amount_saved / $regular_price ) * 100 );

                        $difference_html = ' <span class="price__savings">(' . sprintf( esc_html__( 'Kedvezmény %s (-%s%%)', TEXT_DOMAIN ), wc_price( $amount_saved ), $percent_saved ) . ')</span>';

                        // Append after price HTML
                        $price .= $difference_html;
                    }
                }

                return $price;

            } catch ( Exception $e ) {
                error_log( 'Error displaying club/sale price: ' . $e->getMessage() );
                return $price;
            }
        }
        add_filter( 'woocommerce_get_price_html', 'borspirit_display_club_price', 20, 2 );
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
