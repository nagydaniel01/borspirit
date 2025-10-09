<?php 
/** 
 * Template Name: Custom WooCommerce Shop Template
 */
?>

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Product_Query' ) ) {
    return;
}

$page_title = get_the_title();
$page_slug  = sanitize_title($page_title);

$paged             = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
$ordering          = WC()->query->get_catalog_ordering_args();
$products_per_page = apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() );

// ACF fields
$product_type       = get_field('type') ?: 'simple';
$products_per_page  = get_field('products_per_page') ?: $products_per_page;
$orderby            = get_field('orderby') ?: $ordering['orderby'];
$order              = get_field('order') ?: $ordering['order'];
$on_sale            = get_field('on_sale') ?: false;
$product_cats       = get_field('product_cat');
$product_tags       = get_field('product_tag');

// Base query
$args = [
    'type'       => $product_type,
    'status'     => 'publish',
    'limit'      => $products_per_page,
    'page'       => $paged,
    'orderby'    => $orderby,
    'order'      => $order,
    'return'     => 'ids',
    'visibility' => 'catalog',
];

// Filter by sale products
if ( $on_sale ) {
    $args['on_sale'] = true;
}

// Filter by categories
if ( $product_cats ) {
    $args['category'] = array_map(fn($cat) => $cat->slug, $product_cats);
}

// Filter by tags
if ( $product_tags ) {
    $args['tag'] = array_map(fn($tag) => $tag->slug, $product_tags);
}

/*
echo '<pre>';
var_dump($args);
echo '</pre>';
*/

// Query products
$query = new WC_Product_Query( $args );
$products = $query->get_products();

// Total products for pagination
$count_args = $args;
$count_args['limit'] = -1; // get all products
$total_products = count( (new WC_Product_Query( $count_args ))->get_products() );
$max_num_pages = ceil( $total_products / $products_per_page );

// Set WooCommerce loop props
wc_set_loop_prop( 'current_page', $paged );
wc_set_loop_prop( 'is_paginated', true );
wc_set_loop_prop( 'page_template', get_page_template_slug() );
wc_set_loop_prop( 'per_page', $products_per_page );
wc_set_loop_prop( 'total', $total_products );
wc_set_loop_prop( 'total_pages', $max_num_pages );
?>

<?php get_header( 'shop' ); ?>

<main class="page page--default page--archive page--archive-product page--<?php echo esc_attr( $page_slug ); ?>">
    <section class="section section--archive section--archive-product">
        <div class="container">
            <div class="woocommerce-breadcrumb-wrapper">
                <?php do_action( 'woocommerce_before_main_content' ); ?>
            </div>

            <header class="woocommerce-products-header">
                <h1 class="woocommerce-products-header__title page-title"><?php the_title(); ?></h1>
                <?php do_action( 'woocommerce_archive_description' ); ?>
            </header>

            <?php
                if ( $products ) : 
                    do_action( 'woocommerce_before_shop_loop' ); 

                    woocommerce_product_loop_start();

                    foreach ( $products as $product_id ) {
                        $post_object = get_post( $product_id );
                        setup_postdata( $GLOBALS['post'] =& $post_object );
                        do_action( 'woocommerce_shop_loop' );
                        wc_get_template_part( 'content', 'product' );
                    }
                    wp_reset_postdata();

                    woocommerce_product_loop_end();

                    do_action( 'woocommerce_after_shop_loop' ); // pagination
                else :
                    do_action( 'woocommerce_no_products_found' );
                endif;
            ?>

            <!--
            <h1 data-tg-tour="Welcome to the homepage! This is the main heading.">Hello WordPress</h1>

            <p data-tg-tour="Here’s some text. You can highlight any element.">
                This paragraph is part of the tour.
            </p>
            <button id="start-tour">Start Tour</button>
            -->

            <?php do_action( 'woocommerce_after_main_content' ); ?>
        </div>
    </section>
</main>

<?php get_footer( 'shop' ); ?>