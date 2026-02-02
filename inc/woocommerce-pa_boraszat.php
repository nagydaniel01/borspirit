<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }
    
    if ( ! function_exists( 'custom_product_header_wrapper' ) ) {
        /**
         * Adds a custom wrapper and title for WooCommerce product attribute archive pages.
         *
         * This function opens a <div> wrapper and outputs the page title.
         * Only applies to the 'pa_boraszat' product attribute archive.
         */
        function custom_product_header_wrapper() {
            if ( is_tax( 'pa_boraszat' ) ) {
                echo '<div class="woocommerce-products-header__inner row flex-row-reverse"><div class="col-md-7">';
            }

            // Output the page title
            echo '<h1 class="woocommerce-products-header__title page-title">' . woocommerce_page_title( false ) . '</h1>';
        }
        add_action( 'woocommerce_show_page_title', 'custom_product_header_wrapper', 10 );
    }

    if ( ! function_exists( 'custom_product_header_wrapper_end' ) ) {
        /**
         * Closes the custom wrapper for WooCommerce product attribute archive pages.
         *
         * This function closes the <div> opened in custom_product_header_wrapper().
         * Only applies to the 'pa_boraszat' product attribute archive.
         */
        function custom_product_header_wrapper_end() {
            if ( is_tax( 'pa_boraszat' ) ) {
                echo '</div>';
            }
        }
        add_action( 'woocommerce_archive_description', 'custom_product_header_wrapper_end', 15 );
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

            // Get the field for this term (ACF).
            $gallery = get_field( 'gallery', $term->taxonomy . '_' . $term->term_id );
            $video   = get_field( 'video', $term->taxonomy . '_' . $term->term_id );

            // Ensure gallery is an array
            if ( ! is_array( $gallery ) ) {
                $gallery = [];
            }

            // If video exists, append it as last item
            if ( ! empty( $video ) ) {
                // Extract iframe src
                preg_match('/src="([^"]+)"/', $video, $matches);
                $src = $matches[1] ?? '';

                $gallery[] = $src;
            }

            if ( empty( $gallery ) || ! is_array( $gallery ) ) {
                return; // No gallery found.
            }

            echo '</div>';

            echo '<div class="col-md-5"><div class="slider woocommerce-products-header__gallery">';

            foreach ( $gallery as $key => $item ) {
                echo '<div class="woocommerce-products-header__gallery-item">';

                if ( is_numeric( $item ) || ( is_array( $item ) && ! empty( $item['ID'] ) ) ) {
                    // Handle image
                    $image_id = is_numeric( $item ) ? $item : $item['ID'];

                    $alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

                    if ( empty( $alt ) ) {
                        $alt = sprintf(
                            /* translators: %s: taxonomy term name */
                            __( '%s image (%s)', 'textdomain' ),
                            $term->name,
                            $key + 1
                        );
                    }

                    echo wp_get_attachment_image(
                        $image_id,
                        'medium_large',
                        false,
                        [
                            'class' => 'woocommerce-products-header__image',
                            'alt'   => esc_attr( $alt ),
                            'loading' => 'lazy'
                        ]
                    );
                } else {
                    // Handle video (ACF oEmbed)
                    $video_id = get_youtube_video_id($item);

                    echo '<div class="woocommerce-products-header__video">';
                    echo '<div class="youtube-player" data-id="' . esc_attr($video_id) . '"></div>';
                    echo '</div>';
                }

                echo '</div>';
            }

            echo '</div></div>';
        }
        add_action( 'woocommerce_archive_description', 'my_wc_add_boraszat_gallery_in_shop_loop_header', 10 );
    }