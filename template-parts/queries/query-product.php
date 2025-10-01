<?php
$post_type_args = array();

$post_type_args['post_type'] = 'product';
$post_type_args['post_status'] = 'publish';

if (!empty($filter_object)) {
    $meta_counter = 0;

    foreach ($filter_object as $filter => $values) {
        if ($filter === 'per_page') {
            $post_type_args['posts_per_page'] = $values;
        } elseif ( $filter === 'offset') {
            $post_type_args['offset'] = $values;
        } elseif ( $filter === 'current_page') {
            $post_type_args['paged'] = $values;
        } elseif ($filter === 'keyword') {
            $post_type_args['s'] = $values;
        } else {
            // Check if this filter is a custom field (ACF) starting with "product_filter_"
            if (strpos($filter, 'product_filter_') === 0) {
                if ($meta_counter === 0) {
                    $post_type_args['meta_query'] = array('relation' => 'AND');
                }

                // Handle array or single value for meta
                $post_type_args['meta_query'][] = array(
                    'key' => $filter,
                    'value' => $values,
                    'compare' => 'IN' // you can change this to =, >, < etc based on need
                );

                $meta_counter++;
            } else {
                // Taxonomy query
                if ($counter === 0) {
                    $post_type_args['tax_query'] = array('relation' => 'AND');
                }

                $post_type_args['tax_query'][] = array(
                    'taxonomy' => $filter,
                    'field' => 'slug',
                    'terms' => $values
                );

                $counter++;
            }
        }
    }
}

$post_type_query = new WP_Query($post_type_args);
?>

<?php if ($post_type_query->have_posts() && !empty($post_type_args)) : ?>
    <?php while ($post_type_query->have_posts()) : $post_type_query->the_post(); ?>
        <?php wc_get_template_part( 'content', 'product' ); ?>
    <?php endwhile; ?>

    <li class="load-more js-load-more" data-max-pages="<?php echo $post_type_query->max_num_pages; ?>">
        <span><?php _e('Load more...', 'lungaugold'); ?></span>
    </li>

    <?php wp_reset_postdata(); ?>
<?php else : ?>
    <p class="no-result"><?php _e('No results', 'lungaugold'); ?></p>
<?php endif; ?>
