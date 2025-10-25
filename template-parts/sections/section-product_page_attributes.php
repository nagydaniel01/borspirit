<?php
global $product;

if ( ! $product ) return;

// Get the product attributes
$attributes = $product->get_attributes();

if ( ! empty( $attributes ) ) : ?>
    <section class="section section--product-attributes">
        <div class="container">
            <h2><?php echo apply_filters( 'woocommerce_product_additional_information_heading', __( 'Additional information', 'woocommerce' ) ); ?></h2>
            <?php wc_display_product_attributes( $product ); ?>
        </div>
    </section>
<?php endif; ?>
