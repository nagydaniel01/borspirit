<?php
if ( ! class_exists( 'WooCommerce' ) ) {
    return;
}

$GLOBALS['product_query_section'] = true;

// Get the current number of product columns from the WooCommerce settings
$columns = wc_get_loop_prop( 'columns' );
if ( ! $columns ) {
    $columns = apply_filters( 'loop_shop_columns', 4 ); // fallback default
}

$section_classes = build_section_classes($section, 'product_query');

$section_title      = $section['product_query_section_title'] ?? '';
$section_hide_title = $section['product_query_section_hide_title'] ?? false;
$section_slug       = sanitize_title($section_title);
$section_lead       = $section['product_query_section_lead'] ?? '';

$link               = $section['product_query_link'] ?? '';
$slider             = $section['product_query_slider'] ?? '';

$url         = $link['url'] ?? '';
$title       = $link['title'] ?? esc_url($url);
$target      = isset($link['target']) && $link['target'] !== '' ? $link['target'] : '_self';
$is_external = is_external_url($url, get_home_url());

$query_args = [
    'return'       => 'objects',
    'status'       => 'publish',
    'visibility'   => 'catalog',
    'type'         => $section['type'] ?? '',
    'limit'        => (int) ($section['products_per_page'] ?? get_option('posts_per_page')),
    'orderby'      => $section['orderby'] ?? 'date',
    'order'        => strtoupper($section['order'] ?? 'DESC'),
    'virtual'      => $section['virtual'] ?? null,
    'downloadable' => $section['downloadable'] ?? null,
];

// Manual selection
if (!empty($section['selection_type']) && $section['selection_type'] === 'manual') {
    if (!empty($section['selected_product_items']) && is_array($section['selected_product_items'])) {
        $query_args['include'] = array_map(function($p) {
            return is_object($p) ? $p->ID : $p; // Get ID if it's a WP_Post object
        }, $section['selected_product_items']);

        $query_args['orderby'] = 'post__in';
    }
}

// Auto selection – taxonomy filters
if (!empty($section['selection_type']) && $section['selection_type'] === 'auto') {
    if (!empty($section['product_cat'])) {
        $query_args['category'] = array_map(fn($t) => $t->slug, $section['product_cat']);
    }
    if (!empty($section['product_tag'])) {
        $query_args['tag'] = array_map(fn($t) => $t->slug, $section['product_tag']);
    }
}

// Featured products
if (!empty($section['selection_type']) && $section['selection_type'] === 'featured') {
    $query_args['tax_query'][] = [
        'taxonomy' => 'product_visibility',
        'field'    => 'name',
        'terms'    => 'featured',
        'operator' => 'IN',
    ];
}

// Popular products in the last month
if (!empty($section['selection_type']) && $section['selection_type'] === 'popular') {
    $date_query = [
        'after' => date('Y-m-d', strtotime('-1 month')),
        'inclusive' => true,
    ];

    $query_args['orderby']    = 'meta_value_num'; // Sort by meta numeric value
    $query_args['meta_key']   = 'total_sales';    // WooCommerce total sales meta key
    $query_args['order']      = 'DESC';           // Most sold first
    $query_args['date_query'] = [$date_query];    // Limit to last month
}

// Handle Sale Products
if (!empty($section['on_sale']) && $section['on_sale'] === true) {
    $sale_products = wc_get_product_ids_on_sale();
    if (!empty($sale_products)) {
        $query_args['include'] = $sale_products;
    } else {
        // No sale products, prevent returning all products
        $query_args['include'] = [0];
    }
}

// Meta query
if (!empty($section['meta_query']) && is_array($section['meta_query'])) {
    $meta_counter = 0;
    foreach ($section['meta_query'] as $row) {
        if ($meta_counter === 0) {
            $query_args['meta_query'] = ['relation' => 'AND'];
        }
        $query_args['meta_query'][] = [
            'key'     => $row['meta_key'] ?? '',
            'value'   => $row['meta_value'] ?? '',
            'compare' => $row['meta_compare'] ?? '=',
        ];
        $meta_counter++;
    }
}

/*
echo '<pre>';
var_dump($query_args);
echo '</pre>';
*/

$product_query = new WC_Product_Query($query_args);
$products      = $product_query->get_products();
?>

<?php if (!empty($products)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--product_query<?php echo esc_attr($section_classes); ?><?php echo ($slider != false) ? ' section--slider' : ''; ?>">
        <div class="container">
            <?php if (($section_title && $section_hide_title !== true) || $section_lead) : ?>
                <div class="section__header">
                    <?php if ($section_hide_title !== true) : ?>
                        <h1 class="section__title"><?php echo esc_html($section_title); ?></h1>
                    <?php endif; ?>

                    <?php if (!empty($url)) : ?>
                        <a href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($target); ?>" <?php echo $is_external ? 'rel="noopener noreferrer"' : ''; ?> class="btn btn-link section__link">
                            <span><?php echo esc_html($title); ?></span>
                            <svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($section_lead)) : ?>
                        <div class="section__lead"><?php echo wp_kses_post($section_lead); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="section__content">
                <?php if ($slider != false) : ?>
                    <div class="slider slider--product-query">
                        <div class="slider__list">
                            <?php foreach ($products as $product) : ?>
                                <?php
                                // Allow WooCommerce template parts to work correctly
                                $post_object = get_post($product->get_id());
                                setup_postdata($GLOBALS['post'] =& $post_object);

                                /**
                                 * Hook: woocommerce_shop_loop.
                                 */
                                do_action('woocommerce_shop_loop');

                                wc_get_template_part('content', 'product');
                                ?>
                            <?php endforeach; ?>
                            <?php wp_reset_postdata(); ?>
                        </div>
                        <div class="slider__controls"></div>
                    </div>
                <?php else : ?>
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
            </div>
        </div>
    </section>
<?php endif; ?>

<?php
unset($GLOBALS['product_query_section']);