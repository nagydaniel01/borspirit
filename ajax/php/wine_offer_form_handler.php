<?php
    if ( ! function_exists('bsp_quiz_handle_recommend') ) {
        /**
         * Handles AJAX quiz recommendation requests.
         */
        function bsp_quiz_handle_recommend() {
            try {
                // Ensure POST request
                if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
                    wp_send_json_error(['message' => __('Invalid request method.', 'bsp-wine-quiz')], 405);
                }

                // Check nonce
                if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'bsp_quiz_action') ) {
                    wp_send_json_error(['message' => __('Invalid security token.', 'bsp-wine-quiz')], 403);
                }

                // Sanitize inputs
                $answers = [
                    'q1' => isset($_POST['q1']) ? sanitize_text_field($_POST['q1']) : '',
                    'q2' => isset($_POST['q2']) ? sanitize_text_field($_POST['q2']) : '',
                    'q3' => isset($_POST['q3']) ? sanitize_text_field($_POST['q3']) : '',
                    'q4' => isset($_POST['q4']) ? sanitize_text_field($_POST['q4']) : '',
                ];

                // Compute recommendation using your existing logic
                $quiz = new BorSpirit_Wine_Quiz(); // make sure class is loaded
                $rule = $quiz->compute_recommendation($answers);

                // Prepare response
                if ( $rule['type'] === 'product' ) {
                    $product = wc_get_product($rule['value']);
                    if ( $product ) {
                        wp_send_json_success([
                            'type'  => 'product',
                            'id'    => $rule['value'],
                            'title' => $product->get_name(),
                            'url'   => get_permalink($rule['value']),
                            'price' => $product->get_price_html(),
                        ]);
                    }
                    wp_send_json_error(['message' => __('Recommended product not found. Check settings.', 'bsp-wine-quiz')]);
                } else {
                    $cat = get_term_by('slug', $rule['value'], 'product_cat');
                    if ( $cat ) {
                        wp_send_json_success([
                            'type' => 'category',
                            'slug' => $cat->slug,
                            'name' => $cat->name,
                            'url'  => get_term_link($cat),
                        ]);
                    }
                    // fallback if ID is used
                    if ( is_numeric($rule['value']) ) {
                        $catObj = get_term($rule['value'], 'product_cat');
                        if ( $catObj && ! is_wp_error($catObj) ) {
                            wp_send_json_success([
                                'type' => 'category',
                                'slug' => $catObj->slug,
                                'name' => $catObj->name,
                                'url'  => get_term_link($catObj),
                            ]);
                        }
                    }
                    wp_send_json_error(['message' => __('Recommended category not found. Check settings.', 'bsp-wine-quiz')]);
                }

            } catch ( Exception $e ) {
                wp_send_json_error(['message' => sprintf(__('Unexpected error: %s', 'bsp-wine-quiz'), $e->getMessage())], 500);
            }
        }

        add_action('wp_ajax_bsp_recommend', 'bsp_quiz_handle_recommend');
        add_action('wp_ajax_nopriv_bsp_recommend', 'bsp_quiz_handle_recommend');
    }

    if ( ! function_exists('bsp_quiz_handle_add_to_cart') ) {
        /**
         * Handles AJAX add-to-cart requests from the quiz.
         */
        function bsp_quiz_handle_add_to_cart() {
            try {
                // Ensure POST request
                if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
                    wp_send_json_error(['message' => __('Invalid request method.', 'bsp-wine-quiz')], 405);
                }

                // Check nonce
                if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'bsp_quiz_action') ) {
                    wp_send_json_error(['message' => __('Invalid security token.', 'bsp-wine-quiz')], 403);
                }

                // Sanitize product ID
                $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
                if ( ! $product_id ) {
                    wp_send_json_error(['message' => __('Invalid product.', 'bsp-wine-quiz')], 422);
                }

                // Add to WooCommerce cart
                if ( WC()->cart->add_to_cart($product_id) ) {
                    wp_send_json_success(['message' => __('Product added to cart.', 'bsp-wine-quiz')]);
                } else {
                    wp_send_json_error(['message' => __('Failed to add product to cart.', 'bsp-wine-quiz')], 500);
                }

            } catch ( Exception $e ) {
                wp_send_json_error(['message' => sprintf(__('Unexpected error: %s', 'bsp-wine-quiz'), $e->getMessage())], 500);
            }
        }

        add_action('wp_ajax_bsp_add_to_cart', 'bsp_quiz_handle_add_to_cart');
        add_action('wp_ajax_nopriv_bsp_add_to_cart', 'bsp_quiz_handle_add_to_cart');
    }
