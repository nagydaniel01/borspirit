<?php
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
         * Shortcode callback: Display WooCommerce general settings.
         *
         * Show all settings:
         * [woocommerce_settings]
         *
         * Show only the store city:
         * [woocommerce_settings setting="store_city"]
         *
         * Show only the currency:
         * [woocommerce_settings setting="currency"]
         * 
         * @param array $atts Shortcode attributes. 
         *                    - setting (string) Optional. Specific setting key to display.
         *
         * @return string HTML output of the settings or specific record.
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
         * Render Opening Hours Table (Accessible + Translatable)
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
         * Shortcode to display opening hours from ACF Options page
         *
         * Usage: [opening_hours]
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