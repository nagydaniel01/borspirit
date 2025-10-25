<?php
/**
 * Description tab
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

global $post;

$heading = apply_filters( 'woocommerce_product_description_heading', __( 'Description', 'woocommerce' ) );

?>

<?php if ( $heading ) : ?>
	<h2 class="section__title"><?php echo esc_html( $heading ); ?></h2>
<?php endif; ?>

<div class="section__content">
    <div class="text-collapse">
        <div class="text-collapse__text" data-height="180">
            <?php the_content(); ?>
        </div>
        <div class="text-collapse__toggle">
            <button type="button" class="btn read-more-button js-collapse-toggle is-hidden"><?php echo esc_html__( 'Show more', 'borspirit' ); ?></a>
        </div>
    </div>
</div>