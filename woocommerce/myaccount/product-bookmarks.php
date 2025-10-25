<?php
    $current_user_id = get_current_user_id();

    // Ensure the user is logged in
    if ( ! $current_user_id ) {
        wp_send_json_error([
            'message' => __('You must be logged in.', 'borspirit')
        ], 401);
    }

    // Get existing bookmarks from user meta
    $bookmarks = get_user_meta($current_user_id, 'user_bookmarks', true);
    if ( ! is_array($bookmarks) || empty($bookmarks) ) {
        echo wpautop( __('No bookmarks found.', 'borspirit') );
        return; // Stop here to avoid running the query
    }

    // Convert IDs to WC_Product objects and filter out invalid/trashed products
    $products = array_filter(array_map(function($product_id) {
        $product = wc_get_product($product_id);
        return $product && $product->get_status() === 'publish' ? $product : null;
    }, $bookmarks));

    // Get the current number of product columns from the WooCommerce settings
    $columns = wc_get_loop_prop( 'columns' );
    if ( ! $columns ) {
        $columns = apply_filters( 'loop_shop_columns', 4 ); // fallback default
    }
?>

<?php if ( ! empty($products) ) : ?>
    <ul class="products columns-<?php echo esc_attr($columns); ?>">
        <?php foreach ($products as $product) : ?>
            <?php
            // Allow WooCommerce template parts to work correctly
            $post_object = get_post($product->get_id());
            setup_postdata($GLOBALS['post'] =& $post_object);

            /**
             * Hook: woocommerce_shop_loop.
             */
            do_action('woocommerce_shop_loop');

            // Load WooCommerce product card template (content-product.php)
            wc_get_template_part('content', 'product');
            ?>
        <?php endforeach; ?>
        <?php wp_reset_postdata(); ?>
    </ul>
<?php endif; ?>
