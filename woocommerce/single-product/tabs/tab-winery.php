<?php

defined( 'ABSPATH' ) || exit;

global $product;

$heading = get_query_var( 'tab_title' );

// Flag to decide whether to show the tab title.
$show_tab_title = false;

// Collect valid terms with both name + description.
$valid_terms = [];

if ( $product instanceof WC_Product ) {
    $terms = wp_get_post_terms( $product->get_id(), 'pa_boraszat' );

    if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
        foreach ( $terms as $term ) {
            if ( ! empty( $term->name ) && ! empty( $term->description ) ) {
                $valid_terms[] = $term;
            }
        }

        if ( ! empty( $valid_terms ) ) {
            $show_tab_title = true;
        }
    }
}

// Output only if we have valid terms.
if ( $show_tab_title ) : ?>
    <?php if ( $heading ) : ?>
	    <h2 class="section__title"><?php echo esc_html( $heading ); ?></h2>
    <?php endif; ?>

    <?php foreach ( $valid_terms as $term ) : ?>
        <?php
            $title       = $term->name;
            $description = $term->description;
            $term_link   = get_term_link( $term );
            $gallery     = get_field( 'gallery', $term->taxonomy . '_' . $term->term_id ); // ACF gallery field.
        ?>

        <div class="section__content">
            <div class="row">
                <?php if ( ! empty( $gallery ) && is_array( $gallery ) ) : ?>
                    <div class="col-md-4 woocommerce-products-header">
                        <div class="slider woocommerce-products-header__gallery">
                            <?php foreach ( $gallery as $key => $image ) : ?>
                                <?php
                                    $image_id = null;

                                    if ( is_numeric( $image ) ) {
                                        $image_id = $image;
                                    } elseif ( is_array( $image ) && ! empty( $image['ID'] ) ) {
                                        $image_id = $image['ID'];
                                    }

                                    if ( $image_id ) {
                                        $alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

                                        if ( empty( $alt ) ) {
                                            $alt = sprintf(
                                                /* translators: %s: taxonomy term name */
                                                __( '%s image (%s)', 'borspirit' ),
                                                $term->name,
                                                $key + 1
                                            );
                                        }
                                        ?>
                                        <div class="woocommerce-products-header__gallery-item">
                                            <?php echo wp_get_attachment_image(
                                                $image_id,
                                                'medium_large',
                                                false,
                                                [
                                                    'class'   => 'woocommerce-products-header__image',
                                                    'alt'     => esc_attr( $alt ),
                                                    'loading' => 'lazy'
                                                ]
                                            ); ?>
                                        </div>
                                    <?php } ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="<?php echo ( ! empty( $gallery ) && is_array( $gallery ) ) ? 'col-md-8' : 'col'; ?>">
                    <h3><?php echo esc_html( $title ); ?></h3>
                    <?php echo wpautop( wp_kses_post( $description ) ); ?>

                    <?php if ( ! is_wp_error( $term_link ) ) : ?>
                        <?php
                            $aria_label = sprintf(
                                // translators: %s is the post title
                                __('Read more about %s', 'borspirit'),
                                $title
                            );
                        ?>
                        <a href="<?php echo esc_url( $term_link ); ?>" class="btn btn-outline-primary" aria-label="<?php echo esc_attr( $aria_label ); ?>">
                            <span><?php echo esc_html__( 'Read more', 'borspirit' ); ?></span>
                            <svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

<?php else : ?>
    <?php
        // Optional fallback message
        echo wpautop( __( 'No valid terms found.', 'borspirit' ) );
    ?>
<?php endif; ?>
