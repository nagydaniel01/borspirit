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

        // Related posts
        $related_posts = new WP_Query([
            'post_status'    => 'publish',
            'posts_per_page' => $posts_per_page,
            'category__in'   => wp_get_post_categories(get_the_ID()),
            'post__not_in'   => [get_the_ID()],
        ]);

        // Define taxonomy dynamically based on post type
        switch ( $post_type ) {
            case 'product':
                $taxonomy = 'product_cat';
                break;

            case 'service':
                $taxonomy = 'service_cat';
                break;

            case 'knowledge_base':
                $taxonomy = 'knowledge_base_cat';
                break;

            case 'event':
                $taxonomy = 'event_cat';
                break;

            default:
                $taxonomy = 'category';
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
                                        __('Updated on %s', TEXT_DOMAIN),
                                        esc_html( $modified )
                                    );
                                } else {
                                    // Otherwise show published date
                                    printf(
                                        /* translators: %s: Post date */
                                        __('Published on %s', TEXT_DOMAIN),
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
                                            TEXT_DOMAIN
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
                                <span><?php echo esc_html__('Add to Bookmarks', TEXT_DOMAIN); ?></span>
                            </a>
                        <?php else : ?>
                            <?php
                                $bookmark_ids  = get_field('user_bookmarks', 'user_'.$current_user_id) ?: [];
                                $is_bookmarked = in_array( get_the_ID(), $bookmark_ids, true );
                                $bookmark_icon = $is_bookmarked ? 'bookmark' : 'bookmark-empty';
                                $bookmark_text = $is_bookmarked ? __('Remove form bookmarks', TEXT_DOMAIN) : __('Add to Bookmarks', TEXT_DOMAIN);
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
                        'before' => '<div class="page-links">' . __('Pages:', TEXT_DOMAIN),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>

                <footer class="section__footer">
                    <?php if ( $taxonomy ) : ?>
                        <?php 
                            $taxonomy_obj   = get_taxonomy( $taxonomy ); 
                            $taxonomy_label = $taxonomy_obj ? $taxonomy_obj->labels->name : __('Categories', TEXT_DOMAIN);
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

                    <?php if ( $related_posts->have_posts() ) : ?>
                        <?php
                            $template_args = array('post_type' => esc_attr($post_type));
                            $template      = locate_template("template-parts/cards/card-related.php");
                        ?>
                        <div class="section__related-posts">
                            <h2 class="section__title"><?php _e('You may also be interested in', TEXT_DOMAIN); ?></h2>
                            <div class="slider slider--related" id="related-posts-slider">
                                <div class="slider__list">
                                    <?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>
                                        <div class="slider__item">
                                            <?php
                                                if ( ! empty( $template ) ) {
                                                    get_template_part('template-parts/cards/card', 'related', $template_args);
                                                } else {
                                                    get_template_part('template-parts/cards/card', 'default', $template_args);
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
