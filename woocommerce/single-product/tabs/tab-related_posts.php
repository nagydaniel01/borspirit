<?php

defined( 'ABSPATH' ) || exit;

global $product;

$heading = get_query_var( 'tab_title' );

// Try to get Related posts linked to this product
$related_posts = get_field( 'product_related_posts', $product->get_id() ) ?: [];

// Filter out items where either title or content is empty
$related_posts = array_filter( $related_posts ?? [], function ($post) {
    $title   = trim( $post->post_title ?? '' );
    $content = trim( $post->post_content ?? '' );

    return $title !== '' && $content !== '';
} );
?>

<?php if ( $heading ) : ?>
	<h2 class="section__title"><?php echo esc_html( $heading ); ?></h2>
<?php endif; ?>

<div class="section__content">
    <?php if ( ! empty( $related_posts ) ) : ?>
        <div class="slider slider--related">
            <div class="slider__list">
                <?php foreach ( $related_posts as $post ) : setup_postdata( $post ); ?>
                    <div class="slider__item">
                        <?php 
                            $template_args = [
                                'post_type' => esc_attr(get_post_type($post))
                            ];

                            $template_slug = 'template-parts/cards/card-related.php';

                            if ( locate_template( $template_slug ) ) {
                                // File exists, include it
                                get_template_part( 'template-parts/cards/card', 'related', $template_args );
                            } else {
                                // File does not exist, handle accordingly
                                get_template_part( 'template-parts/cards/card', 'default', $template_args );
                            }
                        ?>
                    </div>
                <?php endforeach; wp_reset_postdata(); ?>
            </div>
            <div class="slider__controls"></div>
        </div>
    <?php else : ?>
        <?php echo wpautop( __( 'No related posts found.', 'borspirit' ) ); ?>
    <?php endif; ?>
</div>
