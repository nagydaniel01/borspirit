<?php get_header(); ?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

    <?php 
        $current_user_id = get_current_user_id();
        $posts_per_page  = get_option('posts_per_page');

        $post_id         = get_the_ID();
        $post_type       = get_post_type();
        $categories_list = get_the_category_list(', ');
        $post_lead       = get_field('post_lead');

        $published       = get_the_date();
        $modified        = get_the_modified_date();
        
        $estimated_reading_time = get_estimated_reading_time( get_the_content() );

        // Get related faqs linked to this post
        $post_faq = get_field('post_faqs', get_the_ID()) ?: [];

        // Normalize FAQ items
        $post_faq = array_values(array_filter(array_map(function ($item) {

            // Case 1: FAQ is a WP_Post (relationship field)
            if ($item instanceof WP_Post) {
                return [
                    'question' => get_the_title($item),
                    'answer'   => apply_filters('the_content', $item->post_content),
                ];
            }

            // Case 2: FAQ is a post ID
            if (is_numeric($item)) {
                $post = get_post($item);
                if ($post) {
                    return [
                        'question' => get_the_title($post),
                        'answer'   => apply_filters('the_content', $post->post_content),
                    ];
                }
            }

            // Case 3: FAQ is already an array (ACF repeater)
            if (is_array($item) && ! empty($item['question']) && ! empty($item['answer'])) {
                return [
                    'question' => $item['question'],
                    'answer'   => $item['answer'],
                ];
            }

            return null;

        }, $post_faq)));

        // Get related products linked to this post
        $products = get_field('post_related_products', get_the_ID()) ?: [];

        // Normalize into WC_Product objects
        $products = array_map(function($item) {
            // Case 1: If it's a Post object, convert to product ID
            if ($item instanceof WP_Post) {
                $item = $item->ID;
            }

            // Case 2: If numeric ID, try to get WC product
            if (is_numeric($item)) {
                return wc_get_product($item);
            }

            // Case 3: If somehow already a WC_Product, return it
            if ($item instanceof WC_Product) {
                return $item;
            }

            return null;
        }, $products);

        // Related posts
        $related_posts = new WP_Query([
            'post_status'    => 'publish',
            'posts_per_page' => $posts_per_page,
            'category__in'   => wp_get_post_categories(get_the_ID()),
            'post__not_in'   => [get_the_ID()],
        ]);

        // Define taxonomy dynamically based on post type
        switch ( $post_type ) {
            case 'post':
                $taxonomy = 'category';
                break;
                
            case 'product':
                $taxonomy = 'product_cat';
                break;

            default:
                $taxonomy = '';
                break;
        }
    ?>

    <main class="page page--single page--single-<?php echo esc_attr( $post_type ); ?>">
        <section class="section section--single section--single-<?php echo esc_attr( $post_type ); ?>">
            <div class="container container--narrow">
                <header class="section__header">
                    <?php if ( function_exists('rank_math_the_breadcrumbs') ) rank_math_the_breadcrumbs(); ?>

                    <h1 class="section__title"><?php the_title(); ?></h1>

                    <?php if ( $post_lead ) : ?>
                        <div class="section__lead"><?php echo wp_kses_post( $post_lead ); ?></div>
                    <?php endif; ?>

                    <div class="section__meta">
                        <span class="section__date">
                            <?php
                                if ( $published !== $modified ) {
                                    // Show last modified date if different
                                    printf(
                                        /* translators: %s: Post modified date */
                                        __('Updated on %s', 'borspirit'),
                                        esc_html( $modified )
                                    );
                                } else {
                                    // Otherwise show published date
                                    printf(
                                        /* translators: %s: Post date */
                                        __('Published on %s', 'borspirit'),
                                        esc_html( $published )
                                    );
                                }
                            ?>
                        </span>

                        <?php if ( $estimated_reading_time ) : ?>
                            <span class="section__reading-time">
                                <?php
                                    /* translators: %s: Estimated reading time in minutes */
                                    printf(
                                        _n(
                                            '%s minute reading',   // singular
                                            '%s minutes reading',  // plural
                                            $estimated_reading_time,
                                            'borspirit'
                                        ),
                                        esc_html( $estimated_reading_time )
                                    );
                                ?>
                            </span>
                        <?php endif; ?>

                        <?php if ( ! is_user_logged_in() ) : ?>
                            <a class="section__bookmark" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <svg class="icon icon-bookmark-empty">
                                    <use xlink:href="#icon-bookmark-empty"></use>
                                </svg>
                                <span><?php echo esc_html__('Add to bookmarks', 'borspirit'); ?></span>
                            </a>
                        <?php else : ?>
                            <?php
                                $bookmark_ids  = get_field('user_bookmarks', 'user_'.$current_user_id) ?: [];
                                $is_bookmarked = in_array( get_the_ID(), $bookmark_ids, true );
                                $bookmark_icon = $is_bookmarked ? 'bookmark' : 'bookmark-empty';
                                $bookmark_text = $is_bookmarked ? __('Remove form bookmarks', 'borspirit') : __('Add to bookmarks', 'borspirit');
                            ?>
                            <a id="btn-bookmark" class="section__bookmark" href="#" data-post-id="<?php echo esc_attr($post_id); ?>" data-bookmarked="<?php echo esc_attr($is_bookmarked ? 'true' : 'false'); ?>">
                                <svg class="icon icon-<?php echo esc_attr($bookmark_icon); ?>">
                                    <use xlink:href="#icon-<?php echo esc_attr($bookmark_icon); ?>"></use>
                                </svg>
                                <span><?php echo esc_html( $bookmark_text ); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="section__image-wrapper">
                            <?php
                                $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
                                $alt_text = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );

                                the_post_thumbnail('full', [
                                    'class'         => 'section__image',
                                    'alt'           => $alt_text ?: get_the_title(),
                                    'loading'       => 'eager',
                                    'fetchpriority' => 'high',
                                    'decoding'      => 'async'
                                ]);
                            ?>
                        </div>
                    <?php endif; ?>
                </header>
                
                
                <div class="section__content">
                    <?php echo do_shortcode( '[table_of_contents]' ); ?>
                    
                    <?php
                    // The main content
                    the_content();

                    // Optional: Pagination for multi-page posts
                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . __('Pages:', 'borspirit'),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>

                <footer class="section__footer">
                    <?php if ( $taxonomy ) : ?>
                        <?php 
                            $taxonomy_obj   = get_taxonomy( $taxonomy ); 
                            $taxonomy_label = $taxonomy_obj ? $taxonomy_obj->labels->name : __('Categories', 'borspirit');
                        ?>
                        <span class="section__categories category">
                            <div class="category__container">
                                <strong class="visually-hidden"><?php echo esc_html( $taxonomy_label ) . ':'; ?></strong>
                                <div class="category__wrapper">
                                    <?php 
                                        // Categories
                                        wp_list_categories( array(
                                            'current_category'     => 0,
                                            'depth'                => 0,
                                            'echo'                 => true,
                                            'exclude'              => '',
                                            'exclude_tree'         => '',
                                            'feed'                 => '',
                                            'feed_image'           => '',
                                            'feed_type'            => '',
                                            'hide_title_if_empty'  => false,
                                            'separator'            => '',
                                            'show_count'           => 0,
                                            'show_option_all'      => '',
                                            'show_option_none'     => '',
                                            'style'                => '',
                                            'taxonomy'             => $taxonomy,
                                            'title_li'             => '',
                                            'use_desc_for_title'   => 0,
                                            'walker'               => '',
                                        ) ); 
                                    ?>
                                </div>
                            </div>
                        </span>
                    <?php endif; ?>

                    <?php if ( ! empty( $post_faq ) ) : ?>
                        <?php
                            $section_slug        = 'faq';
                            $extra_classes       = ' accordion--alt';
                            $accordion_behavior  = 'standard'; // standard | collapsed | always_open
                        ?>
                        <div class="accordion<?php echo esc_attr( $extra_classes ); ?>" id="accordion-<?php echo esc_attr( $section_slug ); ?>">
                            <?php foreach ( $post_faq as $index => $faq ) : 
                                $is_first    = ( $index === 0 );
                                $item_id     = $section_slug . '_' . $index;
                                $title       = $faq['question'] ?? '';
                                $description = $faq['answer'] ?? '';

                                if ( ! $title || ! $description ) {
                                    continue;
                                }

                                // Defaults
                                $collapse_classes = 'accordion-collapse collapse';
                                $show_class       = '';
                                $aria_expanded    = 'false';
                                $collapse_attrs   = '';

                                switch ($accordion_behavior) {
                                    case 'standard':
                                        if ($is_first) {
                                            $show_class    = ' show';
                                            $aria_expanded = 'true';
                                        }
                                        $collapse_attrs = ' data-bs-parent="#accordion-' . esc_attr($section_slug) . '"';
                                        break;

                                    case 'collapsed':
                                        // All start collapsed, one open at a time
                                        $collapse_attrs = ' data-bs-parent="#accordion-' . esc_attr($section_slug) . '"';
                                        break;

                                    case 'always_open':
                                        if ($is_first) {
                                            $show_class    = ' show';
                                            $aria_expanded = 'true';
                                        }
                                        // No parent attribute allows multiple open
                                        break;
                                }

                                $button_attrs = sprintf(
                                    'data-bs-toggle="collapse" data-bs-target="#collapse-%1$s" aria-expanded="%2$s" aria-controls="collapse-%1$s"',
                                    esc_attr( $item_id ),
                                    esc_attr( $aria_expanded )
                                );
                            ?>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-<?php echo esc_attr( $item_id ); ?>">
                                    <button
                                        class="accordion-button <?php echo ( $aria_expanded === 'false' ? 'collapsed' : '' ); ?>"
                                        type="button"
                                        <?php echo $button_attrs; ?>
                                    >
                                        <?php echo esc_html( $title ); ?>
                                    </button>
                                </h2>

                                <div
                                    id="collapse-<?php echo esc_attr( $item_id ); ?>"
                                    class="<?php echo esc_attr( $collapse_classes . $show_class ); ?>"
                                    aria-labelledby="heading-<?php echo esc_attr( $item_id ); ?>"
                                    <?php echo $collapse_attrs; ?>
                                >
                                    <div class="accordion-body">
                                        <?php echo wp_kses_post( $description ); ?>
                                    </div>
                                </div>
                            </div>

                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $products ) ) : ?>
                        <div class="section__related-products">
                            <h2 class="section__title"><?php echo esc_html__('Related products', 'borspirit'); ?></h2>

                            <div class="slider slider--product-query slider--related-products">
                                <div class="slider__list">
                                    <?php foreach ( $products as $product ) : ?>
                                        <?php
                                        // Allow WooCommerce template parts to work correctly
                                        $post_object = get_post($product->get_id());
                                        setup_postdata($GLOBALS['post'] =& $post_object);

                                        /**
                                         * Hook: woocommerce_shop_loop.
                                         */
                                        do_action('woocommerce_shop_loop');

                                        // Load WooCommerce product card template (content-product.php)
                                        wc_get_template_part('content', 'product');
                                        ?>
                                    <?php endforeach; wp_reset_postdata(); ?>
                                </div>
                                <div class="slider__controls"></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( $related_posts->have_posts() ) : ?>
                        <div class="section__related-posts">
                            <h2 class="section__title"><?php echo esc_html__('You may also like…', 'borspirit'); ?></h2>

                            <div class="slider slider--related" id="related-posts-slider">
                                <div class="slider__list">
                                    <?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>
                                        <div class="slider__item">
                                            <?php
                                                $template_args = [
                                                    'post_type' => esc_attr($post_type)
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
                                    <?php endwhile; wp_reset_postdata(); ?>
                                </div>
                                <div class="slider__controls"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </footer>

                <?php
                // Load comments template
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>
            </div>
        </section>
    </main>

    <?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>
