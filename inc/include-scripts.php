<?php
    defined( 'ABSPATH' ) || exit;
    
    if ( ! function_exists( 'theme_scripts' ) ) {
        /**
         * Dequeues unnecessary styles and enqueues theme-specific CSS and JS assets.
         *
         * Also localizes script data for use in JavaScript (e.g., ajax URL, theme URL, translations).
         *
         * @return void
         */
        function theme_scripts() {
            // Enqueue theme CSS and JS
            wp_enqueue_style( 'borspirit' . '-theme', TEMPLATE_DIR_URI . '/assets/dist/css/styles.css', array(), ASSETS_VERSION );
            wp_enqueue_script( 'borspirit' . '-theme', TEMPLATE_DIR_URI . '/assets/dist/js/scripts.js', array( 'jquery' ), ASSETS_VERSION, true );

            // Localize script for use in JS
            wp_localize_script( 'borspirit' . '-theme', 'localize', array(
                'ajaxurl'      => admin_url( 'admin-ajax.php' ),
                'resturl'      => esc_url( rest_url( 'wp/v2/posts' ) ),
                'themeurl'     => TEMPLATE_DIR_URI,
                'siteurl'      => SITE_URL,
                'ag_min_age'      => get_option('ag_min_age', 18),
                'ag_cookie_days'  => get_option('ag_cookie_days', 30),
                'ag_redirect_url' => get_option('ag_redirect_url', 'https://www.google.com'),
                //'start_time'   => current_time( 'c' ),
                'translations' => array(
                    'read_more' => __( 'Show more', 'borspirit' ),
                    'read_less' => __( 'Show less', 'borspirit' ),
                ),
            ) );

            //wp_enqueue_style('dashicons');

            if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
                wp_enqueue_script( 'comment-reply' );
            }

            /*
            // Check if the current page uses a given template
            $target_templates = array(
                'templates/page-felhivasok.php',
                'templates/page-rendezvenyek.php',
                'templates/page-sidebar.php',
                'templates/page-hirek-es-esemenyek.php'
            );

            if ( is_home() || is_page_template( $target_templates ) ) {
                // Pass event post data to MomentJS
                $event_data = get_upcoming_events_data();
                if ( ! empty( $event_data ) ) {
                    wp_add_inline_script(
                        'theme',
                        'var MomentData = ' . wp_json_encode( $event_data ) . ';',
                        'after'
                    );
                }
            }
            */

            // Disable WooCommerce brands CSS (handle may vary depending on plugin/theme)
            wp_dequeue_style( 'brands-styles' );
            wp_deregister_style( 'brands-styles' );
        }
        add_action( 'wp_enqueue_scripts', 'theme_scripts', 100 );
    }

    if ( ! function_exists( 'recaptcha_scripts' ) ) {
        /**
         * Enqueue Google reCAPTCHA v3 script.
         *
         * @return void
         */
        function recaptcha_scripts() {
            $recaptcha_site_key = '6LcOnQ4sAAAAAF_Mcnr5Adg4xtHC4sP46nC8LKjn';
    
            wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . esc_attr( $recaptcha_site_key ), [], null, true );
        }
        add_action( 'wp_enqueue_scripts', 'recaptcha_scripts', 110 );
    }

    if ( ! function_exists( 'gtm_script' ) ) {
        /**
         * Enqueue Google Tag Manager script and output noscript fallback.
         *
         * Inline GTM JS goes in <head>, noscript iframe goes immediately after <body>.
         *
         * @return void
         */
        function gtm_script() {
            // Get GTM ID
            $gtm_id = get_field( 'gtm_id', 'option' ) ?: ( defined('GTM_ID') ? GTM_ID : null );

            if ( empty( $gtm_id ) ) {
                return; // Exit if no GTM ID is defined
            }

            // 1. Register a dummy script to attach inline JS in the <head>
            wp_register_script( 'google-tag-manager', false );
            wp_enqueue_script( 'google-tag-manager' );

            $gtm_js = <<<JS
    (function(w,d,s,l,i){
        w[l]=w[l]||[];
        w[l].push({'gtm.start': new Date().getTime(), event:'gtm.js'});
        var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),
            dl=l!='dataLayer'?'&l='+l:'';
        j.async=true;
        j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;
        f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{$gtm_id}');
    JS;

            wp_add_inline_script( 'google-tag-manager', $gtm_js );

            // 2. Output <noscript> immediately after <body> using wp_body_open (best practice)
            add_action( 'wp_body_open', function() use ( $gtm_id ) {
                echo "<!-- Google Tag Manager (noscript) -->\n";
                echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . esc_attr( $gtm_id ) . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n";
                echo "<!-- End Google Tag Manager (noscript) -->\n";
            } );
        }

        add_action( 'wp_enqueue_scripts', 'gtm_script', 120 );
    }

    if ( ! function_exists( 'fb_meta_pixel_script' ) ) {
        /**
         * Add Meta Pixel tracking code via wp_enqueue_scripts.
         *
         * This function registers a dummy script handle and attaches
         * the Meta Pixel initialization code as inline JavaScript.
         * It also outputs the <noscript> tracking image fallback.
         *
         * @return void
         */
        function fb_meta_pixel_script() {
            // Get Meta Pixel ID
            $fb_pixel_id = get_field( 'fb_pixel_id', 'option' ) ?: ( defined('FB_PIXEL_ID') ? FB_PIXEL_ID : null );

            if ( ! $fb_pixel_id ) {
                return; // Exit if no Meta Pixel ID
            }

            // Register an empty script to attach inline JavaScript to
            wp_register_script( 'meta-pixel', false );
            wp_enqueue_script( 'meta-pixel' );

            // Meta Pixel inline JavaScript
            $pixel_script = "
                !function(f,b,e,v,n,t,s){
                    if(f.fbq)return;n=f.fbq=function(){
                        n.callMethod ? n.callMethod.apply(n,arguments) : n.queue.push(arguments)
                    };
                    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                    n.queue=[];t=b.createElement(e);t.async=!0;
                    t.src=v;s=b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t,s)
                }(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');

                fbq('init', '{$fb_pixel_id}');
                fbq('track', 'PageView');
            ";

            wp_add_inline_script( 'meta-pixel', $pixel_script );

            // Add the noscript pixel
            echo '<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=' . esc_attr($fb_pixel_id) . '&ev=PageView&noscript=1" /></noscript>';
        }
        add_action( 'wp_enqueue_scripts', 'fb_meta_pixel_script', 120 );
    }

    if ( ! function_exists( 'custom_gtm_push' ) ) {
        /**
         * Helper function: Push to GTM dataLayer safely
         */
        function custom_gtm_push( $data ) {
            ?>
            <script>
            window.dataLayer = window.dataLayer || [];
            dataLayer.push(<?php echo wp_json_encode( $data ); ?>);
            </script>
            <?php
        }
    }

    if ( ! function_exists( 'custom_gtm_push_page_view' ) ) {
        /**
         * Push page_view event on all pages
         */
        function custom_gtm_push_page_view() {
            $page_data = [
                'event' => 'page_view',
                'page' => [
                    'url'   => esc_url(home_url(add_query_arg([], $GLOBALS["wp"]->request))),
                    'title' => esc_js(get_the_title() ?: wp_get_document_title()),
                    'path'  => esc_js($_SERVER["REQUEST_URI"])
                ]
            ];
            custom_gtm_push( $page_data );
        }
        add_action( 'wp_footer', 'custom_gtm_push_page_view' );
    }

    if ( ! function_exists( 'custom_gtm_push_view_cart' ) ) {
        /**
         * Push view_cart event on WooCommerce cart page
         */
        function custom_gtm_push_view_cart() {
            // Check if WooCommerce is installed
            if ( ! class_exists( 'WooCommerce' ) ) return;

            // Only run on cart page with cart initialized
            if ( ! is_cart() || ! WC()->cart ) return;

            $items = [];
            foreach ( WC()->cart->get_cart() as $cart_item ) {
                $product = $cart_item['data'];
                $items[] = [
                    'item_name'     => $product->get_name(),
                    'item_id'       => $product->get_id(),
                    'price'         => $product->get_price(),
                    'quantity'      => $cart_item['quantity'],
                    'item_category' => wc_get_product_category_list($product->get_id(), ',') ?: ''
                ];
            }

            if ( ! empty($items) ) {
                custom_gtm_push([
                    'event' => 'view_cart',
                    'ecommerce' => [
                        'items' => $items
                    ]
                ]);
            }
        }
        add_action( 'wp_footer', 'custom_gtm_push_view_cart' );
    }

    if ( ! function_exists( 'custom_gtm_push_product_view' ) ) {
        /**
         * Push product view (view_item) on single product pages
         */
        function custom_gtm_push_product_view() {
            if ( ! is_product() ) return;
            global $product;

            $items = [
                [
                    'item_name'     => $product->get_name(),
                    'item_id'       => $product->get_id(),
                    'price'         => $product->get_price(),
                    'item_brand'    => $product->get_attribute('brand') ?: '',
                    'item_category' => wc_get_product_category_list($product->get_id(), ',') ?: '',
                    //'item_variant'  => $product->get_attribute('pa_variant') ?: '',
                ]
            ];

            custom_gtm_push([
                'event' => 'view_item',
                'ecommerce' => ['items' => $items]
            ]);
        }
        add_action( 'woocommerce_before_single_product', 'custom_gtm_push_product_view' );
    }

    if ( ! function_exists( 'custom_gtm_push_add_to_cart' ) ) {
        /**
         * Add to cart event with dynamic quantity
         */
        function custom_gtm_push_add_to_cart() {
            if ( ! is_product() ) return;
            global $product;

            ?>
            <script>
            (function() {
                window.dataLayer = window.dataLayer || [];
                var form = document.querySelector('form.cart');
                if (!form) return;

                form.addEventListener('submit', function(e) {
                    var qtyField = this.querySelector('input[name="quantity"]');
                    var quantity = 1;
                    if (qtyField) {
                        quantity = parseInt(qtyField.value) || 1;
                    }

                    dataLayer.push({
                        'event': 'add_to_cart',
                        'ecommerce': {
                            'items': [{
                                'item_name': '<?php echo esc_js($product->get_name()); ?>',
                                'item_id': '<?php echo esc_js($product->get_id()); ?>',
                                'price': '<?php echo esc_js($product->get_price()); ?>',
                                'item_category': '<?php echo esc_js(wc_get_product_category_list($product->get_id(), ',')); ?>',
                                'quantity': quantity
                            }]
                        }
                    });
                });
            })();
            </script>
            <?php
        }
        add_action( 'woocommerce_after_add_to_cart_button', 'custom_gtm_push_add_to_cart' );
    }

    if ( ! function_exists( 'custom_gtm_remove_from_cart_script' ) ) {
        /**
         * Remove from cart (AJAX)
         */
        function custom_gtm_remove_from_cart_script() {
            // Check if WooCommerce is installed
            if ( ! class_exists( 'WooCommerce' ) ) return;
            ?>
            <script>
            window.dataLayer = window.dataLayer || [];
            jQuery(document.body).on('removed_from_cart', function(event, fragments, cart_hash, $button) {
                var item = $button.closest('.cart_item');
                if (!item.length) return;
                dataLayer.push({
                    'event': 'remove_from_cart',
                    'ecommerce': {
                        'items': [{
                            'item_name': item.data('product_name'),
                            'item_id': item.data('product_id'),
                            'price': item.data('product_price'),
                            'quantity': item.data('product_qty')
                        }]
                    }
                });
            });
            </script>
            <?php
        }
        add_action('wp_footer', 'custom_gtm_remove_from_cart_script');
    }

    if ( ! function_exists( 'custom_gtm_begin_checkout' ) ) {
        /**
         * Begin checkout event
         */
        function custom_gtm_begin_checkout() {
            if ( ! WC()->cart ) return;

            $items = [];
            foreach ( WC()->cart->get_cart() as $cart_item ) {
                $product = $cart_item['data'];
                $items[] = [
                    'item_name'     => $product->get_name(),
                    'item_id'       => $product->get_id(),
                    'price'         => $product->get_price(),
                    'quantity'      => $cart_item['quantity'],
                    'item_category' => wc_get_product_category_list($product->get_id(), ',') ?: ''
                ];
            }

            custom_gtm_push([
                'event' => 'begin_checkout',
                'ecommerce' => ['items' => $items]
            ]);
        }
        add_action( 'woocommerce_checkout_before_customer_details', 'custom_gtm_begin_checkout' );
    }

    if ( ! function_exists( 'custom_gtm_push_purchase' ) ) {
        /**
         * Purchase event with coupons
         */
        function custom_gtm_push_purchase( $order_id ) {
            $order = wc_get_order( $order_id );
            if ( ! $order ) return;

            $items = [];
            foreach ( $order->get_items() as $item ) {
                $product = $item->get_product();
                $items[] = [
                    'item_name'     => $product->get_name(),
                    'item_id'       => $product->get_id(),
                    'price'         => $product->get_price(),
                    'quantity'      => $item->get_quantity(),
                    'item_category' => wc_get_product_category_list($product->get_id(), ',') ?: ''
                ];
            }

            $coupons = $order->get_coupon_codes();

            custom_gtm_push([
                'event' => 'purchase',
                'ecommerce' => [
                    'transaction_id' => $order->get_id(),
                    'value'          => $order->get_total(),
                    'currency'       => $order->get_currency(),
                    'items'          => $items,
                    'coupon'         => $coupons
                ]
            ]);
        }
        add_action( 'woocommerce_thankyou', 'custom_gtm_push_purchase' );
    }
