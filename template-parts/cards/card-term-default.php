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

    // Get gallery field (ACF)
    $gallery = get_field('gallery', $taxonomy . '_' . $term_id);

    // Get the first image from gallery if available
    $image_id = '';
    $alt_text = '';

    if ($gallery && is_array($gallery)) {
        $first_image = $gallery[0];

        if (is_numeric($first_image)) {
            // Case: Gallery returns IDs
            $image_id = $first_image;
            $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?? $title;
        } elseif (is_array($first_image) && !empty($first_image['ID'])) {
            // Case: Gallery returns full image array
            $image_id = $first_image['ID'];
            $alt_text = !empty($first_image['alt']) ? $first_image['alt'] : $title;
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
                <?php echo wp_get_attachment_image($image_id, 'medium_large', false, ['class' => 'card__image', 'alt' => esc_attr($alt_text), 'loading' => 'lazy']); ?>
            </div>
        <?php endif; ?>

        <div class="card__content">
            <h3 class="card__title"><?php echo esc_html($title); ?></h3>
        </div>
    </a>
</article>