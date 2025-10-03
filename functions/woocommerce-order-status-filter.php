<?php
    if ( ! function_exists( 'custom_my_account_orders_filter_by_status' ) ) {
        /**
         * Filter the My Account orders query by order status if provided in URL.
         *
         * Hooks into 'woocommerce_my_account_my_orders_query'.
         *
         * @param array $args Arguments for the WC_Order_Query.
         * @return array Modified arguments including status filter if present in URL.
         */
        function custom_my_account_orders_filter_by_status($args) {
            if (!empty($_GET['status'])) {
                $args['status'] = [sanitize_text_field($_GET['status'])];
            }
            return $args;
        }
        add_filter('woocommerce_my_account_my_orders_query', 'custom_my_account_orders_filter_by_status');
    }


    if ( ! function_exists( 'custom_my_account_orders_filters' ) ) {
        /**
         * Display list of order status filters above the orders table in My Account.
         *
         * Shows all order statuses that have orders for the current customer and highlights the currently selected filter.
         *
         * Hooks into 'woocommerce_before_account_orders'.
         */
        function custom_my_account_orders_filters() {
            echo '<p>' . __('Filter by:', TEXT_DOMAIN) . ' ';

            $customer_orders = 0;

            // Loop through all WooCommerce order statuses
            foreach (wc_get_order_statuses() as $slug => $name) {
                // Get number of orders for current user and current status
                $status_orders = count(wc_get_orders([
                    'status'   => $slug,
                    'customer' => get_current_user_id(),
                    'limit'    => -1
                ]));

                if ($status_orders > 0) {
                    $status_link = add_query_arg('status', $slug, wc_get_endpoint_url('orders'));

                    // Highlight the currently selected status
                    if (!empty($_GET['status']) && $_GET['status'] == $slug) {
                        echo '<b>' . esc_html($name) . ' (' . esc_html($status_orders) . ')</b><span class="delimit"> - </span>';
                    } else {
                        echo '<a href="' . esc_url($status_link) . '">' . esc_html($name) . ' (' . esc_html($status_orders) . ')</a><span class="delimit"> - </span>';
                    }
                }
                $customer_orders += $status_orders;
            }

            // Display "All statuses" link
            $all_status_link = remove_query_arg('status');
            if (!empty($_GET['status'])) {
                echo '<a href="' . esc_url($all_status_link) . '">' . __('All statuses', TEXT_DOMAIN) . ' (' . esc_html($customer_orders) . ')</a>';
            } else {
                echo '<b>' . __('All statuses', TEXT_DOMAIN) . ' (' . esc_html($customer_orders) . ')</b>';
            }

            echo '</p>';
        }
        add_action('woocommerce_before_account_orders', 'custom_my_account_orders_filters');
    }


    if ( ! function_exists( 'custom_my_account_orders_filter_by_status_pagination' ) ) {
        /**
         * Ensure My Account orders pagination preserves the status filter in the URL.
         *
         * Hooks into 'woocommerce_get_endpoint_url' and appends the current status parameter.
         *
         * @param string $url The endpoint URL.
         * @param string $endpoint Endpoint slug.
         * @param string $value Endpoint value.
         * @param bool $permalink Whether permalinks are enabled.
         * @return string Modified URL with 'status' query arg if filtering by status.
         */
        function custom_my_account_orders_filter_by_status_pagination($url, $endpoint, $value, $permalink) {
            if ('orders' === $endpoint && !empty($_GET['status'])) {
                return add_query_arg('status', sanitize_text_field($_GET['status']), $url);
            }
            return $url;
        }

        // Add filter for endpoint URLs to preserve status query on pagination
        add_action('woocommerce_before_account_orders', function() {
            add_filter('woocommerce_get_endpoint_url', 'custom_my_account_orders_filter_by_status_pagination', 9999, 4);
        });
    }
