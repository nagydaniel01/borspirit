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

// Prepare timestamps
$today = current_time( 'timestamp' );

// Get all published products on sale
$args = array(
    'status'     => 'publish',
    'limit'      => $products_per_page,
    'page'       => $paged,
    'orderby'    => $ordering['orderby'],
    'order'      => $ordering['order'],
    'visibility' => 'catalog',
    'return'     => 'objects', // Return WC_Product objects
);

$query = new WC_Product_Query( $args );
$all_products = $query->get_products();

// Filter products by actual on-sale status and dates
$products = array_filter( $all_products, function( $product ) use ( $today ) {
    if ( ! $product->is_on_sale() ) {
        return false;
    }

    $date_from = $product->get_date_on_sale_from();
    $date_to   = $product->get_date_on_sale_to();

    $from_ts = $date_from ? $date_from->getTimestamp() : 0;
    $to_ts   = $date_to ? $date_to->getTimestamp() : 0;

    // Valid if sale is active or has no start/end restriction
    $active = true;

    if ( $from_ts && $today < $from_ts ) {
        $active = false;
    }

    if ( $to_ts && $today > $to_ts ) {
        $active = false;
    }

    return $active;
});

// Pagination
$total_products = count( $products );
$max_num_pages  = ceil( $total_products / $products_per_page );

// Slice products for current page
$offset   = ( $paged - 1 ) * $products_per_page;
$products = array_slice( $products, $offset, $products_per_page );

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

            <header class="woocommerce-products-header">
                <h1 class="woocommerce-products-header__title page-title"><?php the_title(); ?></h1>
                <?php do_action( 'woocommerce_archive_description' ); ?>
            </header>

            <?php if ( $products ) : ?>

                <?php do_action( 'woocommerce_before_shop_loop' ); ?>

                <?php woocommerce_product_loop_start(); ?>

                <?php foreach ( $products as $product ) : ?>
                    <?php
                    $post_object = get_post( $product->get_id() );
                    setup_postdata( $GLOBALS['post'] =& $post_object );
                    wc_get_template_part( 'content', 'product' );
                    ?>
                <?php endforeach; ?>

                <?php woocommerce_product_loop_end(); ?>

                <?php do_action( 'woocommerce_after_shop_loop' ); ?>

                <div class="woocommerce-pagination">
                    <?php
                    echo paginate_links( array(
                        'total'   => $max_num_pages,
                        'current' => $paged,
                        'prev_text' => __( 'Previous', 'woocommerce' ),
                        'next_text' => __( 'Next', 'woocommerce' ),
                    ) );
                    ?>
                </div>

                <?php wp_reset_postdata(); ?>

            <?php else : ?>

                <?php do_action( 'woocommerce_no_products_found' ); ?>

            <?php endif; ?>

        </div>
    </section>
</main>

<?php get_footer( 'shop' ); ?>
