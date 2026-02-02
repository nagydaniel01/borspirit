<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    
    /**
     * WooCommerce Customizations
     * -----------------------------------------------------
     * Custom wrappers, layouts, fragments, and template tweaks.
     * -----------------------------------------------------
     */

    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    // ============================================================
    // General Setup
    // ============================================================

    // Disable default WooCommerce styles
    add_filter('woocommerce_enqueue_styles', '__return_empty_array');

    // Remove default WooCommerce wrappers
    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

    // Remove WooCommerce breadcrumb
    //remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

    // Remove WooCommerce sidebar
    //remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

    if ( ! function_exists( 'custom_remove_woocommerce_sidebar' ) ) {
        /**
         * Remove WooCommerce sidebar on:
         * - Single product pages
         * - Empty product category pages
         * - Empty product tag pages
         */
        function custom_remove_woocommerce_sidebar() {
            // 1. Remove sidebar on single product pages
            if ( is_product() ) {
                remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
                return; // no need to continue
            }

            // 2. Handle product categories & product tags
            if ( is_product_category() || is_product_tag() ) {

                $term = get_queried_object();

                // Safety check
                if ( $term && isset( $term->term_id ) && isset( $term->taxonomy ) ) {

                    // Query to check if this term has products
                    $args = array(
                        'post_type'      => 'product',
                        'posts_per_page' => 1,
                        'tax_query'      => array(
                            array(
                                'taxonomy'         => $term->taxonomy,
                                'field'            => 'term_id',
                                'terms'            => $term->term_id,
                                'include_children' => false,
                            ),
                        ),
                    );

                    $query = new WP_Query( $args );

                    // If no products → remove sidebar
                    if ( ! $query->have_posts() ) {
                        remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
                    }

                    wp_reset_postdata();
                }
            }
        }
        add_action( 'template_redirect', 'custom_remove_woocommerce_sidebar' );
    }

    // ============================================================
    // 5. BREADCRUMBS
    // ============================================================

    if ( ! function_exists( 'custom_breadcrumb_wrapper_start' ) ) {
        /**
         * Output the opening wrapper for WooCommerce breadcrumbs
         *
         * @return void
         */
        function custom_breadcrumb_wrapper_start() {
            if (is_shop() || is_product_category() ) {
                //echo '<div class="woocommerce-breadcrumb-wrapper">';
            } elseif (is_singular('product') ) {
                echo '<div class="woocommerce-breadcrumb-wrapper"><div class="container">';
            }
        }
        add_action( 'woocommerce_before_main_content', 'custom_breadcrumb_wrapper_start', 15 );
    }

    if ( ! function_exists( 'custom_breadcrumb_wrapper_end' ) ) {
        /**
         * Output the closing wrapper for WooCommerce breadcrumbs
         *
         * @return void
         */
        function custom_breadcrumb_wrapper_end() {
            if (is_shop() || is_product_category() ) {
                //echo '</div>';
            } elseif (is_singular('product') ) {
                echo '</div></div>';
            }
        }
        add_action( 'woocommerce_before_main_content', 'custom_breadcrumb_wrapper_end', 20 );
    }

    // ============================================================
    // 6. NOTICES & TOOLS WRAPPERS
    // ============================================================

    if ( ! function_exists( 'custom_woocommerce_notices_wrapper' ) ) {
        /**
         * Output opening wrapper for WooCommerce notices
         *
         * @return void
         */
        function custom_woocommerce_notices_wrapper() {
            echo '<div class="container">';
        }
        //add_action( 'woocommerce_before_shop_loop', 'custom_woocommerce_notices_wrapper', 5 );
        add_action( 'woocommerce_before_single_product', 'custom_woocommerce_notices_wrapper', 5 );
    }

    if ( ! function_exists( 'custom_woocommerce_notices_wrapper_end' ) ) {
        /**
         * Output closing wrapper for WooCommerce notices
         *
         * @return void
         */
        function custom_woocommerce_notices_wrapper_end() {
            echo '</div>';
        }
        //add_action( 'woocommerce_before_shop_loop', 'custom_woocommerce_notices_wrapper_end', 15 );
        add_action( 'woocommerce_before_single_product', 'custom_woocommerce_notices_wrapper_end', 15 );
    }

    add_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 ); // Default notices
    add_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 ); // Default notices

    if ( ! function_exists( 'custom_woocommerce_catalog_ordering_wrapper' ) ) {
        /**
         * Output opening wrapper for WooCommerce result count and ordering dropdown
         *
         * @return void
         */
        function custom_woocommerce_catalog_ordering_wrapper() {
            echo '<div class="woocommerce-tools">';
        }
        add_action( 'woocommerce_before_shop_loop', 'custom_woocommerce_catalog_ordering_wrapper', 10 );
    }

    if ( ! function_exists( 'custom_woocommerce_catalog_ordering_wrapper_end' ) ) {
        /**
         * Output closing wrapper for WooCommerce result count and ordering dropdown
         *
         * @return void
         */
        function custom_woocommerce_catalog_ordering_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_before_shop_loop', 'custom_woocommerce_catalog_ordering_wrapper_end', 35 );
    }

    if ( ! function_exists( 'custom_output_child_categories_after_sorting' ) ) {
        /**
         * Echo child categories for current WooCommerce category
         * using card-term-{taxonomy}.php template handling
         */
        function custom_output_child_categories_after_sorting() {

            if ( ! is_product_category() ) {
                return;
            }

            // Get current category
            $current_cat = get_queried_object();
            if ( ! $current_cat ) {
                return;
            }

            // Get child categories
            $child_terms = get_terms( array(
                'taxonomy'   => 'product_cat',
                'parent'     => $current_cat->term_id,
                'hide_empty' => false,
            ) );

            if ( empty( $child_terms ) ) {
                return;
            }

            ?>
            <div class="slider slider--term-query">
                <div class="slider__list">
                    <?php foreach ( $child_terms as $term ) : ?>
                        <div class="slider__item">
                            <?php
                                $template_args = [
                                    'taxonomy' => esc_attr( $term->taxonomy ),
                                    'term'     => $term,
                                ];

                                $template_slug = 'template-parts/cards/card-term-' . $template_args['taxonomy'] . '.php';

                                if ( locate_template( $template_slug ) ) {
                                    get_template_part( 'template-parts/cards/card-term', $template_args['taxonomy'], $template_args );
                                } else {
                                    get_template_part( 'template-parts/cards/card-term', 'default', $template_args );
                                }
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="slider__controls"></div>
            </div>
            <?php
        }
        //add_action( 'woocommerce_before_shop_loop', 'custom_output_child_categories_after_sorting', 40 );
    }

    // ============================================================
    // Layout Wrappers
    // ============================================================

    if ( ! function_exists( 'custom_woocommerce_output_content_wrapper' ) ) {
        /**
         * Output the opening wrapper for WooCommerce content.
         */
        function custom_woocommerce_output_content_wrapper() {
            if (is_shop() || is_product_category() || is_tax() ) {
                echo '<main class="page page--default page--archive page--archive-product"><section class="section section--default"><div class="container">';
            } elseif (is_singular('product') ) {
                echo '<main class="page page--default page--single page--single-product"><section class="section section--single section--single-product">';
            }
        }
        add_action( 'woocommerce_before_main_content', 'custom_woocommerce_output_content_wrapper', 10 );
    }

    if ( ! function_exists( 'custom_woocommerce_output_content_wrapper_end' ) ) {
        /**
         * Output the closing wrapper for WooCommerce content.
         */
        function custom_woocommerce_output_content_wrapper_end() {
            if (is_shop() || is_product_category() || is_tax() ) {
                echo '</div></section></main>';
            } elseif (is_singular('product') ) {
                echo '</section></main>';
            }
        }
        add_action( 'woocommerce_after_main_content', 'custom_woocommerce_output_content_wrapper_end', 10 );
    }

    // ============================================================
    // 3. SHOP PAGE STRUCTURE
    // ============================================================

    // Move WooCommerce notices before the layout
    //remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );

    if ( ! function_exists( 'custom_move_notices_before_shop_layout' ) ) {
        /**
         * Output WooCommerce notices before the shop layout wrapper.
         *
         * @hooked woocommerce_before_shop_loop - 1
         */
        function custom_move_notices_before_shop_layout() {
            // Display notices manually before the layout
            echo '<div class="woocommerce-notices-wrapper">';
            wc_print_notices();
            echo '</div>';
        }
        //add_action( 'woocommerce_before_shop_loop', 'custom_move_notices_before_shop_layout', 1 );
    }

    if ( ! function_exists( 'custom_shop_layout_open' ) ) {
        /**
         * Open layout before products loop.
         *
         * Uses Bootstrap flex utilities + column widths.
         * Sidebar will appear on the left (desktop) and stack under products on mobile.
         *
         * @hooked woocommerce_before_shop_loop - 5
         */
        function custom_shop_layout_open() {
            echo '<div class="shop-layout row flex-lg-row flex-column-reverse">';

            // Products (right on desktop, above on mobile)
            echo '<div class="shop-layout__content col-lg-9 col-md-8 order-0 order-lg-1">';
        }
        //add_action( 'woocommerce_before_shop_loop', 'custom_shop_layout_open', 5 );
        add_action( 'woocommerce_before_shop_loop', 'custom_shop_layout_open', 40 );
    }

    if ( ! function_exists( 'custom_shop_layout_close' ) ) {
        /**
         * Close layout after products loop.
         *
         * Closes the flex container opened in custom_shop_layout_open().
         *
         * @hooked woocommerce_after_shop_loop - 50
         */
        function custom_shop_layout_close() {
            echo '</div>'; // Close .shop-layout__content

            // Sidebar (left on desktop, below on mobile)
            echo '<aside class="shop-layout__sidebar col-lg-3 col-md-4 order-1 order-lg-0">';
            do_action( 'woocommerce_sidebar' ); // Loads WooCommerce sidebar
            echo '</aside>';

            echo '</div>'; // Close .shop-layout
        }
        add_action( 'woocommerce_after_shop_loop', 'custom_shop_layout_close', 50 );
    }

    // ============================================================
    // 4. SINGLE PRODUCT WRAPPERS
    // ============================================================

    if ( ! function_exists( 'custom_woocommerce_single_product_main_wrapper' ) ) {
        /**
         * Wraps the single product main content in a custom section and container.
         */
        function custom_woocommerce_single_product_main_wrapper() {
            echo '<div class="section section--product-main"><div class="container"><div class="section__inner">';
        }
        add_action( 'woocommerce_before_single_product_summary', 'custom_woocommerce_single_product_main_wrapper', 5 );
    }

    if ( ! function_exists( 'custom_woocommerce_single_product_main_wrapper_end' ) ) {
        /**
         * Closes the custom section wrapper added around the single product main content.
         */
        function custom_woocommerce_single_product_main_wrapper_end() {
            echo '</div></div></div>';
        }
        add_action( 'woocommerce_after_single_product_summary', 'custom_woocommerce_single_product_main_wrapper_end', 5 );
    }

    // ============================================================
    // 7. PRODUCT LOOP STRUCTURE
    // ============================================================

    // Product crad wrapper
    if ( ! function_exists( 'custom_product_wrapper' ) ) {
        /**
         * Open a wrapper around each WooCommerce product in the loop
         */
        function custom_product_wrapper() {
            echo '<div class="woocommerce-loop-product">';

            // Bookmark button
            if ( ! is_user_logged_in() ) {
                ?>
                <a class="woocommerce-loop-product__bookmark" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">
                    <svg class="icon icon-bookmark-empty">
                        <use xlink:href="#icon-bookmark-empty"></use>
                    </svg>
                    <span class="visually-hidden"><?php echo esc_html__( 'Add to bookmarks', 'borspirit' ); ?></span>
                </a>
                <?php
            } else {
                $current_user_id = get_current_user_id();
                $post_id         = get_the_ID();
                $bookmark_ids    = get_field( 'user_bookmarks', 'user_' . $current_user_id ) ?: [];
                $is_bookmarked   = in_array( $post_id, $bookmark_ids, true );
                $bookmark_icon   = $is_bookmarked ? 'bookmark' : 'bookmark-empty';
                $bookmark_text   = $is_bookmarked ? __( 'Remove from bookmarks', 'borspirit' ) : __( 'Add to bookmarks', 'borspirit' );
                ?>
                <a id="btn-bookmark" class="woocommerce-loop-product__bookmark" href="#" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-bookmarked="<?php echo esc_attr( $is_bookmarked ? 'true' : 'false' ); ?>">
                    <svg class="icon icon-<?php echo esc_attr( $bookmark_icon ); ?>">
                        <use xlink:href="#icon-<?php echo esc_attr( $bookmark_icon ); ?>"></use>
                    </svg>
                    <span class="visually-hidden"><?php echo esc_html( $bookmark_text ); ?></span>
                </a>
                <?php
            }
        }
        add_action( 'woocommerce_before_shop_loop_item', 'custom_product_wrapper', 5 );
    }

    if ( ! function_exists( 'custom_product_wrapper_end' ) ) {
        /**
         * Close the wrapper around each WooCommerce product in the loop
         */
        function custom_product_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_after_shop_loop_item', 'custom_product_wrapper_end', 20 );
    }

    // Body wrapper
    if ( ! function_exists( 'custom_woocommerce_loop_body_wrapper' ) ) {
        /**
         * Open a wrapper around the WooCommerce product body.
         */
        function custom_woocommerce_loop_body_wrapper() {
            echo '<div class="woocommerce-loop-product__body">';
        }
        add_action( 'woocommerce_shop_loop_item_title', 'custom_woocommerce_loop_body_wrapper', 1 );
    }

    if ( ! function_exists( 'custom_woocommerce_loop_body_wrapper_end' ) ) {
        /**
         * Close the wrapper around the WooCommerce product body.
         */
        function custom_woocommerce_loop_body_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_after_shop_loop_item', 'custom_woocommerce_loop_body_wrapper_end', 1 );
    }

    // Image wrapper
    if ( ! function_exists( 'custom_woocommerce_loop_image_wrapper' ) ) {
        /**
         * Open a wrapper around the WooCommerce product image.
         */
        function custom_woocommerce_loop_image_wrapper() {
            /*
            // List of possible random classes
            $classes = array( 
                'woocommerce-loop-product__image--style-01', 
                'woocommerce-loop-product__image--style-02', 
                'woocommerce-loop-product__image--style-03', 
                'woocommerce-loop-product__image--style-04',
                'woocommerce-loop-product__image--style-05',
                'woocommerce-loop-product__image--style-06',
                'woocommerce-loop-product__image--style-07',
                'woocommerce-loop-product__image--style-08',
                'woocommerce-loop-product__image--style-09',
                'woocommerce-loop-product__image--style-10'
            );

            // Pick one at random
            $random_class = $classes[ array_rand( $classes ) ];
            */

            echo '<div class="woocommerce-loop-product__image">';
        }
        add_action( 'woocommerce_before_shop_loop_item_title', 'custom_woocommerce_loop_image_wrapper', 1 );
    }

    if ( ! function_exists( 'custom_woocommerce_loop_image_wrapper_end' ) ) {
        /**
         * Close the wrapper around the WooCommerce product image.
         */
        function custom_woocommerce_loop_image_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_before_shop_loop_item_title', 'custom_woocommerce_loop_image_wrapper_end', 20 );
    }

    // Rating wrapper
    if ( ! function_exists( 'custom_woocommerce_loop_rating_wrapper' ) ) {
        /**
         * Outputs opening wrapper div for rating in WooCommerce product loop, only if product has a rating.
         */
        function custom_woocommerce_loop_rating_wrapper() {
            global $product;

            if ( ! $product ) {
                return;
            }

            // Check if product has rating
            if ( $product->get_average_rating() > 0 ) {
                echo '<div class="woocommerce-loop-product__rating-wrapper">';
            }
        }
        add_action( 'woocommerce_after_shop_loop_item_title', 'custom_woocommerce_loop_rating_wrapper', 4 );
    }

    if ( ! function_exists( 'custom_woocommerce_loop_rating_wrapper_end' ) ) {
        /**
         * Outputs closing wrapper div for rating in WooCommerce product loop, only if product has a rating.
         */
        function custom_woocommerce_loop_rating_wrapper_end() {
            global $product;

            if ( ! $product ) {
                return;
            }

            // Check if product has rating
            if ( $product->get_average_rating() > 0 ) {
                echo '</div>';
            }
        }
        add_action( 'woocommerce_after_shop_loop_item_title', 'custom_woocommerce_loop_rating_wrapper_end', 6 );
    }

    // Add to cart wrapper
    if ( ! function_exists( 'custom_woocommerce_loop_add_to_cart_wrapper' ) ) {
        /**
         * Open a wrapper around the WooCommerce add-to-cart button.
         */
        function custom_woocommerce_loop_add_to_cart_wrapper() {
            echo '<div class="woocommerce-loop-product__add-to-cart-wrapper">';
        }
        add_action( 'woocommerce_after_shop_loop_item', 'custom_woocommerce_loop_add_to_cart_wrapper', 9 );
    }

    if ( ! function_exists( 'custom_woocommerce_loop_add_to_cart_wrapper_end' ) ) {
        /**
         * Close the wrapper around the WooCommerce add-to-cart button.
         */
        function custom_woocommerce_loop_add_to_cart_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_after_shop_loop_item', 'custom_woocommerce_loop_add_to_cart_wrapper_end', 11 );
    }

    // ============================================================
    // 8. SINGLE PRODUCT CONTENT
    // ============================================================

    if ( ! function_exists( 'move_short_description_under_title' ) ) {
        /**
         * Move the WooCommerce short description to appear directly under the product title.
         *
         * By default, WooCommerce displays the short description at priority 20 in the single product summary.
         * This function removes it from the default position and re-adds it immediately after the title (priority 6).
         *
         * Hook this function to 'woocommerce_single_product_summary'.
         *
         * @return void
         */
        function move_short_description_under_title() {
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
            add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 6);
        }
        add_action('woocommerce_single_product_summary', 'move_short_description_under_title', 1);
    }

    if ( ! function_exists( 'custom_woocommerce_single_product_gallery_wrapper' ) ) {
        /**
         * Output opening wrapper for the product gallery
         *
         * @return void
         */
        function custom_woocommerce_single_product_gallery_wrapper() {
            echo '<div class="gallery entry-gallery">';
        }
        add_action( 'woocommerce_before_single_product_summary', 'custom_woocommerce_single_product_gallery_wrapper', 5 );
    }

    if ( ! function_exists( 'custom_woocommerce_single_product_gallery_wrapper_end' ) ) {
        /**
         * Output closing wrapper for the product gallery
         *
         * @return void
         */
        function custom_woocommerce_single_product_gallery_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_before_single_product_summary', 'custom_woocommerce_single_product_gallery_wrapper_end', 20 );
    }

    if ( ! function_exists( 'custom_woocommerce_move_product_tabs' ) ) {
        /**
         * Setup function for moving WooCommerce product tabs.
         *
         * Removes the default tabs output and re-adds them wrapped in a container.
         *
         * @return void
         */
        function custom_woocommerce_move_product_tabs() {
            if ( function_exists( 'woocommerce_output_product_data_tabs' ) ) {
                remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
                add_action( 'woocommerce_after_single_product_summary', 'custom_woocommerce_move_product_tabs_inner', 10 );
            }
        }

        if ( ! function_exists( 'custom_woocommerce_move_product_tabs_inner' ) ) {
            /**
             * Outputs WooCommerce product tabs inside a Bootstrap container.
             *
             * @return void
             */
            function custom_woocommerce_move_product_tabs_inner() {
                echo '<section class="section section--product-tabs"><div class="container">';
                woocommerce_output_product_data_tabs();
                echo '</div></section>';
            }
        }
        //add_action( 'after_setup_theme', 'custom_woocommerce_move_product_tabs', 15 );
    }

    // ============================================================
    // 9. RELATED, UPSELLS
    // ============================================================

    if ( ! function_exists( 'custom_woocommerce_move_upsells' ) ) {
        /**
         * Setup function for moving WooCommerce upsell products.
         *
         * Removes the default upsells output and re-adds them wrapped
         * in a Bootstrap container.
         *
         * @return void
         */
        function custom_woocommerce_move_upsells() {
            if ( function_exists( 'woocommerce_upsell_display' ) ) {
                remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
                add_action( 'woocommerce_after_single_product_summary', 'custom_woocommerce_move_upsells_inner', 15 );
            }
        }

        if ( ! function_exists( 'custom_woocommerce_move_upsells_inner' ) ) {
            /**
             * Outputs WooCommerce upsell products inside a Bootstrap container.
             *
             * @return void
             */
            function custom_woocommerce_move_upsells_inner() {
                global $product;

                if ( ! $product ) {
                    return;
                }

                $upsells = $product->get_upsell_ids();

                if ( !empty( $upsells ) ) {
                    echo '<div class="section section--upsells" id="upsells"><div class="container">';
                    woocommerce_upsell_display();
                    echo '</div></div>';
                }
            }
        }
        add_action( 'after_setup_theme', 'custom_woocommerce_move_upsells', 15 );
    }

    if ( ! function_exists( 'custom_woocommerce_move_related_products' ) ) {
        /**
         * Setup function for moving WooCommerce related products.
         *
         * Removes the default related products output and re-adds them wrapped
         * in a Bootstrap container.
         *
         * @return void
         */
        function custom_woocommerce_move_related_products() {
            if ( function_exists( 'woocommerce_output_related_products' ) ) {
                remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
                add_action('woocommerce_after_single_product_summary', 'custom_woocommerce_move_related_products_inner', 20);
            }
        }

        if ( ! function_exists( 'custom_woocommerce_move_related_products_inner' ) ) {
            /**
             * Outputs WooCommerce related products inside a Bootstrap container.
             *
             * @return void
             */
            function custom_woocommerce_move_related_products_inner() {
                // Capture the output of WooCommerce function
                ob_start();
                woocommerce_output_related_products();
                $content = trim(ob_get_clean());

                // Only render wrapper if WooCommerce actually produced HTML
                if ( ! empty( $content ) ) {
                    echo '<div class="section section--related-products" id="related-products"><div class="container">';
                    echo $content;
                    echo '</div></div>';
                }
            }
        }
        add_action( 'after_setup_theme', 'custom_woocommerce_move_related_products', 15 );
    }

    // ============================================================
    // 10. PRODUCT REVIEWS
    // ============================================================

    if ( ! function_exists( 'woocommerce_product_review_callback' ) ) {
        /**
         * Modify the WooCommerce product review list arguments.
         *
         * @param array $args The default WooCommerce review list arguments.
         * @return array Modified review list arguments with a custom callback.
         */
        function woocommerce_product_review_callback( $args ) {
            $args['style'] = 'div';
            $args['callback'] = 'custom_woocommerce_comments';
            return $args;
        }
        add_filter( 'woocommerce_product_review_list_args', 'woocommerce_product_review_callback' );
    }

    if ( ! function_exists( 'custom_woocommerce_comments' ) ) {
        /**
         * Custom WooCommerce comment (review) callback.
         *
         * Outputs the HTML structure for each product review item.
         *
         * @param WP_Comment $comment The comment object.
         * @param array      $args    Comment display arguments.
         * @param int        $depth   Depth of the comment in the comment thread.
         * @return void
         */
        function custom_woocommerce_comments($comment, $args, $depth) {
            if ( 'div' === $args['style'] ) {
                $tag       = 'div';
                $add_below = 'comment';
            } else {
                $tag       = 'li';
                $add_below = 'div-comment';
            }?>
            <<?php echo $tag; ?> <?php comment_class( array_merge( empty( $args['has_children'] ) ? [] : ['parent'], ['card card--review'] ) ); ?> id="comment-<?php comment_ID() ?>"><?php 
            if ( 'div' != $args['style'] ) { ?>
                <div id="div-comment-<?php comment_ID() ?>" class="comment-body"><?php
            } ?>
            <div class="card__content">
                <div class="card__header"><?php 
                    /*
                    if ( $args['avatar_size'] != 0 ) {
                        echo get_avatar( $comment, $args['avatar_size'] ); 
                    } 
                    */
                    printf( __( '<h3 class="card__title"><cite class="fn">%s</cite><span class="says">%s: </span></h3>' ), get_comment_author(), __( ' says', 'woocommerce' ) ); ?>
                </div><?php 
                if ( $rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) ) ) {
                    echo '<div class="card__rating">';
                    echo wc_get_rating_html( $rating ); // WooCommerce function for star HTML
                    echo '</div>';
                }
                if ( $comment->comment_approved == '0' ) { ?>
                    <em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></em><br/><?php 
                } ?>

                <div class="card__lead"><?php comment_text(); ?></div>

                <!--
                <div class="card__meta comment-meta commentmetadata">
                    <a href="<?php //echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>"><?php
                        /* translators: 1: date, 2: time */
                        /*printf( 
                            __('%1$s at %2$s'), 
                            get_comment_date(),  
                            get_comment_time() 
                        );*/ ?>
                    </a><?php 
                    //edit_comment_link( __( '(Edit)' ), '  ', '' ); ?>
                </div>
                -->

                <div class="reply">
                    <?php 
                        comment_reply_link( 
                            array_merge( 
                                $args, 
                                array( 
                                    'add_below' => $add_below, 
                                    'depth'     => $depth, 
                                    'max_depth' => $args['max_depth'] 
                                ) 
                            ) 
                        ); ?>
                </div>
            </div>
            <?php if ( 'div' != $args['style'] ) : ?>
                </div><?php 
            endif;
        }

        // Remove comment reply link
        add_filter('comment_reply_link', '__return_empty_string');
    }

    // ============================================================
    // 11. PRODUCT SECTIONS (ACF)
    // ============================================================

    if ( ! function_exists('custom_woocommerce_single_product_sections') ) {
        /**
         * Displays flexible content sections on the single product page.
         * Checks for ACF 'sections' field for the current product,
         * then falls back to 'product_page_sections_section' options field.
         */
        function custom_woocommerce_single_product_sections() {
            try {
                $sections = [];
                $page_id  = get_the_ID();

                if ( empty($page_id) || !is_numeric($page_id) ) {
                    throw new Exception( __('The page ID is missing or invalid.', 'borspirit') );
                }

                // Define the base directory for template section files
                $template_dir = trailingslashit(get_template_directory()) . 'template-parts/sections/';
                if ( ! is_dir($template_dir) ) {
                    throw new Exception( sprintf( __('The required template directory does not exist: %s.', 'borspirit'), $template_dir ) );
                }

                // Check for ACF
                if ( ! function_exists('get_field') ) {
                    throw new Exception( __('The Advanced Custom Fields plugin is not activated. Please install or activate ACF to use sections.', 'borspirit') );
                }

                // First try product-specific sections
                $sections = get_field('sections', $page_id);

                // Fallback: if the current product has no custom sections,
                // load the default sections from the 'product_page' ACF field on the Options Page.
                // 'product_page' is the main field (Clone), 
                // and 'sections' is the subfield containing the section layouts.
                // This ensures consistent retrieval of default product page sections.
                if ( empty($sections) || !is_array($sections) ) {
                    $sections = get_field('product_page_sections', 'option'); // Field naming convention: product_page_<field_name>
                }

                // Process sections
                if ( ! empty($sections) && is_array($sections) ) {
                    $section_num = 0;

                    foreach ( $sections as $index => $section ) {
                        $section_num++;

                        if ( ! is_array($section) || empty($section['acf_fc_layout']) ) {
                            printf(
                                '<div class="alert alert-warning" role="alert">%s</div>',
                                esc_html( sprintf( __('Section #%d is incorrectly formatted and cannot be displayed.', 'borspirit'), $section_num ) )
                            );
                            continue;
                        }

                        $section_name = sanitize_file_name($section['acf_fc_layout']);
                        $section_file = $template_dir . 'section-' . $section_name . '.php';

                        if ( file_exists($section_file) ) {
                            require $section_file;
                        } else {
                            printf(
                                '<div class="alert alert-danger" role="alert">%s</div>',
                                sprintf(
                                    __('The template for <code>%s</code> section is missing. Please create the file: <code>%s</code>', 'borspirit'),
                                    esc_html( $section_name ),
                                    esc_html( $section_file )
                                )
                            );
                        }
                    }
                }

            } catch ( Exception $e ) {
                printf(
                    '<div class="alert alert-danger" role="alert">%s</div>',
                    esc_html( $e->getMessage() )
                );
            }
        }
        add_action( 'woocommerce_after_single_product_summary', 'custom_woocommerce_single_product_sections', 30 );
    }

    // ============================================================
    // 12. CART & MINI CART
    // ============================================================
    
    if ( ! function_exists( 'refresh_offcanvas_minicart_fragments' ) ) {
        /**
         * Refresh minicart and cart count via AJAX fragments.
         *
         * This function ensures that both the minicart contents and the cart item count
         * are refreshed dynamically after products are added to the cart via AJAX.
         *
         * @param array $fragments An array of HTML fragments to refresh with AJAX.
         * @return array Modified fragments array including cart count and minicart wrapper.
         */
        function refresh_offcanvas_minicart_fragments( $fragments ) {

            // Cart count fragment
            ob_start();
            ?>
            <span class="cart_contents_count">
                <?php echo WC()->cart->get_cart_contents_count(); ?>
            </span>
            <?php
            $fragments['.cart_contents_count'] = ob_get_clean();

            // Minicart wrapper fragment
            ob_start();
            ?>
            <div class="woocommerce-mini-cart__wrapper">
                <?php woocommerce_mini_cart(); ?>
            </div>
            <?php
            $fragments['.woocommerce-mini-cart__wrapper'] = ob_get_clean();

            return $fragments;
        }
        add_filter( 'woocommerce_add_to_cart_fragments', 'refresh_offcanvas_minicart_fragments' );
    }

    if ( ! function_exists( 'custom_cart_item_remove_link' ) ) {
        /**
         * Override WooCommerce cart item remove link with custom attributes and SVG icon.
         *
         * @param string $link          Original remove link HTML.
         * @param string $cart_item_key The cart item key.
         * @return string               Modified remove link HTML.
         */
        function custom_cart_item_remove_link( $link, $cart_item_key ) {
            $cart_item = WC()->cart->get_cart()[$cart_item_key];
            $product   = $cart_item['data'];
            $product_id   = $product->get_id();
            $product_name = $product->get_name();
            $product_sku  = $product->get_sku();

            // Custom SVG icon
            $svg_icon = '<svg class="icon icon-trash-can"><use xlink:href="#icon-trash-can"></use></svg>';

            $new_link = sprintf(
                '<a role="button" href="%s" class="remove remove_from_cart_button custom-remove" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" data-success_message="%s">%s<span class="visually-hidden">%s</span></a>',
                esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
                esc_attr( $product_id ),
                esc_attr( $cart_item_key ),
                esc_attr( $product_sku ),
                esc_attr( sprintf( __( '&ldquo;%s&rdquo; has been removed from your cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
                $svg_icon,
                esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) )
            );

            return $new_link;
        }
        add_filter( 'woocommerce_cart_item_remove_link', 'custom_cart_item_remove_link', 10, 2 );
    }

    // ============================================================
    // 13. PAGINATION ICONS
    // ============================================================

    if ( ! function_exists( 'custom_woocommerce_pagination_icons' ) ) {
        /**
         * Replaces WooCommerce pagination arrows with custom SVG icons.
         *
         * @param array $args Pagination arguments.
         * @return array Modified pagination arguments.
         */
        function custom_woocommerce_pagination_icons($args) {
            $args['prev_text'] = '<svg class="icon icon-chevron-left"><use xlink:href="#icon-chevron-left"></use></svg>';
            $args['next_text'] = '<svg class="icon icon-chevron-right"><use xlink:href="#icon-chevron-right"></use></svg>';
            
            return $args;
        }
        add_filter( 'woocommerce_pagination_args', 'custom_woocommerce_pagination_icons' );
        add_filter( 'woocommerce_comment_pagination_args', 'custom_woocommerce_pagination_icons' );
    }

    // ============================================================
    // 14. DAILY ORDER SUMMARY EMAIL
    // ============================================================

    if ( ! function_exists( 'wc_send_order_summary' ) ) {
        /**
         * Trigger function to manually test the WooCommerce summary email.
         * Use ?wc_send_order_summary=1 in the URL.
         */
        function wc_send_order_summary() {
            if ( isset( $_GET['wc_send_order_summary'] ) ) {
                wc_send_table_based_daily_order_summary_email();
                wp_die(
                    esc_html__( 'WooCommerce summary e-mail sent (check your inbox).', 'borspirit' ),
                    esc_html__( 'E-mail sent', 'borspirit' ),
                    array( 'response' => 200 )
                );
            }
        }
        add_action( 'init', 'wc_send_order_summary' );
    }
