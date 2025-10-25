<?php
global $product;

if ( ! $product ) return;

// Get the product content
$content = apply_filters( 'the_content', get_the_content() );

if ( ! empty( trim( strip_tags( $content ) ) ) ) : ?>
    <section class="section section--product-description">
        <div class="container">
            <h2><?php echo apply_filters( 'woocommerce_product_description_heading', __( 'Description', 'woocommerce' ) ); ?></h2>
            <?php echo $content; ?>
        </div>
    </section>
<?php endif; ?>
