<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! function_exists( 'should_abort_shortcode' ) ) {
        /**
         * Utility: Prevent shortcodes from running in admin/AJAX/CRON/REST.
         */
        function should_abort_shortcode() {
            // Avoid running in admin/AJAX/CRON/REST contexts (these can produce invalid JSON responses)
            if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
                return true;
            }

            // wp_doing_rest() added in WP 5.7 — check existence for backwards compatibility
            if ( ( function_exists( 'wp_doing_rest' ) && wp_doing_rest() ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
                return true;
            }

            return false;
        }
    }
    
    if ( ! function_exists( 'custom_wc_registration_form_shortcode' ) ) {
        /**
         * Registration Form Shortcode
         *
         * Displays only the WooCommerce registration form.
         * If the user is logged in, shows a message instead.
         *
         * @return string HTML output for registration form or message.
         */
        function custom_wc_registration_form_shortcode() {
            if ( should_abort_shortcode() ) return '';
            if ( ! class_exists( 'WooCommerce' ) ) return '';

            if ( is_user_logged_in() ) {
                return wpautop( esc_html__( 'You are already registered.', 'borspirit' ) );
            }

            ob_start();

            do_action( 'woocommerce_before_customer_login_form' );

            $html = wc_get_template_html( 'myaccount/form-login.php' );

            if ( empty( $html ) ) {
                return wpautop( esc_html__( 'Registration form not available.', 'borspirit' ) );
            }

            libxml_use_internal_errors( true );

            $dom = new DOMDocument();
            $dom->encoding = 'utf-8';

            $loaded = $dom->loadHTML(
                '<?xml encoding="utf-8" ?>' . $html,
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );

            libxml_clear_errors();

            if ( ! $loaded ) {
                return wpautop( esc_html__( 'Error loading registration form.', 'borspirit' ) );
            }

            $xpath = new DOMXPath( $dom );
            $form  = $xpath->query( '//form[contains(@class,"register")]' );
            $form  = $form->item( 0 );

            if ( $form ) {
                echo $dom->saveHTML( $form );
            } else {
                echo wpautop( esc_html__( 'Registration form not found.', 'borspirit' ) );
            }

            return ob_get_clean();
        }
        add_shortcode( 'custom_wc_registration_form', 'custom_wc_registration_form_shortcode' );
    }

    if ( ! function_exists( 'custom_wc_login_form_shortcode' ) ) {
        /**
         * Login Form Shortcode
         *
         * Displays only the WooCommerce login form.
         * If the user is logged in, shows a message instead.
         *
         * @return string HTML output for login form or message.
         */
        function custom_wc_login_form_shortcode() {
            if ( should_abort_shortcode() ) return '';
            if ( ! class_exists( 'WooCommerce' ) ) return '';

            if ( is_user_logged_in() ) {
                return wpautop( esc_html__( 'You are already logged in.', 'borspirit' ) );
            }

            ob_start();

            do_action( 'woocommerce_before_customer_login_form' );

            woocommerce_login_form( [
                'redirect' => wc_get_page_permalink( 'myaccount' ),
            ] );

            return ob_get_clean();
        }
        add_shortcode( 'custom_wc_login_form', 'custom_wc_login_form_shortcode' );
    }

    if ( ! function_exists( 'custom_wc_redirect_logged_in_users' ) ) {
        /**
         * Redirect Logged-In Users Away From Login/Registration Shortcodes
         *
         * If a logged-in user tries to access a page containing
         * the login or registration shortcodes, redirect them
         * to the "My Account" page instead.
         *
         * @return void
         */
        function custom_wc_redirect_logged_in_users() {
            if ( ! class_exists( 'WooCommerce' ) ) {
                return;
            }

            if ( ! is_user_logged_in() || ! is_page() ) {
                return;
            }

            global $post;

            if ( ! $post instanceof WP_Post ) {
                return;
            }

            $content = $post->post_content;

            if ( has_shortcode( $content, 'custom_wc_login_form' ) || has_shortcode( $content, 'custom_wc_registration_form' ) ) {
                wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
                exit;
            }
        }
        add_action( 'template_redirect', 'custom_wc_redirect_logged_in_users' );
    }

    if ( ! function_exists( 'get_woocommerce_general_settings' ) ) {
        /**
         * Get WooCommerce general settings.
         *
         * @return array Array of WooCommerce general settings.
         */
        function get_woocommerce_general_settings() {
            $settings = [
                'store_address'      => get_option( 'woocommerce_store_address' ),
                'store_address_2'    => get_option( 'woocommerce_store_address_2' ),
                'store_city'         => get_option( 'woocommerce_store_city' ),
                'store_postcode'     => get_option( 'woocommerce_store_postcode' ),
                'default_country'    => get_option( 'woocommerce_default_country' ),
                'allowed_countries'  => get_option( 'woocommerce_allowed_countries' ),
                'specific_countries' => get_option( 'woocommerce_specific_allowed_countries' ),
                'ship_to_countries'  => get_option( 'woocommerce_ship_to_countries' ),
                'specific_ship_to'   => get_option( 'woocommerce_specific_ship_to_countries' ),
                'customer_location'  => get_option( 'woocommerce_default_customer_address' ),
                'enable_taxes'       => get_option( 'woocommerce_calc_taxes' ),
                'currency'           => get_option( 'woocommerce_currency' ),
                'currency_position'  => get_option( 'woocommerce_currency_pos' ),
                'num_decimals'       => get_option( 'woocommerce_price_num_decimals' ),
                'thousand_separator' => get_option( 'woocommerce_price_thousand_sep' ),
                'decimal_separator'  => get_option( 'woocommerce_price_decimal_sep' ),
            ];

            return $settings;
        }
    }

    if ( ! function_exists( 'woocommerce_general_settings_shortcode' ) ) {
        /**
         * Display WooCommerce general settings via a shortcode.
         *
         * This shortcode retrieves WooCommerce general settings using the
         * `get_woocommerce_general_settings()` helper function and displays them
         * as formatted HTML. You can optionally specify a single setting to display
         * using the `setting` attribute.
         *
         * Shortcode: [woocommerce_settings]
         *
         * Example usage:
         *   [woocommerce_settings]                 - Displays all WooCommerce settings.
         *   [woocommerce_settings setting="store_address"] - Displays a single setting value.
         *
         * @since 1.0.0
         * @param array $atts {
         *     Optional. Shortcode attributes.
         *
         *     @type string $setting Specific WooCommerce setting key to display. Default '' (show all settings).
         * }
         * @return string HTML output containing the requested WooCommerce settings or an error message.
         */
        function woocommerce_general_settings_shortcode( $atts ) {
            if ( should_abort_shortcode() ) return '';
            if ( ! class_exists( 'WooCommerce' ) ) return '';

            // Parse shortcode attributes
            $atts = shortcode_atts(
                [
                    'setting' => '', // Default empty means show all settings
                ],
                $atts,
                'woocommerce_settings'
            );

            $settings = get_woocommerce_general_settings();

            // If a specific setting is requested
            if ( ! empty( $atts['setting'] ) ) {
                $key = sanitize_text_field( $atts['setting'] );

                if ( isset( $settings[ $key ] ) ) {
                    $value = is_array( $settings[ $key ] ) ? implode( ', ', $settings[ $key ] ) : $settings[ $key ];
                    return esc_html( $value );
                } else {
                    return '<p><em>Setting "' . esc_html( $key ) . '" not found.</em></p>';
                }
            }

            // Otherwise show all settings
            $output = '<ul>';
            foreach ( $settings as $key => $value ) {
                $output .= '<li><strong>' . esc_html( $key ) . ':</strong> ' . esc_html( is_array( $value ) ? implode( ', ', $value ) : $value ) . '</li>';
            }
            $output .= '</ul>';

            return $output;
        }
        add_shortcode( 'woocommerce_settings', 'woocommerce_general_settings_shortcode' );
    }

    if ( ! function_exists( 'site_address_shortcode' ) ) {
        /**
         * Shortcode to display the full address.
         *
         * This shortcode combines the postcode, city, address, and
         * address line 2 (if available) into a single, formatted string.
         *
         * Usage: [site_address]
         *
         * @return string The formatted address.
         */
        function site_address_shortcode() {
            // Use ACF first
            $site_address   = get_field( 'site_address', 'option' ) ?? '';
            $site_address_2 = get_field( 'site_address_2', 'option' ) ?? '';
            $site_city      = get_field( 'site_city', 'option' ) ?? '';
            $site_postcode  = get_field( 'site_postcode', 'option' ) ?? '';
            $site_country   = get_field( 'site_country', 'option' ) ?? '';

            // If WooCommerce is active, fallback to its options if ACF is empty
            if ( class_exists( 'WooCommerce' ) ) {
                $site_address   = $site_address   ?: get_option( 'woocommerce_store_address' );
                $site_address_2 = $site_address_2 ?: get_option( 'woocommerce_store_address_2' );
                $site_city      = $site_city      ?: get_option( 'woocommerce_store_city' );
                $site_postcode  = $site_postcode  ?: get_option( 'woocommerce_store_postcode' );
                $site_country   = $site_country   ?: get_option( 'woocommerce_default_country' );
            }

            // Build full address
            $full_address = trim( $site_postcode . ' ' . $site_city . ', ' . $site_address );
            if ( ! empty( $site_address_2 ) ) {
                $full_address .= ', ' . $site_address_2;
            }

            return esc_html( $full_address );
        }
        add_shortcode( 'site_address', 'site_address_shortcode' );
    }

    if ( ! function_exists( 'site_phone_shortcode' ) ) {
        /**
         * Shortcode: [site_phone]
         *
         * Outputs the site phone number from the ACF Options Page as a clickable tel: link.
         *
         * @return string HTML anchor tag with phone number or empty string if not set or invalid.
         */
        function site_phone_shortcode() {
            if ( should_abort_shortcode() ) return '';

            $phone_raw = get_field( 'site_phone', 'option' );

            if ( empty( $phone_raw ) || ! is_string( $phone_raw ) ) {
                return '';
            }

            $phone_link = preg_replace( '/[^0-9\+]/', '', $phone_raw );

            if ( empty( $phone_link ) ) {
                return '';
            }

            return '<a href="'. esc_attr( 'tel:' . $phone_link ) .'">' . esc_html( $phone_raw ) . '</a>';
        }
        add_shortcode( 'site_phone', 'site_phone_shortcode' );
    }

    if ( ! function_exists( 'site_email_shortcode' ) ) {
        /**
         * Shortcode: [site_email]
         *
         * Outputs the site email address from the ACF Options Page as a clickable mailto: link.
         *
         * @return string HTML anchor tag with email address or empty string if not set or invalid.
         */
        function site_email_shortcode() {
            if ( should_abort_shortcode() ) return '';

            $email_raw = get_field( 'site_email', 'option' );

            if ( empty( $email_raw ) || ! is_string( $email_raw ) || ! is_email( $email_raw ) ) {
                return '';
            }

            // sanitize once
            $email = sanitize_email( $email_raw );

            // obfuscate separately for text and for mailto:
            $email_obfuscated = antispambot( $email, 1 );
            $email_text = antispambot( $email );

            return '<a href="' . esc_url( 'mailto:' . $email_obfuscated ) . '">' . esc_html( $email_text ) . '</a>';
        }
        add_shortcode( 'site_email', 'site_email_shortcode' );
    }

    if ( ! function_exists( 'wp_google_map_shortcode' ) ) {
        /**
         * Generates an embeddable Google Map iframe via shortcode.
         *
         * Shortcode: [google_map location="Location" zoom="13" width="800" height="300"]
         *
         * @param array $atts Shortcode attributes.
         * @return string HTML iframe or error message.
         */
        function wp_google_map_shortcode( $atts ) {
            // Set default attributes
            $atts = shortcode_atts(
                array(
                    'location' => '',
                    'zoom'     => 13,
                    'width'    => '800',
                    'height'   => '300',
                ),
                $atts,
                'google_map'
            );

            // Validate and sanitize attributes
            $location = trim( $atts['location'] );
            $zoom     = intval( $atts['zoom'] );
            $width    = esc_attr( $atts['width'] );
            $height   = esc_attr( $atts['height'] );

            // Error handling
            if ( empty( $location ) ) {
                return '<p style="color:red;">Error: No location provided for the map.</p>';
            }

            if ( $zoom < 0 || $zoom > 21 ) {
                $zoom = 13; // default zoom if invalid
            }

            if ( empty( $width ) ) {
                $width = '800';
            }

            if ( empty( $height ) ) {
                $height = '300';
            }

            // Encode location for URL
            $location_encoded = urlencode( $location );

            // Build and escape the Google Maps URL
            $map_url = esc_url( "https://www.google.com/maps?q={$location_encoded}&z={$zoom}&output=embed" );

            // Return the iframe HTML
            return "<iframe width='{$width}' height='{$height}' loading='lazy' allowfullscreen referrerpolicy='no-referrer-when-downgrade' src='{$map_url}' title='{$location}'></iframe>";
        }
        add_shortcode( 'google_map', 'wp_google_map_shortcode' );
    }

    if ( ! function_exists( 'render_opening_hours_table' ) ) {
        /**
         * Render Opening Hours Table
         *
         * Works with both:
         *  - ACF Group Field (true_false + time_picker fields)
         *  - Plain PHP Array format
         *
         * @param array  $opening_hours Opening hours data.
         * @param bool   $acf_mode      If true, expects ACF-style array. If false, expects simple array.
         * @param string $text_domain   Text domain for translations.
         */
        function render_opening_hours_table( $opening_hours, $acf_mode = true, $text_domain = 'borspirit' ) {

            // Day labels with translation support
            $days = [
                'monday'    => __( 'Monday', $text_domain ),
                'tuesday'   => __( 'Tuesday', $text_domain ),
                'wednesday' => __( 'Wednesday', $text_domain ),
                'thursday'  => __( 'Thursday', $text_domain ),
                'friday'    => __( 'Friday', $text_domain ),
                'saturday'  => __( 'Saturday', $text_domain ),
                'sunday'    => __( 'Sunday', $text_domain ),
            ];

            ob_start();

            echo '<table class="opening-hours" role="table">';
            echo '<thead class="visually-hidden"><tr>';
            echo '<th scope="col">' . esc_html( __( 'Nap', $text_domain ) ) . '</th>';
            echo '<th scope="col">' . esc_html( __( 'Nyitvatartás', $text_domain ) ) . '</th>';
            echo '</tr></thead><tbody>';

            foreach ( $days as $key => $label ) {
                echo '<tr>';
                echo '<th scope="row">' . esc_html( $label ) . '</th>';

                if ( $acf_mode ) {
                    // ACF FIELD MODE
                    $status = $opening_hours[ $key . '_status' ] ?? 0;

                    if ( $status ) {
                        $open  = $opening_hours[ $key . '_open' ] ?? '';
                        $close = $opening_hours[ $key . '_close' ] ?? '';

                        if ( $open && $close ) {
                            $open_fmt  = wp_safe_format_time( $open, 'g:i a' );
                            $close_fmt = wp_safe_format_time( $close, 'g:i a' );
                            echo '<td>' . esc_html( $open_fmt ) . ' - ' . esc_html( $close_fmt ) . '</td>';
                        }
                    } else {
                        echo '<td>' . esc_html( __( 'Zárva', $text_domain ) ) . '</td>';
                    }
                } else {
                    // SIMPLE ARRAY MODE
                    $open  = $opening_hours[ $key ]['open'] ?? 0;
                    $close = $opening_hours[ $key ]['close'] ?? 0;

                    if ( $open == 0 && $close == 0 ) {
                        echo '<td>' . esc_html( __( 'Zárva', $text_domain ) ) . '</td>';
                    } else {
                        $open_fmt  = wp_safe_format_time( sprintf( '%02d:00', $open ) );
                        $close_fmt = wp_safe_format_time( sprintf( '%02d:00', $close ) );
                        echo '<td>' . esc_html( $open_fmt ) . ' - ' . esc_html( $close_fmt ) . '</td>';
                    }
                }

                echo '</tr>';
            }

            echo '</tbody></table>';

            return ob_get_clean();
        }
    }

    if ( ! function_exists( 'opening_hours_shortcode' ) ) {
        /**
         * Display the store's opening hours via a shortcode.
         *
         * Retrieves the "opening_hours" field from ACF (Advanced Custom Fields) options
         * and displays it using the `render_opening_hours_table()` helper function.
         * If ACF is not active or the field is empty, it displays a relevant message instead.
         *
         * Shortcode: [opening_hours]
         *
         * Example usage:
         *   [opening_hours]
         *   echo do_shortcode('[opening_hours]');
         *
         * @since 1.0.0
         * @return string HTML markup for the opening hours table, or a message if not available.
         */
        function opening_hours_shortcode() {
            if ( should_abort_shortcode() ) return '';

            if ( ! function_exists( 'get_field' ) ) {
                return wpautop( esc_html__( 'ACF plugin is not active.', 'borspirit' ) );
            }

            $opening_hours = get_field( 'opening_hours', 'option' );

            if ( empty( $opening_hours ) ) {
                return wpautop( esc_html__( 'No opening hours specified.', 'borspirit' ) );
            }

            return render_opening_hours_table( $opening_hours, true, 'borspirit' );
        }
        add_shortcode( 'opening_hours', 'opening_hours_shortcode' );
    }

    if ( ! function_exists( 'get_wc_free_shipping_amount' ) ) {
        /**
         * Retrieve and display the WooCommerce free shipping minimum amount.
         *
         * Determines the customer's shipping country using the WooCommerce session or IP geolocation,
         * finds the appropriate shipping zone, and returns the formatted minimum order amount required
         * to qualify for free shipping.
         *
         * This function is also registered as a shortcode: [free_shipping_amount]
         * Example usage in content or templates:
         *   [free_shipping_amount]
         *   echo do_shortcode('[free_shipping_amount]');
         *
         * @since 1.0.0
         * @return string The formatted free shipping minimum amount (e.g. "$50.00"), or an empty string if not available.
         */
        function get_wc_free_shipping_amount() {
            if ( should_abort_shortcode() ) return '';

            // Ensure WooCommerce is active
            if ( ! function_exists( 'WC' ) || ! class_exists( 'WC_Shipping_Zones' ) ) {
                return '';
            }

            // Safely get customer country
            $customer_country = '';

            try {
                if ( WC()->customer ) {
                    $customer_country = WC()->customer->get_shipping_country();
                }
            } catch ( \Throwable $e ) {
                // swallow — we'll try other methods
                $customer_country = '';
            }

            if ( empty( $customer_country ) ) {
                if ( class_exists( 'WC_Geolocation' ) && is_callable( [ 'WC_Geolocation', 'geolocate_ip' ] ) ) {
                    $geo = WC_Geolocation::geolocate_ip();
                    $customer_country = $geo['country'] ?? '';
                }
            }

            if ( empty( $customer_country ) ) {
                // try base store country
                if ( WC()->customer ) {
                    WC()->customer->set_to_base();
                    $customer_country = WC()->customer->get_shipping_country();
                }
            }

            if ( empty( $customer_country ) ) {
                return '';
            }

            $package = array(
                'destination' => array(
                    'country'  => $customer_country,
                    'state'    => '',
                    'postcode' => '',
                    'city'     => '',
                    'address'  => '',
                ),
            );

            $customer_zone = WC_Shipping_Zones::get_zone_matching_package( $package );
            if ( ! $customer_zone ) {
                return '';
            }

            $methods = $customer_zone->get_shipping_methods();

            foreach ( $methods as $method ) {
                if ( ! is_object( $method ) ) {
                    continue;
                }

                // Method id can be like 'free_shipping' or 'free_shipping:3' depending on instance — use strpos
                if ( strpos( $method->id, 'free_shipping' ) !== false && ( isset( $method->enabled ) && $method->enabled === 'yes' ) ) {
                    $min_amount = $method->get_option( 'min_amount' );
                    if ( is_numeric( $min_amount ) && $min_amount > 0 ) {
                        return wc_price( $min_amount );
                    }
                }
            }

            return '';
        }
        add_shortcode( 'free_shipping_amount', 'get_wc_free_shipping_amount' );
    }

    if ( ! function_exists( 'show_thankyou_feedbacks' ) ) {
        /**
         * Display all customer feedback (saved in order meta) as stars and text.
         *
         * Fully compatible with WooCommerce HPOS and translatable.
         *
         * @return string HTML output of feedback list.
         */
        function show_thankyou_feedbacks() {
            if ( ! class_exists( 'WooCommerce' ) ) {
                return '';
            }

            // Query all orders that have the _thankyou_feedback meta key
            $orders = wc_get_orders( array(
                'limit'         => -1,
                'meta_key'      => '_thankyou_feedback',
                'meta_compare'  => 'EXISTS',
                'return'        => 'objects',
                'status'        => array( 'wc-completed', 'wc-processing' ),
            ) );

            if ( empty( $orders ) ) {
                return wpautop( esc_html__( 'No feedback yet.', 'borspirit' ) );
            }

            $output = '<div class="thankyou-feedback-list">';

            foreach ( $orders as $order ) {
                $feedback_data = $order->get_meta( '_thankyou_feedback' );

                // Skip if feedback is missing or malformed
                if ( empty( $feedback_data ) || ! is_array( $feedback_data ) ) {
                    continue;
                }

                $like     = $feedback_data['like'] ?? '';
                $rating   = intval($feedback_data['rating'] ?? 0);
                $feedback = $feedback_data['feedback'] ?? '';
                $date     = isset($feedback_data['date']) ? date_i18n(get_option('date_format'), strtotime($feedback_data['date'])) : '';

                // Get customer first name
                $customer_first_name = $order->get_billing_first_name();

                /*
                // Build stars with Dashicons
                $stars = '';
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $rating) {
                        $stars .= '<span class="dashicons dashicons-star-filled" style="color:#FFD700;"></span>';
                    } else {
                        $stars .= '<span class="dashicons dashicons-star-empty" style="color:#ccc;"></span>';
                    }
                }
                */

                // Build WooCommerce-style star rating
                $stars = '';
                if ($rating > 0) {
                    $stars = wc_get_rating_html($rating, 5); // second param = max stars
                }

                $output .= '<div class="thankyou-feedback">';
                if ( $customer_first_name ) {
                    $output .= '<p class="customer-name">' . $customer_first_name . '</p>';
                }
                if ( $stars ) {
                    $output .= '<div class="rating">' . $stars . '</div>';
                }
                if ( $like ) {
                    $output .= '<p><strong>' . esc_html__( 'Opinion:', 'borspirit' ) . '</strong> ' . esc_html( $like ) . '</p>';
                }
                if ( $feedback ) {
                    $output .= '<p><strong>' . esc_html__( 'Feedback:', 'borspirit' ) . '</strong> ' . esc_html( $feedback ) . '</p>';
                }
                if ( $date ) {
                    $output .= '<p class="date">' . sprintf(
                        /* translators: %s = feedback date */
                        esc_html__( 'Submitted on %s', 'borspirit' ),
                        esc_html( $date )
                    ) . '</p>';
                }
                $output .= '</div>';
            }

            $output .= '</div>';

            return $output;
        }
        add_shortcode( 'thankyou_feedbacks', 'show_thankyou_feedbacks' );
    }
