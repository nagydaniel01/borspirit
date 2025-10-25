<?php
    $current_user_id = get_current_user_id();

    // Ensure the user is logged in
    if ( ! $current_user_id ) {
        wp_send_json_error([
            'message' => __('You must be logged in.', 'borspirit')
        ], 401);
    }

    // Get existing bookmarks from user meta
    $bookmarks = get_user_meta($current_user_id, 'user_bookmarks', true);
    if ( ! is_array($bookmarks) || empty($bookmarks) ) {
        echo wpautop( __('No bookmarks found.', 'borspirit') );
        return; // Stop here to avoid running the query
    }

    $query_args = [
        'post_type'      => ['post'],
        'post__in'       => $bookmarks,
        'orderby'        => 'post__in',
        'posts_per_page' => -1
    ];

    $post_query = new WP_Query($query_args);
?>

<?php if ($post_query->have_posts()) : ?>
    <div class="row gy-4">
        <?php while ( $post_query->have_posts() ) : $post_query->the_post(); ?>
            <div class="col-lg-6 col-xl-4">
                <?php
                    $post_type = get_post_type();
                    $template_args = [ 'post_type' => esc_attr($post_type) ];

                    $template_slug = "template-parts/cards/card-{$post_type}.php";

                    if ( locate_template( $template_slug ) ) {
                        get_template_part( 'template-parts/cards/card', $post_type, $template_args );
                    } else {
                        get_template_part( 'template-parts/cards/card', 'default', $template_args );
                    }
                ?>
            </div>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    </div>
<?php endif; ?>
