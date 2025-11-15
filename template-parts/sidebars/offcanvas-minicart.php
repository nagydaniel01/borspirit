<?php if ( class_exists( 'WooCommerce' ) ) : ?>
    <div class="offcanvas offcanvas-end" id="offcanvasMiniCart" tabindex="-1" aria-labelledby="offcanvasMiniCartLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasMiniCartLabel">
                <?php echo esc_html__( 'Your cart', 'borspirit' ); ?>
                <?php if ( WC()->cart->get_cart_contents_count() > 0 ) : ?>
                    <span>&dash;</span>
                    <span class="cart_contents_count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                    <?php echo _n( 'item', 'items', WC()->cart->get_cart_contents_count(), 'borspirit' ); ?>
                <?php endif; ?>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="<?php echo esc_attr('Close', 'borspirit'); ?>"></button>
        </div>
        <div class="offcanvas-body">
            <div class="woocommerce-mini-cart__wrapper"><?php woocommerce_mini_cart(); ?></div>
        </div>
    </div>
<?php endif; ?>