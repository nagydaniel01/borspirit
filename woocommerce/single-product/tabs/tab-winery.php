<?php

defined( 'ABSPATH' ) || exit;

global $product;

?>

<!--<h2><?php //echo esc_html(get_query_var('tab_title')); ?></h2>-->

<?php if ( $product && is_object($product) ) : ?>
    <?php $terms = wp_get_post_terms( $product->get_id(), 'pa_boraszat' ); // Get the terms for the attribute 'pa_boraszat' ?>

    <?php if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) : ?>
        <?php foreach ( $terms as $term ) : ?>
            <?php
                $title       = $term->name;                                                    // Term name / title
                $description = $term->description;                                             // Term description
                $gallery     = get_field( 'gallery', $term->taxonomy . '_' . $term->term_id ); // Get the "gallery" field for this term (ACF).

                if ( empty( $gallery ) || ! is_array( $gallery ) ) {
                    return; // No gallery found.
                }
                
                echo '<h3>' . esc_html( $title ) . '</h3>';
                echo '<div class="woocommerce-products-header__gallery">';

                foreach ( $gallery as $key => $image ) {
                    $image_id = null;

                    // Handle ACF gallery return format.
                    if ( is_numeric( $image ) ) {
                        $image_id = $image;
                    } elseif ( is_array( $image ) && ! empty( $image['ID'] ) ) {
                        $image_id = $image['ID'];
                    }

                    if ( $image_id ) {
                        // Get existing alt text.
                        $alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

                        // Fallback alt text if none is set.
                        if ( empty( $alt ) ) {
                            $alt = sprintf(
                                /* translators: %s: taxonomy term name */
                                __( '%s image (%s)', 'textdomain' ),
                                $term->name,
                                $key + 1
                            );
                        }

                        echo '<div class="woocommerce-products-header__gallery-item">';
                        echo wp_get_attachment_image( $image_id, 'medium_large', false, [ 'class' => esc_attr( 'woocommerce-products-header__image' ), 'alt' => esc_attr( $alt ) ] );
                        echo '</div>';
                    }
                }

                echo '</div>';
                echo '<p>' . wp_kses_post( $description ) . '</p>';
            ?>
        <?php endforeach; ?>
    <?php else : ?>
        <?php
            echo wpautop(
                sprintf(
                    /* translators: %s: taxonomy name */
                    __( 'No %s term assigned.', TEXT_DOMAIN ),
                    esc_html( $taxonomy_name )
                )
            );
        ?>
    <?php endif; ?>
<?php endif; ?>