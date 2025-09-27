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

    if ( ! function_exists( 'display_unit_price_in_summary' ) ) {
        /**
         * Display unit price under the product price in WooCommerce single product summary.
         */
        function display_unit_price_in_summary() {
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
        }
        add_action('woocommerce_single_product_summary', 'display_unit_price_in_summary', 15);
    }

    if ( ! function_exists( 'display_drs_fee_in_summary' ) ) {
        /**
         * Display DRS fee notice under the product price in WooCommerce single product summary.
         */
        function display_drs_fee_in_summary() {
            global $product;

            if ( ! $product instanceof WC_Product ) {
                return;
            }

            // Get ACF field (true/false field)
            $show_drs_fee = get_field( 'product_drs_fee', $product->get_id() );

            if ( ! $show_drs_fee ) {
                return;
            }

            // Fee amount (hardcoded here, but can be dynamic if needed)
            $fee = 50;

            // Image (replace path with your actual icon)
            $image = sprintf(
                '<img width="60" height="60" src="%s" alt="%s" />',
                esc_url( get_stylesheet_directory_uri() . '/assets/src/images/drs-icon.png' ),
                esc_attr__( 'DRS', TEXT_DOMAIN )
            );

            // Translatable text (without HTML)
            $fee_text   = sprintf(
                __( 'DRS - kötelező visszaváltási díj: %s Ft/db.', TEXT_DOMAIN ),
                $fee
            );

            // Details link (replace URL with your actual page)
            $details_link = sprintf(
                '<a href="%s">%s</a>',
                esc_url( get_home_url() ),
                esc_html__( 'Részletek', TEXT_DOMAIN )
            );

            // Output
            echo '<div class="drs-fee">';
            echo $image;
            echo '<p>' . $fee_text . '<br/>' . $details_link . '</p>';
            echo '</div>';
        }
        add_action( 'woocommerce_single_product_summary', 'display_drs_fee_in_summary', 16 );
    }

    if ( ! function_exists( 'display_product_awards' ) ) {
        /**
         * Display product awards with images on the single product page.
         */
        function display_product_awards() {
            global $product;

            // Get all 'award' terms for this product
            $awards = get_the_terms( $product->get_id(), 'award' );

            if ( $awards && ! is_wp_error( $awards ) ) {
                echo '<div class="product-awards">';
                echo '<strong>' . __( 'Awards', TEXT_DOMAIN ) . ': </strong>';
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
        add_action( 'woocommerce_single_product_summary', 'display_product_awards', 9 );
    }

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
            $title = __( 'Ismerd meg jobban a terméket!', TEXT_DOMAIN );
            return $title;
        }

        // Apply the filter to rename the description tab.
        add_filter( 'woocommerce_product_description_heading', 'rename_description_tab' );
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

            if ( $product && is_object($product) ) {
                $product_id = $product->get_id();
                //$icons = get_post_meta($product_id, '_product_icons', true); // Optional meta key if you only want icons for specific products

                // Add the tab (you can wrap it with a condition if needed, like only if icons exist)
                $tabs['icons'] = array(
                    'title'    => __( 'Ikonok', TEXT_DOMAIN ), // Change "Ikonok" to your desired title
                    'priority' => 5,
                    'callback' => 'icons_tab_content'
                );
            }

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
                        'priority' => 30,
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

            echo '<div class="slider woocommerce-products-header__gallery">';

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
        //add_action( 'woocommerce_after_shop_loop_item_title', 'show_product_attributes_in_loop', 25 );
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
                $label = '<span class="price-label">' . esc_html__( 'Polci ár', TEXT_DOMAIN ) . ': </span>';
                return $label . '<span>' . $price . '</span>';

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
                /*
                if ( $product->is_on_sale() ) {
                    $regular_price = (float) $product->get_regular_price();
                    $sale_price    = (float) $product->get_sale_price();

                    if ( $regular_price > $sale_price ) {
                        $amount_saved = $regular_price - $sale_price;
                        $percent_saved = round( ( $amount_saved / $regular_price ) * 100 );

                        $difference_html = ' <span class="price__savings"><span class="price-label">' . esc_html__( 'Kedvezmény', TEXT_DOMAIN ) . ': </span><span class="discount-amount">' . sprintf( esc_html__( '%s (-%s%%)', TEXT_DOMAIN ), wc_price( $amount_saved ), $percent_saved ) . '</span></span>';

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
                esc_attr__( 'Product subtitle', TEXT_DOMAIN )
            );
        }
    }
    add_action( 'edit_form_after_title', 'add_product_subtitle_input' );

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
                    $new_columns['product_subtitle'] = __( 'Subtitle', TEXT_DOMAIN );
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
                            <span class="title"><?php echo esc_html__( 'Subtitle', TEXT_DOMAIN ); ?></span>
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
                echo '<h2>' . __( 'Recently viewed products', TEXT_DOMAIN ) . '</h2>';
                
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
        add_action( 'woocommerce_after_single_product_summary', 'custom_recently_viewed_products', 25 );
    }