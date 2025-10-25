<?php
global $product;

if ( ! $product ) return;

// Only show reviews if there are reviews or comments are open
if ( $product->get_review_count() > 0 || comments_open() ) : ?>
    <section class="section section--product-reviews">
        <div class="container">
            <h2 class="woocommerce-Reviews-title">
                <?php
                $count = $product->get_review_count();
                if ( $count && wc_review_ratings_enabled() ) {
                    printf(
                        _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'woocommerce' ),
                        esc_html( $count ),
                        '<span>' . get_the_title() . '</span>'
                    );
                } else {
                    echo esc_html__( 'Reviews', 'woocommerce' );
                }
                ?>
            </h2>
            <?php comments_template(); ?>
        </div>
    </section>
<?php endif; ?>
