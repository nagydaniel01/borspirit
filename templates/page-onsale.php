<?php
/**
 * Template Name: On Sale Page
 */
?>

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Product_Query' ) ) {
    return;
}

$paged             = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
$ordering          = WC()->query->get_catalog_ordering_args();
$products_per_page = apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() );

// Get all on-sale product IDs
$on_sale_ids = wc_get_product_ids_on_sale();

// Filter products by current sale schedule
$current_time = current_time( 'timestamp' );

$valid_on_sale_ids = array_filter( $on_sale_ids, function( $product_id ) use ( $current_time ) {
    $product = wc_get_product( $product_id );

if ( ! $product ) {
        return false;
    }

    // Exclude products with no price
    if ( $product->is_type( 'simple' ) ) {
        if ( $product->get_price() === '' ) {
            return false;
        }
    }

    if ( $product->is_type( 'variable' ) ) {
        $variation_prices = $product->get_variation_prices( true );

        // If no variation has a price, exclude product
        if ( empty( $variation_prices['price'] ) ) {
            return false;
        }
    }

    // Only allow simple & variable products
    if ( ! $product->is_type( 'simple' ) && ! $product->is_type( 'variable' ) ) {
        return false;
    }

    $sale_from = $product->get_date_on_sale_from();
    $sale_to   = $product->get_date_on_sale_to();

    // If product has no scheduled dates, it's on sale now
    if ( ! $sale_from && ! $sale_to ) {
        return true;
    }

    // Check if the current time is within the sale period
    if ( ( ! $sale_from || $current_time >= $sale_from->getTimestamp() ) &&
         ( ! $sale_to   || $current_time <= $sale_to->getTimestamp() ) ) {
        return true;
    }

    return false;
});

if ( empty( $valid_on_sale_ids ) ) {
    $products = [];
    $total_products = 0;
    $max_num_pages = 1;
} else {
    $args = array(
        'status'     => 'publish',
        'limit'      => $products_per_page,
        'page'       => $paged,
        'orderby'    => $ordering['orderby'],
        'order'      => $ordering['order'],
        'return'     => 'ids',
        'include'    => $valid_on_sale_ids,
        'visibility' => 'catalog',
    );

    $query = new WC_Product_Query( $args );
    $products = $query->get_products();

    $total_products = count( $valid_on_sale_ids );
    $max_num_pages = ceil( $total_products / $products_per_page );
}

wc_set_loop_prop( 'current_page', $paged );
wc_set_loop_prop( 'is_paginated', true );
wc_set_loop_prop( 'page_template', get_page_template_slug() );
wc_set_loop_prop( 'per_page', $products_per_page );
wc_set_loop_prop( 'total', $total_products );
wc_set_loop_prop( 'total_pages', $max_num_pages );
?>

<?php get_header( 'shop' ); ?>

<main class="page page--default page--archive page--archive-product page--onsale">
    <section class="section section--archive section--archive-product">
        <div class="container">
            <?php do_action( 'woocommerce_before_main_content' ); ?>

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
            
            <?php do_action( 'woocommerce_after_main_content' ); ?>
        </div>
    </section>
</main>

<?php get_footer( 'shop' ); ?>
