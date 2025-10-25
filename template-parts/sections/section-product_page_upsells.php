<?php
global $product;

if ( ! $product ) return;

// Get upsell IDs
$upsells = $product->get_upsell_ids();

if ( ! empty( $upsells ) ) : ?>
    <section class="section section--product-upsells">
        <div class="container">
            <h2><?php echo apply_filters( 'woocommerce_upsells_products_heading', __( 'You may also like&hellip;', 'woocommerce' ) ); ?></h2>
            <?php woocommerce_upsell_display( 4, 4 ); ?>
        </div>
    </section>
<?php endif; ?>
