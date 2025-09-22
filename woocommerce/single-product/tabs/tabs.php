<?php
/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

/**
 * Get product sections (same as tabs but without navigation).
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $product_tabs ) ) : ?>

	<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
		<section class="section section--product--<?php echo esc_attr( $key ); ?> wc-section" id="<?php echo esc_attr( $key ); ?>">
			<div class="container">
				<h2 class="woocommerce-Section-title">
					<?php
					// Use review count/title for the "reviews" section
					if ( 'reviews' === $key ) {
						$count = $product->get_review_count();
						if ( $count && wc_review_ratings_enabled() ) {
							/* translators: 1: reviews count 2: product name */
							$reviews_title = sprintf(
								esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'woocommerce' ) ),
								esc_html( $count ),
								'<span>' . get_the_title() . '</span>'
							);
							echo apply_filters( 'woocommerce_reviews_title', $reviews_title, $count, $product ); // phpcs:ignore WordPress.Security.EscapeOutput
						} else {
							esc_html_e( 'Reviews', 'woocommerce' );
						}
					} else {
						// Default title for other sections
						echo wp_kses_post(
							apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key )
						);
					}
					?>
				</h2>

				<div class="section__content">
					<?php
					if ( isset( $product_tab['callback'] ) ) {
						call_user_func( $product_tab['callback'], $key, $product_tab );
					}
					?>
				</div>
			</div>
		</section>
	<?php endforeach; ?>

	<?php do_action( 'woocommerce_product_after_tabs' ); ?>

<?php endif; ?>