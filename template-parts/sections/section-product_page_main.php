<?php
global $product;

if ( ! $product ) return;
?>

<section class="section section--product-main">
    <div class="container">
        <div class="section__inner">
            <!-- Left column: Product gallery -->
            <div class="gallery entry-gallery">
                <?php
                global $product;

                $columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
                $post_thumbnail_id = $product->get_image_id();
                $wrapper_classes   = apply_filters(
                    'woocommerce_single_product_image_gallery_classes',
                    array(
                        'woocommerce-product-gallery',
                        'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
                        'woocommerce-product-gallery--columns-' . absint( $columns ),
                        'images',
                    )
                );
                ?>

                <div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" 
                    data-columns="<?php echo esc_attr( $columns ); ?>" 
                    style="opacity: 0; transition: opacity .25s ease-in-out;">

                    <div class="woocommerce-product-gallery__wrapper">
                        <?php
                        if ( $post_thumbnail_id ) {
                            $html = wc_get_gallery_image_html( $post_thumbnail_id, true );
                        } else {
                            $html = sprintf(
                                '<div class="woocommerce-product-gallery__image--placeholder">
                                    <img src="%s" alt="%s" class="wp-post-image" />
                                </div>',
                                esc_url( wc_placeholder_img_id( 'woocommerce_single' ) ),
                                esc_html__( 'Awaiting product image', 'woocommerce' )
                            );
                        }

                        echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

                        // This action outputs the gallery thumbnails
                        do_action( 'woocommerce_product_thumbnails' );
                        ?>
                    </div>
                </div>
            </div>

            <!-- Right column: Product info -->
            <div class="summary entry-summary">
                <?php
                // Product title
                $title = get_the_title();
                if ( $title ) {
                    echo '<h1 class="product_title entry-title">' . esc_html( $title ) . '</h1>';
                }
                
                // Product excerpt
                $excerpt = get_the_excerpt();
                if ( $excerpt ) {
                    echo '<div class="woocommerce-product-details__short-description">' . wp_kses_post( $excerpt ) . '</div>';
                }

                // Product rating
                if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
                    echo woocommerce_template_single_rating();
                }

                // Product price
                if ( $product->get_price() !== '' ) {
                    woocommerce_template_single_price();
                }

                // Add to cart button
                if ( $product->is_purchasable() ) {
                    woocommerce_template_single_add_to_cart();
                }

                // Product meta
                echo '<div class="product_meta">';
                if ( $product->get_sku() ) {
                    echo '<span class="sku">SKU: ' . esc_html( $product->get_sku() ) . '</span>';
                }
                $categories = wc_get_product_category_list( $product->get_id() );
                if ( $categories ) {
                    echo '<span class="posted_in"><svg class="product_meta__icon icon icon-tags"><use xlink:href="#icon-tags"></use></svg><span class="visually-hidden">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . '</span> ' . $categories . '</span>';
                }
                $tags = wc_get_product_tag_list( $product->get_id() );
                if ( $tags ) {
                    echo '<span class="tagged_as"><svg class="product_meta__icon icon icon-hashtag"><use xlink:href="#icon-hashtag"></use></svg><span class="visually-hidden">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . '</span> ' . $tags . '</span>';
                }
                echo '</div>';
                ?>
            </div>
        </div>
    </div>
</section>
