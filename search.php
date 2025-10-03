<?php /** Template Name: Search */ ?>

<?php get_header(); ?>

<?php
    global $wp_query;
    
    $search_query           = get_search_query();
    $post_types             = get_post_types(array( 'public' => true ), 'objects');
    $post_type_filter       = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : null;
    $default_posts_per_page = get_option('posts_per_page');
    $paged                  = max(1, get_query_var('paged', 1));

    // Define your preferred post type order
    $desired_order = array( 'post', 'event', 'announcement', 'researcher', 'project', 'publication', 'podcast', 'video', 'page', 'person' );

    // Sort $post_types according to $desired_order
    uksort( $post_types, function( $a, $b ) use ( $desired_order ) {
        $pos_a = array_search( $a, $desired_order );
        $pos_b = array_search( $b, $desired_order );

        // If not found in $desired_order, send to end
        $pos_a = ($pos_a === false) ? PHP_INT_MAX : $pos_a;
        $pos_b = ($pos_b === false) ? PHP_INT_MAX : $pos_b;

        return $pos_a - $pos_b;
    });

    $pagination_args = [
        'total'                 => $wp_query->max_num_pages,
        'current'               => max(1, get_query_var('paged')),
        'format'                => '?paged=%#%',
        'show_all'              => false,
        'end_size'              => 1,
        'mid_size'              => 2,
        'prev_text'             => sprintf('<span class="page-numbers__label">%s</span><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg>', __('Előző', TEXT_DOMAIN)),
        'next_text'             => sprintf('<span class="page-numbers__label">%s</span><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg>', __('Következő', TEXT_DOMAIN)),
        'type'                  => 'list',
        //'before_page_number'    => '',
        'after_page_number'     => '<span class="visually-hidden"> ' . __('Oldal', TEXT_DOMAIN) . '</span>',
    ];
?>

<main class="page page--archive page--search">
    <section class="section section--archive section--archive-search">
        <div class="section__header">
            <div class="container">
                <h1 class="section__title fs-2"><?php printf( esc_html__('Keresés erre: %s', TEXT_DOMAIN), '<span>' . $search_query . '</span>' ); ?></h1>
            </div>
        </div>

        <div class="section__body">
            <?php if ( have_posts() ) : ?>
                <?php if ( $post_type_filter ) : ?>
                    <?php
                        // Query for only the specific post type defined in URL
                        $query = new WP_Query( array(
                            's'                 => $search_query,
                            'post_type'         => $post_type_filter,
                            'posts_per_page'    => $default_posts_per_page,
                            'paged'             => $paged,
                        ) );
                    ?>

                    <?php if ( is_object($query) && $query->have_posts() ) : ?>
                        <div class="block block--search block--search-<?php echo esc_attr($post_type_filter); ?>">
                            <div class="container">
                                <header class="block__header">
                                    <h2 class="block__title fs-1">
                                        <?php
                                            if ( isset( $post_type_filter ) ) {
                                                $post_type_object = get_post_type_object( $post_type_filter );
                                                if ( $post_type_object ) {
                                                    echo esc_html( $post_type_object->labels->name );
                                                }
                                            }
                                        ?>
                                    </h2>
                                </header>
                                <div class="block__body">
                                    <div class="row g-3">
                                        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                                            <?php 
                                                $post_type = esc_attr(get_post_type());

                                                switch ($post_type) {
                                                    case 'podcast':
                                                        $col_class = 'col-12';
                                                        break;
                                                    default:
                                                        $col_class = 'col-12 col-md-6 col-lg-3';
                                                        break;
                                                }
                                            ?>
                                            <div class="<?php echo $col_class; ?>">
                                                <?php 
                                                    $template_args = array(
                                                        'post_type' => esc_attr(get_post_type())
                                                    );

                                                    $template_slug = 'template-parts/cards/card-' . $template_args['post_type'] . '.php';
                                                    if ( locate_template($template_slug) ) {
                                                        get_template_part( 'template-parts/cards/card', $template_args['post_type'], $template_args );
                                                    } else {
                                                        get_template_part( 'template-parts/cards/card', 'default', $template_args );
                                                    }
                                                ?>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; wp_reset_postdata(); ?>
                <?php else : ?>
                    <?php foreach ( $post_types as $key => $post_type ) : ?>
                        <?php
                            $query = new WP_Query( array(
                                's'                 => $search_query,
                                'post_type'         => $post_type->name,
                                'posts_per_page'    => $posts_per_page,
                            ) );

                            // Total posts found by the query
                            $total_posts = $query->found_posts;
                        ?>

                        <?php if ( is_object($query) && $query->have_posts() ) : ?>
                            <div class="block block--search block--<?php echo esc_attr($post_type->name); ?>">
                                <div class="container">
                                    <header class="block__header">
                                        <h2 class="block__title fs-1">
                                            <?php echo esc_html( get_post_type_object( $post_type->name )->labels->name ); ?>
                                        </h2>
                                    </header>
                                    <div class="block__body">
                                        <div class="row g-3">
                                            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                                                <?php 
                                                    $post_type = esc_attr(get_post_type());

                                                    switch ($post_type) {
                                                        case 'podcast':
                                                            $col_class = 'col-12';
                                                            break;
                                                        default:
                                                            $col_class = 'col-12 col-md-6 col-lg-3';
                                                            break;
                                                    }
                                                ?>
                                                <div class="<?php echo $col_class; ?>">
                                                    <?php 
                                                        $template_args = array(
                                                            'post_type' => esc_attr(get_post_type())
                                                        );

                                                        $template_slug = 'template-parts/cards/card-' . $template_args['post_type'] . '.php';
                                                        if ( locate_template($template_slug) ) {
                                                            get_template_part( 'template-parts/cards/card', $template_args['post_type'], $template_args );
                                                        } else {
                                                            get_template_part( 'template-parts/cards/card', 'default', $template_args );
                                                        }
                                                    ?>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; wp_reset_postdata(); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else : ?>
                <div class="container">
                    <p class="text-center"><?php echo esc_html__('Sajnáljuk, de nem találtunk találatot a keresési feltételek alapján. Kérjük, próbálkozzon újra más kulcsszavakkal.', TEXT_DOMAIN); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>