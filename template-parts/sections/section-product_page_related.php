<?php
global $product;

if ( ! $product ) return;

// Get related products
$related_products = wc_get_related_products( $product->get_id(), 4 ); // 4 is the number of products to display

if ( ! empty( $related_products ) ) : ?>
    <section class="section section--product-related">
        <div class="container">
            <h2><?php echo apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) ); ?></h2>
            <?php woocommerce_output_related_products(); ?>
        </div>
    </section>
<?php endif; ?>
