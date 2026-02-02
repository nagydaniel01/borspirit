<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    // ============================================================
    // 1. WOOCOMMERCE - BORSPIRIT SETTINGS PAGE
    // ============================================================
    
    if ( ! function_exists( 'borspirit_add_settings_tab' ) ) {
        /**
         * Add Custom Settings tab to WooCommerce settings tabs.
         */
        function borspirit_add_settings_tab( $tabs ) {
            $tabs['borspirit_settings'] = __( 'Custom Settings', 'borspirit' );
            return $tabs;
        }
        add_filter( 'woocommerce_settings_tabs_array', 'borspirit_add_settings_tab', 50 );
    }

    if ( ! function_exists( 'borspirit_settings_tab_content' ) ) {
        /**
         * Render Custom Settings tab content with section-based subtabs.
         */
        function borspirit_settings_tab_content() {

            // Use 'section' instead of 'subtab'
            $current_section = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : 'general';

            // Subtab navigation using WooCommerce style
            $general_class = $current_section === 'general' ? 'current' : '';
            $display_class = $current_section === 'display' ? 'current' : '';

            echo '<ul class="subsubsub">';
            echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=borspirit_settings&section=general' ) . '" class="' . $general_class . '">' . __( 'General Settings', 'borspirit' ) . '</a> | </li>';
            echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=borspirit_settings&section=display' ) . '" class="' . $display_class . '">' . __( 'Display Setting', 'borspirit' ) . '</a></li>';
            echo '</ul>';

            echo '<br class="clear">';

            // Render fields based on section
            if ( $current_section === 'display' ) {
                woocommerce_admin_fields( borspirit_settings_display() );
            } else {
                woocommerce_admin_fields( borspirit_settings_general() );
            }
        }
        add_action( 'woocommerce_settings_tabs_borspirit_settings', 'borspirit_settings_tab_content' );
    }

    /**
     * Save Custom Settings tab options per section.
     */
    if ( ! function_exists( 'borspirit_update_settings' ) ) {
        function borspirit_update_settings() {

            $current_section = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : 'general';

            if ( $current_section === 'display' ) {
                woocommerce_update_options( borspirit_settings_display() );
            } else {
                woocommerce_update_options( borspirit_settings_general() );
            }
        }
        add_action( 'woocommerce_update_options_borspirit_settings', 'borspirit_update_settings' );
    }

    if ( ! function_exists( 'borspirit_settings_general' ) ) {
        /**
         * General Settings section fields.
         *
         * @return array Settings fields for General tab.
         */
        function borspirit_settings_general() {
            return [
                'section_title' => [
                    'name' => __( 'General Settings', 'borspirit' ),
                    'type' => 'title',
                    'id'   => 'borspirit_general_section_title',
                ],

                'club_discount_amount' => [
                    'name'              => __( 'Club Discount Amount', 'borspirit' ),
                    'type'              => 'number',
                    'id'                => 'borspirit_club_discount_amount',
                    'desc'              => __( 'Enter the club discount amount.', 'borspirit' ),
                    'placeholder'       => '5',
                    'custom_attributes' => [
                        'min'  => '0',
                        'max'  => '100',
                        'step' => '1',
                    ],
                ],

                'section_end' => [
                    'type' => 'sectionend',
                    'id'   => 'borspirit_general_section_end',
                ],
            ];
        }
    }

    if ( ! function_exists( 'borspirit_settings_display' ) ) {
        /**
         * Display Setting section fields.
         *
         * @return array Settings fields for Display Setting tab.
         */
        function borspirit_settings_display() {
            return [
                'section_title' => [
                    'name' => __( 'Display Setting', 'borspirit' ),
                    'type' => 'title',
                    'id'   => 'borspirit_display_section_title',
                ],

                'regular_price_label' => [
                    'name'        => __( 'Regular Price Label', 'borspirit' ),
                    'type'        => 'text',
                    'id'          => 'borspirit_regular_price_label',
                    'desc'        => __( 'Enter the label for the regular price.', 'borspirit' ),
                    'placeholder' => __( 'Regular Price', 'borspirit' ),
                ],

                'club_price_label' => [
                    'name'        => __( 'Club Price Label', 'borspirit' ),
                    'type'        => 'text',
                    'id'          => 'borspirit_club_price_label',
                    'desc'        => __( 'Enter the label for the club price.', 'borspirit' ),
                    'placeholder' => __( 'Club Price', 'borspirit' ),
                ],

                'savings_label' => [
                    'name'        => __( 'Savings Label', 'borspirit' ),
                    'type'        => 'text',
                    'id'          => 'borspirit_savings_label',
                    'desc'        => __( 'Enter the label for the savings displayed on sale products.', 'borspirit' ),
                    'placeholder' => __( 'You Save', 'borspirit' ),
                ],

                'section_end' => [
                    'type' => 'sectionend',
                    'id'   => 'borspirit_display_section_end',
                ],
            ];
        }
    }

    // ============================================================
    // 2. WOOCOMMERCE IMAGE LINK AND SIZES
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
        add_filter( 'woocommerce_single_product_image_thumbnail_html', 'custom_remove_product_image_link', 10, 2 );
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
                    'crop'   => 0,
                );
            });

            // Gallery thumbnails (below main image)
            add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function( $size ) {
                return array(
                    'width'  => 650,
                    'height' => 650,
                    'crop'   => 0,
                );
            });

        }
        add_action( 'after_setup_theme', 'custom_woocommerce_image_sizes', 10 );
    }

    if ( ! function_exists( 'custom_wrap_main_product_image_html' ) ) {
        /**
         * Wrap the main WooCommerce product image with a custom HTML div.
         *
         * This function targets only the main product image on single product pages
         * and wraps it in a div with the class "product_image_thumbnail".
         *
         * @param string $html          The HTML of the product image thumbnail.
         * @param int    $attachment_id The ID of the image attachment.
         * @return string Modified HTML with custom wrapper for the main image.
         */
        function custom_wrap_main_product_image_html( $html, $attachment_id ) {
            global $product;

            if ( ! is_a( $product, 'WC_Product' ) ) {
                return $html;
            }

            // Only target the main product image
            $main_image_id = $product->get_image_id();

            if ( $attachment_id == $main_image_id ) {
                // List of possible random classes
                $classes = array( 
                    'product_image_thumbnail--style-01', 
                    'product_image_thumbnail--style-02', 
                    'product_image_thumbnail--style-03', 
                    'product_image_thumbnail--style-04',
                    'product_image_thumbnail--style-05',
                    'product_image_thumbnail--style-06',
                    'product_image_thumbnail--style-07',
                    'product_image_thumbnail--style-08',
                    'product_image_thumbnail--style-09',
                    'product_image_thumbnail--style-10'
                );

                // Pick one at random
                $random_class = $classes[ array_rand( $classes ) ];

                // Wrap image in a div with both classes
                $html = '<div class="product_image_thumbnail ' . esc_attr( $random_class ) . '">' . $html . '</div>';
            }

            return $html;
        }
        add_filter( 'woocommerce_single_product_image_thumbnail_html', 'custom_wrap_main_product_image_html', 10, 2 );
    }

    // ============================================================
    // 3. ADDRESS FORMATS
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
    // 4. QUANTITY BUTTONS
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
    // 5. SINGLE PRODUCT ELEMENTS
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
    // 6. UNIT PRICE AND DRS FEE
    // ============================================================

    if ( ! function_exists( 'calculate_unit_price_per_liter' ) ) {
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
        add_action( 'woocommerce_single_product_summary', 'display_unit_price_in_summary', 10 );
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
                __( 'DRS - mandatory redemption fee: %s/item.', 'borspirit' ),
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
        add_action( 'woocommerce_single_product_summary', 'display_drs_fee_in_summary', 25 );
    }

    if ( ! function_exists( 'add_drs_fee_per_item_to_cart' ) ) {
        /**
         * Add DRS fee per item to WooCommerce cart total.
         *
         * This checks if the product has the ACF field 'product_drs_fee' enabled.
         * If yes, it adds the 'drs_price' (from options) multiplied by quantity.
         */
        function add_drs_fee_per_item_to_cart( WC_Cart $cart ) {
            // Avoid running in admin or before cart is ready
            if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
                return;
            }

            // Ensure cart isn't empty
            if ( count( $cart->get_cart() ) === 0 ) {
                return;
            }

            $total_drs_fee = 0;
            $drs_price     = get_field( 'drs_price', 'option' );

            if ( ! $drs_price || ! is_numeric( $drs_price ) ) {
                return; // Skip if no fee is defined in options
            }

            // Loop through cart items
            foreach ( $cart->get_cart() as $cart_item ) {
                $product_id = $cart_item['product_id'];
                $quantity   = $cart_item['quantity'];

                $has_drs_fee = get_field( 'product_drs_fee', $product_id );

                if ( $has_drs_fee ) {
                    $total_drs_fee += floatval( $drs_price ) * intval( $quantity );
                }
            }

            // Add the fee if applicable
            if ( $total_drs_fee > 0 ) {
                $cart->add_fee( __( 'DRS - mandatory redemption fee', 'borspirit' ), $total_drs_fee, false );
            }
        }
        add_action( 'woocommerce_cart_calculate_fees', 'add_drs_fee_per_item_to_cart' );
    }

    if ( ! function_exists( 'display_drs_fee_in_mini_cart' ) ) {
        /**
         * Display DRS Fee in the WooCommerce mini cart with a proper ACF link.
         */
        function display_drs_fee_in_mini_cart() {
            // Ensure fees are calculated
            WC()->cart->calculate_totals();

            // Get the DRS link from ACF options
            $drs_link = get_field( 'drs_link', 'option' );

            foreach ( WC()->cart->get_fees() as $fee ) {
                if ( $fee->name === __( 'DRS - mandatory redemption fee', 'borspirit' ) ) {
                    echo '<div class="woocommerce-mini-cart__drs_fee-wrapper">';
                    echo '<p class="woocommerce-mini-cart__drs_fee"><strong>' . esc_html( $fee->name ) . ':</strong> ' . wc_price( $fee->amount ) . '</p>';
                    
                    echo '<p><small>';
                    echo esc_html__('Some of the products in your basket are subject to a redemption fee.', 'borspirit');
                    if ( $drs_link && isset( $drs_link['url'], $drs_link['title'] ) ) {
                        echo sprintf(
                            ' <a href="' . esc_url( $drs_link['url'] ) . '" target="' . esc_attr( $drs_link['target'] ?: '_self' ) . '">' . esc_html__('Learn more', 'borspirit') . '</a>'
                        );
                    }
                    echo '</small></p>';

                    echo '</div>';
                }
            }
        }
        add_action( 'woocommerce_widget_shopping_cart_before_buttons', 'display_drs_fee_in_mini_cart', 5 );
    }

    // ============================================================
    // 7. PRODUCT AWARDS
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
        add_action( 'woocommerce_single_product_summary', 'display_product_awards', 9 );
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

                set_query_var( 'tab_title', __('Related posts', 'borspirit') );

                // Load template if you have one
                get_template_part( 'woocommerce/single-product/tabs/tab', 'related_posts' );

                echo '</div></div>';
            }
        }
        add_action( 'woocommerce_after_single_product_summary', 'custom_product_related_posts_after_upsells', 25 );
    }

    // ============================================================
    // 10. RECENTLY VIEWED PRODUCTS
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
    // 11. PRICE MODIFICATIONS
    // ============================================================

    if ( ! function_exists( 'borspirit_is_club_member' ) ) {
        /**
         * Check if user is club member (spent 50 000+ HUF)
         *
         * @param int $user_id Optional user ID.
         * @return bool True if user spent >= 100000 HUF.
         */
        function borspirit_is_club_member( $user_id = 0 ) {
            try {
                if ( ! $user_id ) {
                    $user_id = get_current_user_id();
                }
                if ( ! $user_id || ! is_numeric( $user_id ) ) {
                    return false;
                }

                $total_spent = wc_get_customer_total_spent( $user_id );
                if ( ! is_numeric( $total_spent ) ) {
                    return false;
                }

                return $total_spent >= 100000;

            } catch ( Exception $e ) {
                error_log( 'Club Member Error: ' . $e->getMessage() );
                return false;
            }
        }
    }

    if ( ! function_exists( 'borspirit_add_label_before_price' ) ) {
        /**
         * Add custom label before regular price on product pages.
         *
         * @param string     $price   Original price HTML.
         * @param WC_Product $product WooCommerce product object.
         * @return string Modified price HTML.
         */
        function borspirit_add_label_before_price( $price, $product ) {
            try {
                if ( is_admin() ) {
                    return $price;
                }

                if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
                    return $price;
                }

                if ( $product->is_type( 'subscription' ) || $product->is_type( 'variable-subscription' ) ) {
                    return $price;
                }

                if ( empty( $price ) || ! is_string( $price ) ) {
                    return $price;
                }

                $regular_label = get_option( 'borspirit_regular_price_label', __( 'Shelf price', 'borspirit' ) );
                $label = '<span class="price-label">' . esc_html( $regular_label ) . ': </span>';
                return $label . '<span>' . $price . '</span>';

            } catch ( Exception $e ) {
                error_log( 'Label Price Error: ' . $e->getMessage() );
                return $price;
            }
        }
        add_filter( 'woocommerce_get_price_html', 'borspirit_add_label_before_price', 10, 2 );
    }

    if ( ! function_exists( 'borspirit_add_club_price_field' ) ) {
        /**
         * Adds backend custom field for manual club price.
         */
        function borspirit_add_club_price_field() {
            try {
                // Get discount from options (default 5)
                $discount = floatval( get_option( 'borspirit_club_discount_amount', 0 ) );

                // Ensure discount is not empty or invalid
                if ( $discount <= 0 ) {
                    $discount = 5;
                }

                $description = sprintf(
                    __( 'Optional: special manual club price. Leave empty to use %d%% club discount.', 'borspirit' ),
                    $discount
                );

                // Get WooCommerce currency symbol
                $currency_symbol = get_woocommerce_currency_symbol();

                woocommerce_wp_text_input([
                    'id'          => '_club_price',
                    'label'       => sprintf(
                        __( 'Club price (%s)', 'borspirit' ),
                        $currency_symbol
                    ),
                    'desc_tip'    => true,
                    'description' => $description,
                    'type'        => 'text',
                    'data_type'   => 'price'
                ]);

            } catch ( Exception $e ) {
                error_log( 'Club Price Field Error: ' . $e->getMessage() );
            }
        }
        add_action( 'woocommerce_product_options_pricing', 'borspirit_add_club_price_field' );
    }

    if ( ! function_exists( 'borspirit_save_club_price_field' ) ) {
        /**
         * Saves the manual club price field when product is saved.
         *
         * @param int $post_id Product ID.
         */
        function borspirit_save_club_price_field( $post_id ) {
            try {
                if ( ! isset( $_POST['_club_price'] ) ) {
                    delete_post_meta( $post_id, '_club_price' );
                    return;
                }

                $club_price = wc_clean( wp_unslash( $_POST['_club_price'] ) );

                if ( $club_price === '' || ! is_numeric( $club_price ) ) {
                    delete_post_meta( $post_id, '_club_price' );
                } else {
                    update_post_meta( $post_id, '_club_price', $club_price );
                }

            } catch ( Exception $e ) {
                error_log( 'Club Price Save Error: ' . $e->getMessage() );
            }
        }
        add_action( 'woocommerce_process_product_meta', 'borspirit_save_club_price_field' );
    }

    if ( ! function_exists( 'borspirit_display_club_price' ) ) {
        /**
         * Display club price for all visitors (manual value or automatic discount).
         * Excludes sale products.
         *
         * @param string     $price   Original price HTML.
         * @param WC_Product $product WooCommerce product object.
         * @return string Modified price output with club price.
         */
        function borspirit_display_club_price( $price, $product ) {
            try {
                if ( is_admin() ) {
                    return $price;
                }

                if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
                    return $price;
                }

                // Exclude subscription products
                if ( $product->is_type( 'subscription' ) || $product->is_type( 'variable-subscription' ) ) {
                    return $price;
                }

                $price_html = '<span class="price__regular">' . $price . '</span>';

                /** --------------------------------------
                 * SALE PRICE HANDLING
                 * -------------------------------------- */
                if ( $product->is_on_sale() ) {
                    $regular = floatval( $product->get_regular_price() );
                    $sale    = floatval( $product->get_sale_price() );

                    if ( $regular > 0 && $sale > 0 ) {
                        $amount_saved = $regular - $sale;

                        $save_html = '';
                        if ( is_product() ) {
                            $savings_label = get_option( 'borspirit_savings_label', __( 'Savings', 'borspirit' ) );
                            $save_html  = '<span class="price__discount">';
                            $save_html .= '<span class="price-label">' . esc_html( $savings_label ) . ':</span> ';
                            $save_html .= wc_price( $amount_saved );
                            $save_html .= '</span>';
                        }

                        return '<span class="price__regular">' . $price . '</span>' . $save_html;
                    }

                    return $price;
                }

                /** --------------------------------------
                 * CLUB PRICE HANDLING
                 * -------------------------------------- */

                // Manual price
                $manual_club_price = get_post_meta( $product->get_id(), '_club_price', true );
                $manual_club_price = is_numeric( $manual_club_price ) ? floatval( $manual_club_price ) : 0;

                // Discount percent
                $discount_percent = floatval( get_option( 'borspirit_club_discount_amount', 0 ) );
                $discount_percent = $discount_percent > 0 ? $discount_percent : 0;

                // If neither manual price nor discount is valid -> DO NOT DISPLAY CLUB PRICE
                if ( $manual_club_price <= 0 && $discount_percent <= 0 ) {
                    return $price_html;
                }

                // Determine club price
                if ( $manual_club_price > 0 ) {
                    $club_price = $manual_club_price;
                } else {
                    $regular_price = floatval( $product->get_regular_price() );
                    if ( $regular_price <= 0 ) {
                        return $price_html;
                    }
                    $club_price = $regular_price * ( 1 - $discount_percent / 100 );
                }

                // Output HTML
                $club_price_html = wc_price( $club_price );
                $club_label      = get_option( 'borspirit_club_price_label', __( 'Club price', 'borspirit' ) );

                $price_html .= '<span class="price__club"><span class="price-label">' . esc_html( $club_label ) . ':</span> <ins>' . $club_price_html . '</ins></span>';

                return $price_html;

            } catch ( Exception $e ) {
                error_log( 'Display Club Price Error: ' . $e->getMessage() );
                return $price;
            }
        }
        add_filter( 'woocommerce_get_price_html', 'borspirit_display_club_price', 20, 2 );
    }

    if ( ! function_exists( 'borspirit_apply_club_price_in_cart' ) ) {
        /**
         * Apply club price in cart for logged-in club members only.
         *
         * @param WC_Cart $cart WooCommerce cart object.
         */
        function borspirit_apply_club_price_in_cart( $cart ) {
            try {
                if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
                if ( ! is_user_logged_in() ) return;

                $user_id = get_current_user_id();
                if ( ! borspirit_is_club_member( $user_id ) ) return;

                foreach ( $cart->get_cart() as $cart_item ) {

                    if ( empty( $cart_item['data'] ) || ! is_a( $cart_item['data'], 'WC_Product' ) ) {
                        continue;
                    }

                    $product = $cart_item['data'];

                    if ( $product->is_type( 'subscription' ) || $product->is_type( 'variable-subscription' ) ) {
                        continue;
                    }

                    if ( $product->is_on_sale() ) {
                        continue;
                    }

                    // Manual price
                    $manual_club_price = get_post_meta( $product->get_id(), '_club_price', true );
                    $manual_club_price = is_numeric( $manual_club_price ) ? floatval( $manual_club_price ) : 0;

                    // Discount percent
                    $discount_percent = floatval( get_option( 'borspirit_club_discount_amount', 0 ) );
                    $discount_percent = $discount_percent > 0 ? $discount_percent : 0;

                    // If neither manual nor discount is valid → no club price
                    if ( $manual_club_price <= 0 && $discount_percent <= 0 ) {
                        continue;
                    }

                    // Determine club price
                    if ( $manual_club_price > 0 ) {
                        $club_price = $manual_club_price;
                    } else {
                        $regular_price = floatval( $product->get_regular_price() );
                        if ( $regular_price <= 0 ) {
                            continue;
                        }
                        $club_price = $regular_price * ( 1 - $discount_percent / 100 );
                    }

                    // Apply price
                    $cart_item['data']->set_price( $club_price );
                }

            } catch ( Exception $e ) {
                error_log( 'Apply Club Price Error: ' . $e->getMessage() );
            }
        }
        add_action( 'woocommerce_before_calculate_totals', 'borspirit_apply_club_price_in_cart' );
    }

    if ( ! function_exists( 'borspirit_mini_cart_club_price_only' ) ) {
        /**
         * Mini cart: show ONLY club price (with label) if user is a club member.
         *
         * @param string   $price_html    Original price HTML.
         * @param array    $cart_item     Cart item data.
         * @param string   $cart_item_key Cart item key.
         *
         * @return string Modified price HTML or original.
         */
        function borspirit_mini_cart_club_price_only( $price_html, $cart_item, $cart_item_key ) {
            try {
                // Must be logged in
                if ( ! is_user_logged_in() ) {
                    return $price_html;
                }

                // Must be club member
                $user_id = get_current_user_id();
                if ( ! borspirit_is_club_member( $user_id ) ) {
                    return $price_html;
                }

                // Validate product
                if ( empty( $cart_item['data'] ) || ! $cart_item['data'] instanceof WC_Product ) {
                    return $price_html;
                }

                /** @var WC_Product $product */
                $product = $cart_item['data'];

                // Exclude subscription items
                if ( $product->is_type( 'subscription' ) || $product->is_type( 'variable-subscription' ) ) {
                    return $price_html;
                }

                // Exclude sale products
                if ( $product->is_on_sale() ) {
                    return $price_html;
                }

                // --------------------------------------
                // CLUB PRICE HANDLING (matches logic)
                // --------------------------------------

                // Manual club price
                $manual_club_price = get_post_meta( $product->get_id(), '_club_price', true );
                $manual_club_price = is_numeric( $manual_club_price ) ? floatval( $manual_club_price ) : 0;

                // Discount percent
                $discount_percent = floatval( get_option( 'borspirit_club_discount_amount', 0 ) );
                $discount_percent = $discount_percent > 0 ? $discount_percent : 0;

                // If neither manual nor discount → do not show club price
                if ( $manual_club_price <= 0 && $discount_percent <= 0 ) {
                    return $price_html;
                }

                // Determine club price
                if ( $manual_club_price > 0 ) {

                    $club_price = $manual_club_price;

                } else {

                    $regular_price = floatval( $product->get_regular_price() );
                    if ( $regular_price <= 0 ) {
                        return $price_html;
                    }

                    $club_price = $regular_price * ( 1 - $discount_percent / 100 );
                }

                // --------------------------------------
                // MINI CART CLUB PRICE HTML
                // --------------------------------------

                $club_label = get_option(
                    'borspirit_club_price_label',
                    __( 'Club price', 'borspirit' )
                );

                $club_html  = '<span class="mini-price-club">';
                $club_html .= '<span class="price-label">' . esc_html( $club_label ) . ':</span> ';
                $club_html .= '<ins aria-hidden="true">' . wc_price( $club_price ) . '</ins>';
                $club_html .= '</span>';

                return $club_html;

            } catch ( Exception $e ) {
                error_log( 'Mini Cart Club Price Only Error: ' . $e->getMessage() );
                return $price_html;
            }
        }
        add_filter( 'woocommerce_cart_item_price', 'borspirit_mini_cart_club_price_only', 10, 3 );
    }

    if ( ! function_exists( 'borspirit_show_club_progress_message' ) ) {
        /**
         * Display a message showing how much more the user needs to spend
         * to become a Club Member (threshold: 100 000 HUF), only if the product
         * has a club price or a club discount.
         */
        function borspirit_show_club_progress_message() {
            try {
                // Only logged-in users have spending history
                if ( ! is_user_logged_in() ) {
                    return;
                }

                global $product;

                // Check if product exists
                if ( ! $product instanceof WC_Product ) {
                    return;
                }

                // Do not show for sale products
                if ( $product->is_on_sale() ) {
                    return;
                }

                // Check for manual club price
                $manual_club_price = get_post_meta( $product->get_id(), '_club_price', true );
                $manual_club_price = is_numeric( $manual_club_price ) ? floatval( $manual_club_price ) : 0;

                // Check for club discount
                $discount_percent = floatval( get_option( 'borspirit_club_discount_amount', 0 ) );
                $discount_percent = $discount_percent > 0 ? $discount_percent : 0;

                // If neither manual nor discount → do not show club progress message
                if ( $manual_club_price <= 0 && $discount_percent <= 0 ) {
                    return;
                }

                $user_id = get_current_user_id();
                $total_spent = wc_get_customer_total_spent( $user_id );
                $threshold   = 100000;

                if ( ! is_numeric( $total_spent ) ) {
                    return;
                }

                // User already reached club membership
                if ( $total_spent >= $threshold ) {
                    return;
                }

                // Calculate remaining amount
                $remaining = $threshold - $total_spent;
                if ( $remaining <= 0 ) {
                    return;
                }

                // Format currency
                $remaining_html = wc_price( $remaining );

                // Build message
                $message = sprintf(
                    __( 'Spend %s more to become a Club Member and unlock exclusive prices!', 'borspirit' ),
                    $remaining_html
                );

                // Prevent showing the notice multiple times on the same page load
                if ( ! wc_has_notice( $message, 'notice' ) ) {
                    wc_print_notice( $message, 'notice' );
                }

            } catch ( Exception $e ) {
                error_log( 'Club Progress Message Error: ' . $e->getMessage() );
            }
        }
        add_action( 'woocommerce_after_add_to_cart_form', 'borspirit_show_club_progress_message', 10 );
    }

    // ============================================================
    // 12. SHOP LOOP MODIFICATIONS
    // ============================================================

    if ( ! function_exists( 'custom_add_to_cart_text_type' ) ) {
        /**
         * Modify the WooCommerce "Add to cart" text by product type.
         *
         * @param string $text Default button text.
         * @return string Modified button text.
         */
        function custom_add_to_cart_text_type( $text ) {
            global $product;

            if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
                return $text;
            }

            switch ( true ) {
                case $product->is_type( 'simple' ):
                    $text = __( 'Add to cart', 'borspirit' );
                    break;

                case $product->is_type( 'variable' ):
                    $text = __( 'Select options', 'borspirit' );
                    break;

                case $product->is_type( 'grouped' ):
                    $text = __( 'View products', 'borspirit' );
                    break;

                case $product->is_type( 'subscription' ):
                    $text = get_option( 'woocommerce_subscriptions_add_to_cart_button_text', __( 'Subscribe now', 'borspirit' ) );
                    break;

                case $product->is_type( 'variable-subscription' ):
                    $text = get_option( 'woocommerce_subscriptions_add_to_cart_button_text', __( 'Select subscription', 'borspirit' ) );
                    break;

                default:
                    $text = __( 'Buy now', 'borspirit' );
                    break;
            }

            return $text;
        }
        add_filter( 'woocommerce_product_single_add_to_cart_text', 'custom_add_to_cart_text_type' );
        add_filter( 'woocommerce_product_add_to_cart_text', 'custom_add_to_cart_text_type' );
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
    // 13. PRODUCT TITLE
    // ============================================================

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
                    $new_columns['product_subtitle'] = __( 'Product subtitle', 'borspirit' );
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
                            <span class="title"><?php echo esc_html__( 'Product subtitle', 'borspirit' ); ?></span>
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
    // 14. SHIPPING
    // ============================================================

    if ( ! function_exists( 'show_free_shipping_notice' ) ) {
        /**
         * Display a notice showing how much more a customer needs to spend 
         * to qualify for free shipping.
         */
        function show_free_shipping_notice() {
            if ( ! WC()->cart || WC()->cart->is_empty() ) {
                return; // Only show when cart has products
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

            // Get shipping zone and methods
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
                    $minimum_amount = (float) ( $method->min_amount ?? 0 );
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

            $remaining_amount = max( 0, $minimum_amount - $current_amount );

            $message = sprintf(
                __( 'Add %1$s more to your cart to qualify for free shipping! %2$s', 'borspirit' ),
                wc_price( $remaining_amount ),
                '<a href="' . wc_get_page_permalink( 'shop' ) . '" class="button wc-forward">' . get_the_title(SHOP_PAGE_ID) . '</a>'
            );

            echo '<div class="woocommerce-notices-wrapper"><div class="woocommerce-message free-shipping-notice" role="alert">' . $message . '</div></div>';
        }

        // Hooks for where to display the message
        //add_action( 'woocommerce_archive_description', 'show_free_shipping_notice', 20 );
        add_action( 'woocommerce_before_cart', 'show_free_shipping_notice' );
        add_action( 'woocommerce_before_checkout_form', 'show_free_shipping_notice' );
        add_action( 'bbloomer_before_woocommerce/cart', 'show_free_shipping_notice' );
        add_action( 'bbloomer_before_woocommerce/checkout', 'show_free_shipping_notice' );

        /**
         * AJAX: Return remaining amount + notice HTML
         */
        function get_remaining_free_shipping_amount() {
            if ( ! WC()->cart ) {
                wp_send_json_error( [ 'message' => 'Cart not found' ] );
            }

            // If cart empty → hide notice
            if ( WC()->cart->is_empty() ) {
                wp_send_json_success( [ 'remaining' => '', 'notice_html' => '' ] );
            }

            // Determine customer country
            $customer_country = WC()->customer->get_shipping_country();

            if ( empty( $customer_country ) ) {
                $geo = WC_Geolocation::geolocate_ip();
                $customer_country = $geo['country'] ?? '';
            }

            if ( empty( $customer_country ) ) {
                wp_send_json_error( [ 'message' => 'No country found' ] );
            }

            // Find free shipping rule
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
                    $minimum_amount = (float) ( $method->min_amount ?? 0 );
                    break;
                }
            }

            if ( $minimum_amount <= 0 ) {
                wp_send_json_error( [ 'message' => 'Free shipping not available' ] );
            }

            $display_prices_include_tax = wc_prices_include_tax();
            $current_amount = $display_prices_include_tax
                ? (float) WC()->cart->get_subtotal() + (float) WC()->cart->get_subtotal_tax()
                : (float) WC()->cart->get_subtotal();

            $remaining_amount = max( 0, $minimum_amount - $current_amount );

            $remaining_html = wc_price( $remaining_amount );

            $notice_html = '';
            if ( $remaining_amount > 0 ) {
                $notice_html = sprintf(
                    '<div class="woocommerce-message free-shipping-notice" role="alert">%s</div>',
                    sprintf(
                        __( 'Add %1$s more to your cart to qualify for free shipping! %2$s', 'borspirit' ),
                        wc_price( $remaining_amount ),
                        '<a href="' . wc_get_page_permalink( 'shop' ) . '" class="button wc-forward">' . get_the_title(SHOP_PAGE_ID) . '</a>'
                    )
                );
            }

            wp_send_json_success( [
                'remaining'   => $remaining_html,
                'notice_html' => $notice_html,
            ] );
        }
        add_action( 'wp_ajax_get_remaining_free_shipping_amount', 'get_remaining_free_shipping_amount' );
        add_action( 'wp_ajax_nopriv_get_remaining_free_shipping_amount', 'get_remaining_free_shipping_amount' );

        /**
         * Enqueue inline JS to show/hide free shipping notice dynamically.
         */
        function enqueue_free_shipping_notice_script() {
            $nonce = wp_create_nonce( 'get_remaining_free_shipping_amount' );

            wp_add_inline_script( 'wc-add-to-cart', "
                jQuery(function($){
                    function updateFreeShipping() {
                        $.post(wc_add_to_cart_params.ajax_url, {
                            action: 'get_remaining_free_shipping_amount',
                            _ajax_nonce: '{$nonce}'
                        }, function(response){
                            if (!response?.success) return;

                            let noticeContainer = $('.woocommerce-notices-wrapper');

                            // Remove any old notices
                            noticeContainer.find('.free-shipping-notice').remove();

                            // Add new notice if available
                            if (response.data.notice_html) {
                                const notice = $(response.data.notice_html);

                                // Ensure no inline display:none remains
                                notice.removeAttr('style');

                                noticeContainer.append(notice);
                                notice.fadeIn(300);
                            }
                        });
                    }

                    // Update when cart changes or fragments refresh
                    $(document.body)
                        .on('added_to_cart removed_from_cart updated_cart_totals wc_fragments_refreshed', updateFreeShipping);

                    // Initial load
                    updateFreeShipping();
                });
            " );
        }
        add_action( 'wp_enqueue_scripts', 'enqueue_free_shipping_notice_script' );
    }

    if ( ! function_exists( 'hide_flat_rate_when_free_available' ) ) {
        /**
         * Hide Flat Rate shipping when Free Shipping is available in WooCommerce.
         *
         * This function checks if free shipping exists for the cart. 
         * If so, it removes all flat rate shipping options from the available rates.
         *
         * @param array $rates Array of shipping rates available for the package.
         * @return array Filtered shipping rates.
         */
        function hide_flat_rate_when_free_available( $rates ) {

            $free_shipping_exists = false;

            // Check if free shipping is available
            foreach ( $rates as $rate ) {
                if ( $rate->method_id === 'free_shipping' ) {
                    $free_shipping_exists = true;
                    break;
                }
            }

            // Remove flat rate if free shipping exists
            if ( $free_shipping_exists ) {
                foreach ( $rates as $rate_id => $rate ) {
                    if ( $rate->method_id === 'flat_rate' ) {
                        unset( $rates[ $rate_id ] );
                    }
                }
            }

            return $rates;
        }
        add_filter( 'woocommerce_package_rates', 'hide_flat_rate_when_free_available', 100 );
    }

    // ============================================================
    // 15. CHECKOUT FIELDS MODIFICATIONS
    // ============================================================

    if ( ! function_exists( 'my_customize_country_locale' ) ) {
        /**
         * Modify WooCommerce country locale:
         * - Force the "state" field to be required and visible.
         * - Change the order of address fields.
         *
         * @param array $locale Country locale settings.
         * @return array Modified locale settings.
         */
        function my_customize_country_locale( $locale ) {
            foreach ( $locale as $country_code => $fields ) {

                // Ensure state is required + visible
                $locale[ $country_code ]['state'] = [
                    'required' => true,
                    'hidden'   => false,
                    'priority' => 45, // Add priority to change order
                ];

                /**
                 * Change the order of fields by controlling priority.
                 * Lower number = earlier in the form.
                 * Adjust these values as needed.
                 */
                if ( isset( $locale[ $country_code ]['postcode'] ) ) {
                    $locale[ $country_code ]['postcode']['priority'] = 50;
                }

                if ( isset( $locale[ $country_code ]['city'] ) ) {
                    $locale[ $country_code ]['city']['priority'] = 60;
                }

                if ( isset( $locale[ $country_code ]['address_1'] ) ) {
                    $locale[ $country_code ]['address_1']['priority'] = 70;
                }

                if ( isset( $locale[ $country_code ]['address_2'] ) ) {
                    $locale[ $country_code ]['address_2']['priority'] = 80;
                }
            }

            return $locale;
        }
        add_filter( 'woocommerce_get_country_locale', 'my_customize_country_locale' );
    }

    // ============================================================
    // 16. AGE CONFIRMATION
    // ============================================================

    /**
     * WooCommerce 18+ Age Confirmation Checkbox for Checkout (HPOS-Compatible)
     *
     * Adds an "I confirm I am 18 years or older" checkbox after Order Notes.
     * Saves the value in HPOS orders and displays it in the admin order page.
     */

    if ( ! function_exists( 'my_plugin_register_age_confirmation_field' ) ) {
        /**
         * Register the 18+ age confirmation checkout field.
         */
        function my_plugin_register_age_confirmation_field() {

            // Only proceed if WooCommerce provides the function.
            if ( ! function_exists( 'woocommerce_register_additional_checkout_field' ) ) {
                wc_get_logger()->debug(
                    'Age confirmation field not registered — missing woocommerce_register_additional_checkout_field()',
                    array( 'source' => 'my-plugin-age-confirm' )
                );
                return;
            }

            $min_age = absint( get_option( 'ag_min_age', 18 ) );
            if ( $min_age <= 0 ) {
                $min_age = 18;
            }

            try {
                woocommerce_register_additional_checkout_field( array(
                    'id'            => 'my_plugin/age_confirmation',
                    'label'         => sprintf(
                        esc_html__( 'You must confirm you are over %d before placing the order.', 'borspirit' ),
                        $min_age
                    ),
                    'location'      => 'order',
                    'type'          => 'checkbox',
                    'required'      => true,
                    'error_message' => esc_html__( 'Please check this box if you want to proceed.', 'borspirit' )
                ) );
            } catch ( Exception $e ) {
                wc_get_logger()->error(
                    'Failed to register age confirmation field: ' . $e->getMessage(),
                    array( 'source' => 'my-plugin-age-confirm' )
                );
            }
        }
        add_action( 'woocommerce_init', 'my_plugin_register_age_confirmation_field' );
    }

    // ============================================================
    // 17. MARKETING NEWSLETTER OPT-IN
    // ============================================================

    if ( ! function_exists( 'my_plugin_register_marketing_optin_field' ) ) {
        /**
         * Register the marketing newsletter opt-in checkout field.
         */
        function my_plugin_register_marketing_optin_field() {

            // Ensure WooCommerce provides the function.
            if ( ! function_exists( 'woocommerce_register_additional_checkout_field' ) ) {
                wc_get_logger()->debug(
                    'Marketing opt-in field not registered — missing woocommerce_register_additional_checkout_field()',
                    array( 'source' => 'my-plugin-marketing-optin' )
                );
                return;
            }

            try {
                woocommerce_register_additional_checkout_field( array(
                    'id'       => 'my_plugin/marketing_opt_in',
                    'label'    => esc_html__( 'Do you want to subscribe to our newsletter?', 'borspirit' ),
                    'location' => 'contact',
                    'type'     => 'checkbox',
                    'required' => false,
                ) );
            } catch ( Exception $e ) {
                wc_get_logger()->error(
                    'Failed to register marketing opt-in field: ' . $e->getMessage(),
                    array( 'source' => 'my-plugin-marketing-optin' )
                );
            }
        }
        add_action( 'woocommerce_init', 'my_plugin_register_marketing_optin_field' );
    }

    if ( ! function_exists( 'my_plugin_handle_newsletter_subscription' ) ) {
        /**
         * Handle newsletter subscription after WooCommerce checkout.
         *
         * Triggered on the `woocommerce_thankyou` hook. Validates the order,
         * checks whether the customer opted into marketing, and if so, attempts
         * to subscribe them to Mailchimp using the configured API credentials.
         *
         * @param int $order_id WooCommerce order ID.
         *
         * @return void
         */
        function my_plugin_handle_newsletter_subscription( $order_id ) {

            // Validate order ID
            if ( empty( $order_id ) || ! is_numeric( $order_id ) ) {
                wc_get_logger()->warning(
                    'Invalid order ID passed to newsletter subscription handler.',
                    [ 'source' => 'my-plugin-marketing-optin' ]
                );
                return;
            }

            // Retrieve order object
            $order = wc_get_order( $order_id );
            if ( ! $order ) {
                wc_get_logger()->error(
                    'Order not found for newsletter subscription handler.',
                    [ 'source' => 'my-plugin-marketing-optin', 'order_id' => $order_id ]
                );
                return;
            }

            // Check if the customer opted in
            $opt_in = $order->get_meta( '_wc_other/my_plugin/marketing_opt_in' );

            if ( empty( $opt_in ) ) {
                return; // Customer did not check the box
            }

            // Get customer data
            $email      = $order->get_billing_email();
            $first_name = $order->get_billing_first_name();
            $last_name  = $order->get_billing_last_name();

            if ( empty( $email ) ) {
                wc_get_logger()->warning(
                    'Newsletter subscription skipped — missing customer email.',
                    [ 'source' => 'my-plugin-marketing-optin' ]
                );
                return;
            }

            // Mailchimp credentials
            $api_key     = get_field( 'mailchimp_api_key', 'option' ) ?: MAILCHIMP_API_KEY;
            $audience_id = get_field( 'mailchimp_audience_id', 'option' ) ?: MAILCHIMP_AUDIENCE_ID;

            if ( empty( $api_key ) || empty( $audience_id ) ) {
                wc_get_logger()->error(
                    'Mailchimp configuration missing — cannot subscribe user.',
                    [ 'source' => 'my-plugin-marketing-optin' ]
                );
                return;
            }

            try {
                // Ensure class exists
                if ( ! class_exists( 'MailchimpService' ) ) {
                    wc_get_logger()->error(
                        'MailchimpService class not found — subscription aborted.',
                        [ 'source' => 'my-plugin-marketing-optin' ]
                    );
                    return;
                }

                $mailchimp = new MailchimpService( $api_key, $audience_id );

                $mailchimp->subscribe(
                    $email,
                    $first_name,
                    $last_name,
                    [ 'webshop' ],
                    'subscribed'
                );

                wc_get_logger()->info(
                    'User subscribed to Mailchimp successfully.',
                    [ 'source' => 'my-plugin-marketing-optin', 'email' => $email ]
                );

            } catch ( Exception $e ) {

                wc_get_logger()->error(
                    'Mailchimp subscription failed: ' . $e->getMessage(),
                    [ 'source' => 'my-plugin-marketing-optin', 'email' => $email ]
                );
            }
        }
        add_action( 'woocommerce_thankyou', 'my_plugin_handle_newsletter_subscription', 20, 1 );
    }
