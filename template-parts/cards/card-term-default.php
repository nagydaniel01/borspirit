<?php
    $term        = $args['term'];

    if (empty($term)) {
        return;
    }

    $term_id     = $term->term_id;
    $taxonomy    = $term->taxonomy;
    $term_link   = get_term_link($term);
    $title       = $term->name;
    //$description = term_description($term_id, $taxonomy);

    $image_id  = '';
    $alt_text  = __('', 'borspirit');

    // If taxonomy is 'product_cat', get WooCommerce thumbnail
    if ($taxonomy === 'product_cat') {
        $thumbnail_id = get_term_meta($term_id, 'thumbnail_id', true);
        if ($thumbnail_id) {
            $image_id = $thumbnail_id;
            $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true) ?: $title;
        }
    }

    // Otherwise, use ACF gallery if available
    if ($taxonomy !== 'product_cat') {
        $gallery = get_field('gallery', $taxonomy . '_' . $term_id);

        if ($gallery && is_array($gallery)) {
            $first_image = $gallery[0];

            if (is_numeric($first_image)) {
                $image_id = $first_image;
                $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: $title;
            } elseif (is_array($first_image) && !empty($first_image['ID'])) {
                $image_id = $first_image['ID'];
                $alt_text = !empty($first_image['alt']) ? $first_image['alt'] : $title;
            }
        }
    }

    $extra_classes = '';
    if ($taxonomy) {
        $extra_classes = 'card--'.$taxonomy;
    }
?>

<article class="card card--term <?php echo esc_attr($extra_classes); ?>">
    <a href="<?php echo esc_url($term_link); ?>" class="card__link">
        <?php if ($image_id) : ?>
            <div class="card__header">
                <div class="card__image-wrapper">
                    <?php echo wp_get_attachment_image($image_id, 'medium_large', false, ['class' => 'card__image', 'alt' => esc_attr($alt_text), 'loading' => 'lazy']); ?>
                </div>
            </div>
        <?php elseif ( defined( 'PLACEHOLDER_IMG_SRC' ) && PLACEHOLDER_IMG_SRC ) : ?>
            <div class="card__header">
                <div class="card__image-wrapper">
                    <img src="<?php echo esc_url( PLACEHOLDER_IMG_SRC ); ?>" alt="" class="card__image card__image--placeholder" loading="lazy">
                </div>
            </div>
        <?php endif; ?>

        <div class="card__content">
            <h3 class="card__title"><?php echo esc_html($title); ?></h3>
        </div>
    </a>
</article>