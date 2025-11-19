<?php

defined( 'ABSPATH' ) || exit;

global $product;

$heading = get_query_var( 'tab_title' );

// Try to get FAQs linked to this product
$faq_posts = get_field( 'product_faqs', $product->get_id() ) ?: [];

// Fallback: get FAQs from global "product_page_faq_items" option if product has none
if ( empty( $faq_posts ) ) {
    $faq_posts = get_field( 'product_page_faq_items', 'option' ) ?: [];
}

/*
// Fallback: get all FAQs if none found yet
if ( empty( $faq_posts ) ) {
    $faq_posts = get_posts( [
        'post_type'      => 'faq',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ] );
}
*/

// Filter out items where either title or content is empty
$faq_posts = array_filter( $faq_posts ?? [], function ($faq) {
    $title   = trim( $faq->post_title ?? '' );
    $content = trim( $faq->post_content ?? '' );

    return $title !== '' && $content !== '';
} );
?>

<?php if ( $heading ) : ?>
	<h2 class="section__title"><?php echo esc_html( $heading ); ?></h2>
<?php endif; ?>

<div class="section__content">
    <?php if ( ! empty( $faq_posts ) ) : ?>
        <div class="accordion" id="faqAccordion">
            <?php foreach ( $faq_posts as $index => $faq ) : 
                $title   = trim( $faq->post_title ?? '' );
                $content = trim( $faq->post_content ?? '' );
            ?>
                <div class="accordion-item">
                    <?php if ( $title ) : ?>
                        <h2 class="accordion-header" id="heading-<?php echo $index; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $index; ?>">
                                <?php echo esc_html( $title ); ?>
                            </button>
                        </h2>
                    <?php endif; ?>

                    <?php if ( $content ) : ?>
                        <div id="collapse-<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo $index; ?>" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <?php echo wp_kses_post( apply_filters( 'the_content', $content ) ); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <?php echo wpautop( __( 'No FAQs found', 'borspirit' ) ); ?>
    <?php endif; ?>
</div>
