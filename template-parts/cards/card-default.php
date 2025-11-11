<?php
    $post_type    = $args['post_type'] ?? '';

    if (empty($post_type)) {
        return;
    }

    $current_user_id = get_current_user_id();

    $post_id      = get_the_ID();
    $title        = get_the_title();
    $thumbnail_id = get_post_thumbnail_id();
    $image_id     = $thumbnail_id ?? null;
    $categories   = get_the_terms($post_id, 'category');
    $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: $title;

    if (is_wp_error($categories)) {
        $categories = [];
    }

    $extra_classes = '';
    if ($post_type) {
        $extra_classes = 'card--'.$post_type;

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

<article class="card <?php echo esc_attr($extra_classes); ?>">
    <a href="<?php the_permalink(); ?>" class="card__link">
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
            <h3 class="card__title"><?php the_title(); ?></h3>
            
            <div class="card__lead"><?php the_excerpt(); ?></div>

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
    <?php if ( ! is_user_logged_in() ) : ?>
        <a class="card__bookmark" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">
            <svg class="icon icon-bookmark-empty">
                <use xlink:href="#icon-bookmark-empty"></use>
            </svg>
            <span class="visually-hidden"><?php echo esc_html__('Add to Bookmarks', 'borspirit'); ?></span>
        </a>
    <?php else : ?>
        <?php
            $bookmark_ids  = get_field('user_bookmarks', 'user_'.$current_user_id) ?: [];
            $is_bookmarked = in_array( get_the_ID(), $bookmark_ids, true );
            $bookmark_icon = $is_bookmarked ? 'bookmark' : 'bookmark-empty';
            $bookmark_text = $is_bookmarked ? __('Remove form bookmarks', 'borspirit') : __('Add to Bookmarks', 'borspirit');
        ?>
        <a id="btn-bookmark" class="card__bookmark" href="#" data-post-id="<?php echo esc_attr($post_id); ?>" data-bookmarked="<?php echo esc_attr($is_bookmarked ? 'true' : 'false'); ?>">
            <svg class="icon icon-<?php echo esc_attr($bookmark_icon); ?>">
                <use xlink:href="#icon-<?php echo esc_attr($bookmark_icon); ?>"></use>
            </svg>
            <span class="visually-hidden"><?php echo esc_html($bookmark_text); ?></span>
        </a>
    <?php endif; ?>
</article>
