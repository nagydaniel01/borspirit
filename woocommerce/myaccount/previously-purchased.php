<?php
global $product;

$current_user_id = get_current_user_id();

// Ensure the user is logged in
if ( ! $current_user_id ) {
    wp_send_json_error([
        'message' => __( 'You must be logged in.', 'borspirit' ),
    ], 401);
    return;
}

// Get all completed or processing orders for the current user
$customer_orders = wc_get_orders([
    'customer_id' => $current_user_id,
    'status'      => ['completed', 'processing'],
    'limit'       => -1,
]);

// Check if orders exist
if ( empty( $customer_orders ) ) {
    echo wpautop( __( 'You have not purchased any products yet.', 'borspirit' ) );
    return;
}

$products = [];

// Collect products from orders
foreach ( $customer_orders as $order ) {
    if ( ! $order instanceof WC_Order ) {
        continue;
    }

    foreach ( $order->get_items() as $item ) {
        $product_id = $item->get_product_id();
        if ( ! isset( $products[ $product_id ] ) ) {
            $prod = wc_get_product( $product_id );
            if ( $prod ) {
                $products[ $product_id ] = $prod;
            }
        }
    }
}

// Check if there are any products
if ( empty( $products ) ) {
    echo wpautop( __( 'You have not purchased any products yet.', 'borspirit' ) );
    return;
}

if ( ! empty( $products ) ) : ?>
<table class="woocommerce-table woocommerce-table--order-products shop_table shop_table_responsive">
    <thead>
        <tr>
            <th scope="col" class="woocommerce-orders-table__header woocommerce-orders-table__header-product-thumbnail"><?php echo esc_html__( 'Product', 'woocommerce' ); ?></th>
            <th scope="col" class="woocommerce-orders-table__header woocommerce-orders-table__header-product-name">&nbsp;</th>
            <th scope="col" class="woocommerce-orders-table__header woocommerce-orders-table__header-product-price"><?php echo esc_html__( 'Price', 'woocommerce' ); ?></th>
            <th scope="col" class="woocommerce-orders-table__header woocommerce-orders-table__header-product-actions"><?php echo esc_html__( 'Actions', 'woocommerce' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ( $products as $prod ) : 
            if ( ! $prod instanceof WC_Product ) continue;
            $product = $prod; // Set global product for WooCommerce functions

            $alt_text         = get_post_meta($product->get_id(), '_wp_attachment_image_alt', true) ?: get_the_title($product->get_id());
            $add_to_cart_url  = ( $product->is_purchasable() && $product->is_in_stock() ) ? esc_url( $product->add_to_cart_url() ) : '';
            $add_to_cart_text = $product->single_add_to_cart_text();
        ?>
        <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--product">
            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-product-thumbnail">
                <?php echo get_the_post_thumbnail( $product->get_id(), 'product-sticky-thumbnail', ['class' => 'product-thumbnail-image', 'alt' => esc_attr($alt_text), 'loading' => 'lazy'] ); ?>
            </td>
            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-product-name">
                <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
                    <?php echo esc_html( $product->get_name() ); ?>
                </a>
            </td>
            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-product-price">
                <?php woocommerce_template_single_price(); ?>
            </td>
            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-product-actions">
                <?php if ( $add_to_cart_url ) : ?>
                    <a href="<?php echo $add_to_cart_url; ?>" class="button add_to_cart_button">
                        <?php echo esc_html( $add_to_cart_text ); ?>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
