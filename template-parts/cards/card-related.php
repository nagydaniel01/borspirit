<?php
    $post_type    = $args['post_type'] ?? '';

    if (empty($post_type)) {
        return;
    }
    
    $post_id      = get_the_ID();
    $title        = get_the_title();
    $thumbnail_id = get_post_thumbnail_id();
    $image_id     = $thumbnail_id ?? null;
    $categories   = get_the_terms($post_id, 'category');
    $alt_text     = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: $title;

    if (is_wp_error($categories)) {
        $categories = [];
    }

    $extra_classes = '';
    if ($post_type) {
        $extra_classes = ' card--'.$post_type;

        /*
        // Get singular name of post type for ARIA label
        $post_type_singular_name = '';
        $post_type_obj = get_post_type_object($post_type);
        if (is_object($post_type_obj) && isset($post_type_obj->labels->singular_name)) {
            $post_type_singular_name = mb_strtolower($post_type_obj->labels->singular_name);
        }

        $aria_label = sprintf(
            // translators: %1$s is the post title, %2$s is the singular post type name
            __('A(z) "%1$s" című %2$s megtekintése', 'borspirit'),
            $title,
            $post_type_singular_name
        );
        */
    }
?>

<article class="card card--related<?php echo esc_attr($extra_classes); ?>">
    <a href="<?php the_permalink(); ?>" class="card__link">
        <?php if ($image_id) : ?>
            <div class="card__header">
                <div class="card__image-wrapper">
                    <?php echo wp_get_attachment_image($image_id, 'thumbnail', false, ['class' => 'card__image', 'alt' => esc_attr($alt_text), 'loading' => 'lazy']); ?>
                </div>
            </div>
        <?php elseif ( defined( 'PLACEHOLDER_IMG_SRC' ) && PLACEHOLDER_IMG_SRC ) : ?>
            <div class="card__header">
                <div class="card__image-wrapper">
                    <img width="150" height="150" src="<?php echo esc_url( PLACEHOLDER_IMG_SRC ); ?>" alt="" class="card__image card__image--placeholder" loading="lazy">
                </div>
            </div>
        <?php endif; ?>

        <div class="card__content">
            <h3 class="card__title"><?php the_title(); ?></h3>

            <div class="card__meta">
                <?php if (!empty($categories) && is_array($categories)) : ?>
                    <span class="card__categories">
                        <?php
                            $primary_category = '';

                            if (function_exists('get_rank_math_primary_term_name')) {
                                $primary_category = get_rank_math_primary_term_name(null, 'category');
                            }

                            if (empty($primary_category) && !empty($categories[0]) && isset($categories[0]->name)) {
                                $primary_category = $categories[0]->name;
                            }
                        ?>

                        <?php if (!empty($primary_category)) : ?>
                            <span class="card__category"><?php echo esc_html($primary_category); ?></span>
                        <?php endif; ?>
                    </span>
                <?php endif; ?>

                <time datetime="<?php echo esc_html(get_the_date('c')); ?>" class="card__date"><?php echo get_the_date(); ?></time>
            </div>

            <span class="card__button">
                <svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg>
            </span>
        </div>
    </a>
</article>
