<?php get_header(); ?>

<?php
    $taxonomy     = get_query_var('taxonomy');
    $taxonomy_obj = get_taxonomy($taxonomy);
?>

<main class="page page--default">
    <section class="section section--default">
        <div class="container">
            <h1 class="section__title"><?php printf( esc_html__( 'All %s', TEXT_DOMAIN ), esc_html( mb_strtolower($taxonomy_obj->labels->singular_name) ) ); ?></h1>
            <div class="section__content">
                <?php
                    $terms = get_terms([
                        'taxonomy'   => $taxonomy,
                        'hide_empty' => false,
                    ]);
                ?>

                <?php if (!empty($terms) && !is_wp_error($terms)) : ?>
                    <div class="row gy-4">
                        <?php foreach ($terms as $term) : ?>
                            <div class="col-lg-6 col-xl-4">
                            <?php
                                $template_args = [
                                    'taxonomy' => $taxonomy,
                                    'term'     => $term,
                                ];

                                $template_slug = 'template-parts/cards/card-' . $template_args['taxonomy'] . '.php';

                                if ( locate_template($template_slug) ) {
                                    get_template_part( 'template-parts/cards/card', $template_args['taxonomy'], $template_args );
                                } else {
                                    get_template_part( 'template-parts/cards/card', 'term-default', $template_args );
                                }
                            ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p><?php printf( esc_html__( 'No %s found.', TEXT_DOMAIN ), esc_html( mb_strtolower($taxonomy_obj->labels->singular_name) ) ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>