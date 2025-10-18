<?php
    defined( 'ABSPATH' ) || exit;
    
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
            if ( is_user_logged_in() ) {
                return '<p>' . esc_html__( 'You are already registered.', 'woocommerce' ) . '</p>';
            }

            ob_start();

            do_action( 'woocommerce_before_customer_login_form' );

            $html = wc_get_template_html( 'myaccount/form-login.php' );

            if ( empty( $html ) ) {
                return '<p>' . esc_html__( 'Registration form not available.', 'woocommerce' ) . '</p>';
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
                return '<p>' . esc_html__( 'Error loading registration form.', 'woocommerce' ) . '</p>';
            }

            $xpath = new DOMXPath( $dom );
            $form  = $xpath->query( '//form[contains(@class,"register")]' );
            $form  = $form->item( 0 );

            if ( $form ) {
                echo $dom->saveHTML( $form );
            } else {
                echo '<p>' . esc_html__( 'Registration form not found.', 'woocommerce' ) . '</p>';
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
            if ( is_user_logged_in() ) {
                return '<p>' . esc_html__( 'You are already logged in.', 'woocommerce' ) . '</p>';
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
                    return '<p><strong>' . esc_html( $key ) . ':</strong> ' . esc_html( $value ) . '</p>';
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
        function render_opening_hours_table( $opening_hours, $acf_mode = true, $text_domain = TEXT_DOMAIN ) {

            // Day labels with translation support
            $days = [
                'monday'    => __( 'Hétfő', $text_domain ),
                'tuesday'   => __( 'Kedd', $text_domain ),
                'wednesday' => __( 'Szerda', $text_domain ),
                'thursday'  => __( 'Csütörtök', $text_domain ),
                'friday'    => __( 'Péntek', $text_domain ),
                'saturday'  => __( 'Szombat', $text_domain ),
                'sunday'    => __( 'Vasárnap', $text_domain ),
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
            if ( ! function_exists( 'get_field' ) ) {
                return wpautop( esc_html__( 'ACF plugin is not active.', TEXT_DOMAIN ) );
            }

            $opening_hours = get_field( 'opening_hours', 'option' );

            if ( empty( $opening_hours ) ) {
                return wpautop( esc_html__( 'No opening hours specified.', TEXT_DOMAIN ) );
            }

            return render_opening_hours_table( $opening_hours, true, TEXT_DOMAIN );
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
            if ( ! class_exists( 'WooCommerce' ) ) {
                return '';
            }

            $customer_country = WC()->customer->get_shipping_country();

            // If country is not yet set, try geolocation
            if ( empty( $customer_country ) ) {
                $geo = WC_Geolocation::geolocate_ip();
                $customer_country = $geo['country'] ?? '';
            }

            if ( empty( $customer_country ) ) {
                return '';
            }

            // Prepare package for zone matching
            $package = [
                'destination' => [
                    'country'  => $customer_country,
                    'state'    => '',
                    'postcode' => '',
                    'city'     => '',
                    'address'  => '',
                ],
            ];

            $customer_zone = WC_Shipping_Zones::get_zone_matching_package( $package );
            $methods = $customer_zone->get_shipping_methods();

            foreach ( $methods as $method ) {
                if ( ! is_object( $method ) ) {
                    continue;
                }

                if ( $method->id === 'free_shipping' && $method->enabled === 'yes' ) {
                    if ( isset( $method->min_amount ) && is_numeric( $method->min_amount ) && $method->min_amount > 0 ) {
                        return wc_price( $method->min_amount ); // Just return the amount
                    }
                }
            }

            return ''; // No free shipping found
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

            // Query all orders that have the _thankyou_feedback meta key
            $orders = wc_get_orders( array(
                'limit'         => -1,
                'meta_key'      => '_thankyou_feedback',
                'meta_compare'  => 'EXISTS',
                'return'        => 'objects',
                'status'        => array( 'wc-completed', 'wc-processing' ),
            ) );

            if ( empty( $orders ) ) {
                return '<p>' . esc_html__( 'No feedback yet.', TEXT_DOMAIN ) . '</p>';
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
                    $output .= '<p><strong>' . esc_html__( 'Opinion:', TEXT_DOMAIN ) . '</strong> ' . esc_html( $like ) . '</p>';
                }
                if ( $feedback ) {
                    $output .= '<p><strong>' . esc_html__( 'Feedback:', TEXT_DOMAIN ) . '</strong> ' . esc_html( $feedback ) . '</p>';
                }
                if ( $date ) {
                    $output .= '<p class="date">' . sprintf(
                        /* translators: %s = feedback date */
                        esc_html__( 'Submitted on %s', TEXT_DOMAIN ),
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