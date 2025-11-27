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
 * Check if ANY pa_boraszat term has BOTH name AND description
 */
$has_valid_boraszat_term = false;

if ( $product instanceof WC_Product ) {
    $terms = wp_get_post_terms( $product->get_id(), 'pa_boraszat' );

    if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
        foreach ( $terms as $term ) {
            if ( ! empty( $term->name ) && ! empty( $term->description ) ) {
                $has_valid_boraszat_term = true;
                break;
            }
        }
    }
}

/**
 * Get product sections (same as tabs but without navigation).
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $product_tabs ) ) : ?>

	<?php foreach ( $product_tabs as $key => $product_tab ) : ?>

		<?php
			// Skip ONLY the winary section if no valid terms exist
			if ( $key === 'winery' && ! $has_valid_boraszat_term ) {
				continue;
			}
        ?>

		<div class="section section--product--<?php echo esc_attr( $key ); ?> wc-section" id="<?php echo esc_attr( $key ); ?>">
			<div class="container">
				<?php
				if ( isset( $product_tab['callback'] ) ) {
					call_user_func( $product_tab['callback'], $key, $product_tab );
				}
				?>
			</div>
		</div>
	<?php endforeach; ?>

	<?php do_action( 'woocommerce_product_after_tabs' ); ?>

<?php endif; ?>