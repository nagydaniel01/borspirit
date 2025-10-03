<?php
$section_classes = build_section_classes($section, 'post_query');

$section_title      = $section['post_query_section_title'] ?? '';
$section_hide_title = $section['post_query_section_hide_title'] ?? false;
$section_slug       = sanitize_title($section_title);
$section_lead       = $section['post_query_section_lead'] ?? '';

$link               = $section['post_query_link'] ?? '';
$slider             = $section['post_query_slider'] ?? '';
$box                = $section['post_query_box'] ?? '';

$url         = $link['url'] ?? '';
$title       = $link['title'] ?? esc_url($url);
$target      = isset($link['target']) && $link['target'] !== '' ? $link['target'] : '_self';
$is_external = is_external_url($url, get_home_url());

$query_args = [
    'post_type'      => $section['post_type'] ?? 'post',
    'orderby'        => $section['orderby'] ?? 'date',
    'order'          => strtoupper($section['order'] ?? 'DESC'),
    'posts_per_page' => (int) ($section['posts_per_page'] ?? get_option('posts_per_page')),
];

/*
// Special override for events
if (($query_args['post_type'] === 'event') && ($query_args['orderby'] === 'date')) {
    $query_args['orderby']  = 'meta_value';
    $query_args['meta_key'] = 'event_start_date';
    $query_args['meta_type'] = 'DATETIME';
}
*/

// Manual selection
if (!empty($section['selection_type']) && $section['selection_type'] === 'manual') {
    if (!empty($section['post']) && $section['post_type'] === 'post') {
        $query_args['post__in'] = array_map(fn($p) => $p->ID, $section['post']);
    }
    /*
    if (!empty($section['event']) && $section['post_type'] === 'event') {
        $query_args['post__in'] = array_map(fn($e) => $e->ID, $section['event']);
    }
    */
}

// Auto selection – taxonomy filters
if (!empty($section['selection_type']) && $section['selection_type'] === 'auto') {
    if (!empty($section['category']) && $section['post_type'] === 'post') {
        $query_args['tax_query'][] = [
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => array_map(fn($t) => $t->term_id, $section['category']),
        ];
    }
    /*
    if (!empty($section['event_cat']) && $section['post_type'] === 'event') {
        $query_args['tax_query'][] = [
            'taxonomy' => 'event_cat',
            'field'    => 'term_id',
            'terms'    => array_map(fn($t) => $t->term_id, $section['event_cat']),
        ];
    }
    */
}

// Meta query with explicit counter + AND relation
if (!empty($section['meta_query']) && is_array($section['meta_query'])) {
    $meta_counter = 0;
    foreach ($section['meta_query'] as $key => $row) {
        if ($meta_counter === 0) {
            $query_args['meta_query'] = ['relation' => 'AND'];
        }
        $query_args['meta_query'][] = [
            'key'     => $row['meta_key'] ?? '',
            'value'   => $row['meta_value'] ?? '',
            'compare' => $row['meta_compare'] ?? '=',
        ];
        $meta_counter++;
    }
}

/*
echo '<pre>';
var_dump($query_args);
echo '</pre>';
*/

$post_query = new WP_Query($query_args);
?>

<?php if ($post_query->have_posts()) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--post_query <?php echo esc_attr($section_classes); ?><?php echo ($slider != false) ? ' section--slider' : ''; ?><?php echo ($box != false) ? ' section--box' : ''; ?>">
        <div class="container">
            <?php if (($section_title && $section_hide_title !== true) || $section_lead) : ?>
                <div class="section__header">
                    <?php if ($section_hide_title !== true) : ?>
                        <h1 class="section__title"><?php echo esc_html($section_title); ?></h1>
                    <?php endif; ?>

                    <?php if ($query_args['post_type'] === 'testimonial') : ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#testimonial_formModal"><?php echo esc_html__('Write a testimonial'); ?></button>
                    <?php endif; ?>
                    
                    <?php if (!empty($url)) : ?>
                        <a href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($target); ?>" <?php echo $is_external ? 'rel="noopener noreferrer"' : ''; ?> class="btn btn-link section__link">
                            <span><?php esc_html_e($title, TEXT_DOMAIN); ?></span>
                            <svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($section_lead)) : ?>
                        <div class="section__lead"><?php echo wp_kses_post($section_lead); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="section__content">
                <?php if ( $slider != false ) : ?>
                    <div class="slider slider--post-query">
                        <div class="slider__list">
                            <?php while ( $post_query->have_posts() ) : $post_query->the_post(); ?>
                                <div class="slider__item">
                                    <?php 
                                        $post_type = get_post_type(); // Get the current post type once
                                        $template_args = array('post_type' => esc_attr($post_type));
                                        $template = locate_template("template-parts/cards/card-{$post_type}.php");

                                        if (!empty($template)) {
                                            // File exists, include it
                                            get_template_part('template-parts/cards/card', $post_type, $template_args);
                                        } else {
                                            // File does not exist, handle accordingly
                                            get_template_part('template-parts/cards/card', 'default', $template_args);
                                        }
                                    ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <div class="slider__controls"></div>
                    </div>
                <?php else : ?>
                    <div class="row gy-4">
                        <?php while ($post_query->have_posts()) : $post_query->the_post(); ?>
                            <?php 
                                $post_type = get_post_type(); // Get the current post type once
                                $template_args = array('post_type' => esc_attr($post_type));
                                $template = locate_template("template-parts/cards/card-{$post_type}.php");
                            ?>

                            <div class="col-lg-6 col-xl-4">
                                <?php
                                    if (!empty($template)) {
                                        // File exists, include it
                                        get_template_part('template-parts/cards/card', $post_type, $template_args);
                                    } else {
                                        // File does not exist, handle accordingly
                                        get_template_part('template-parts/cards/card', 'default', $template_args);
                                    }
                                ?>
                            </div>
                        <?php endwhile; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
