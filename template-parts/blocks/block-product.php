<?php

defined( 'ABSPATH' ) || exit;

global $product;

$alt_text     = get_post_meta($product->get_id(), '_wp_attachment_image_alt', true) ?: get_the_title();
?>

<div class="block block--product-sticky">
    <div class="container">
        <div class="block__inner">
            <?php echo get_the_post_thumbnail( $product->get_id(), 'product-sticky-thumbnail', ['class' => 'block__image', 'alt' => esc_attr($alt_text)] ); ?>
            <p class="block__title"><?php the_title(); ?></p>
            <?php echo woocommerce_template_single_rating(); ?>
            <?php echo woocommerce_template_single_price(); ?>
            <button type="button" class="btn btn-primary block__button js-sticky-add-to-cart">
                <svg class="icon icon-bag-shopping"><use xlink:href="#icon-bag-shopping"></use></svg>
                <span><?php echo esc_html( $product->single_add_to_cart_text() ); ?></span>
            </button>
        </div>
    </div>
</div>