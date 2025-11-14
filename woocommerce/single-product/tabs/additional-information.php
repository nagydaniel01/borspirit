<?php
/**
 * Additional Information tab
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/additional-information.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

$heading = apply_filters( 'woocommerce_product_additional_information_heading', __( 'Additional information', 'woocommerce' ) );

?>

<?php if ( $heading ) : ?>
	<h2 class="section__title"><?php echo esc_html( $heading ); ?></h2>
<?php endif; ?>

<div class="row">
	<div class="col-md-8">
		<?php do_action( 'woocommerce_product_additional_information', $product ); ?>
	</div>
	<div class="col-md-4">
		<?php
		// Get the saved values from the checkbox field for the current product
		$selected_tips 	  = get_field('product_food_pairing_tips');
		$tips_description = get_field('product_food_pairing_tips_description');

		// Ensure $selected_tips is an array
		if ( ! is_array( $selected_tips ) || empty( $selected_tips ) ) {
			$selected_tips = array();
		}

		if ( ! empty( $selected_tips ) ) :

			// Check if the repeater exists on the Options page
			if ( have_rows('food_pairing_tip_items', 'option') ) :

				echo '<h3 class="section__title">' . esc_html__( 'Food that goes well with this product', 'borspirit' ) . '</h3>';

				if ( $tips_description ) {
					echo wp_kses_post($tips_description);
				}

				echo '<ul class="section__list">';

				while ( have_rows('food_pairing_tip_items', 'option') ) : the_row();

					$text  = get_sub_field('food_pairing_tip_text');
					$image = get_sub_field('food_pairing_tip_image');

					// Skip if text is empty
					if ( empty( $text ) ) {
						continue;
					}

					// Sanitize text to match checkbox value
					$value = sanitize_title( $text );

					// Only display if this item was checked
					if ( in_array( $value, $selected_tips, true ) ) :

						// Initialize image URL
						$image_url = '';

						// Handle image formats: ID, array, or URL
						if ( is_array( $image ) && isset( $image['url'] ) ) {
							$image_url = $image['url'];
						} elseif ( is_numeric( $image ) ) {
							$image_url = wp_get_attachment_url( $image );
						} elseif ( is_string( $image ) ) {
							$image_url = $image;
						}

						// Skip if image URL is empty (optional, remove this if images are optional)
						if ( empty( $image_url ) ) {
							continue;
						}

						echo '<li class="section__listitem">';

						if ( ! empty( $image_url ) ) {
							echo '<img src="' . esc_url( $image_url ) . '" class="section__icon icon imgtosvg" />';
						}

						echo '<span class="section__text">' . esc_html( $text ) . '</span>';
						echo '</li>';

					endif;

				endwhile;

				echo '</ul>';

			else :
				// No repeater rows found
				//echo wpautop( esc_html__( 'No food pairing tips available.', 'borspirit' ) );
			endif;

		else :
			// No tips selected in the checkbox
			//echo wpautop( esc_html__( 'No food pairing tips selected for this product.', 'borspirit' ) );
		endif;
		?>
	</div>
</div>
