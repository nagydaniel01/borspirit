<?php
$post_type          = $args['post_type'] ?? '';
$event_type         = $args['event_type'] ?? '';
$post_ids           = $args['post_ids'] ?? '';

$current_date_time  = current_time('Y-m-d H:i:s');
$paged              = max(1, get_query_var('paged', 1));
$post_type_args     = [];

// =========================
// Basic post type args
// =========================
if (!empty($post_type)) {
    $post_type_args['post_type'] = $post_type;
    $post_type_args['post_status'] = 'publish';

    if ($post_type == 'service') {
        $post_type_args['orderby'] = 'name';
        $post_type_args['order'] = 'ASC';
    } elseif ($post_type == 'event') {
        $post_type_args['orderby'] = 'meta_value';
        $post_type_args['meta_key'] = 'event_start_date';

        if ($event_type == 'upcoming') {
            $post_type_args['meta_query'][] = array(
                'key' => 'event_end_date',
                'value' => $current_date_time,
                'compare' => '>=',
                'type' => 'DATETIME'
            );
            $post_type_args['order'] = 'ASC';
        } elseif ($event_type == 'past') {
            $post_type_args['meta_query'][] = array(
                'key' => 'event_end_date',
                'value' => $current_date_time,
                'compare' => '<',
                'type' => 'DATETIME'
            );
            $post_type_args['order'] = 'DESC';
        }
    }
    else {
        $post_type_args['orderby'] = 'date';
        $post_type_args['order'] = 'DESC';
    }
}

// =========================
// Handle apply_profile_filters pre-filter
// =========================
/*
$apply_profile_filters = !empty($filter_object['apply_profile_filters']) && isset($filter_object['apply_profile_filters'][0]) && $filter_object['apply_profile_filters'][0] === '1';
$current_user_id       = get_current_user_id();

if ($apply_profile_filters && $current_user_id) {
    $user_meta_keys = ['szintipus']; // Add more ACF user fields here

    foreach ($user_meta_keys as $meta_key) {
        $user_value = get_user_meta($current_user_id, $meta_key, true);

        if (!empty($user_value)) {
            if (!isset($post_type_args['meta_query'])) {
                $post_type_args['meta_query'] = ['relation' => 'AND'];
            }

            $post_type_args['meta_query'][] = [
                'key'     => $meta_key,
                'value'   => sanitize_text_field($user_value),
                'compare' => '=', // or 'LIKE' if serialized
            ];
        }
    }
}
*/

// =========================
// Remove apply_profile_filters from other filters
// =========================
/*
if (!empty($filter_object) && isset($filter_object['apply_profile_filters'])) {
    unset($filter_object['apply_profile_filters']);
}
*/

// =========================
// Detect Bookmark query
// =========================
if ($post_ids) {
    $post_type_args['post__in'] = $post_ids;
}

// =========================
// Process other filters
// =========================
if (!empty($filter_object)) {
    $tax_counter = 0;
    $meta_counter = 0;

    foreach ($filter_object as $filter => $values) {
        switch ($filter) {
            case 'per_page':
                $post_type_args['posts_per_page'] = $values;
                break;

            case 'offset':
                $post_type_args['offset'] = $values;
                break;

            case 'current_page':
                $post_type_args['paged'] = $values;
                break;

            case 'keyword':
                $post_type_args['s'] = $values;
                break;

            case 'author':
                $post_type_args['author'] = intval($values);
                break;

            case 'alphabet':
                $post_type_args['alphabet'] = $values;
                break;

            case 'post_date':
                $post_type_args['year'] = $values;
                break;

            default:
                // Check if filter is a registered taxonomy
                if (taxonomy_exists($filter)) {
                    $options = array(
                        'taxonomy' => $filter,
                        'field'    => 'slug',
                        'terms'    => $values,
                    );

                    if ($tax_counter === 0) {
                        $post_type_args['tax_query'] = array('relation' => 'AND');
                    }

                    $post_type_args['tax_query'][] = $options;
                    $tax_counter++;
                } else {
                    // Make sure $values is not empty
                    $value = is_array($values) ? $values : [$values];
                    // remove empty strings
                    $value = array_filter($value);

                    // Treat as meta_query
                    if ($meta_counter === 0) {
                        $post_type_args['meta_query'] = array('relation' => 'AND');
                    }

                    $post_type_args['meta_query'][] = array(
                        'key'     => $filter,
                        'value'   => array_map('sanitize_text_field', $value),
                        'compare' => 'IN',
                    );
                    $meta_counter++;
                }
                break;
        }
    }
}

/*
echo '<pre>';
var_dump($post_type_args);
echo '</pre>';
*/

$post_type_query = new WP_Query($post_type_args);
?>

<?php if (is_object($post_type_query) && $post_type_query->have_posts()) : ?>
    <div class="row gy-4">
        <?php while ($post_type_query->have_posts()) : $post_type_query->the_post(); ?>
            <?php 
                $post_type = get_post_type(); // Get the current post type once
                $template_args = array('post_type' => esc_attr($post_type));
                $template = locate_template("template-parts/cards/card-{$post_type}.php");

                switch ($post_type) {
                    case 'event':
                        ?>

                        <div class="col-12">
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

                        <?php
                        break;
                    
                    default:
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

                        <?php
                        break;
                }
            ?>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    </div>

    <?php
        // posts to display per page
        $posts_per_page = (int) $post_type_query->query_vars['posts_per_page'];
        // current page
        $current_page = (int) $post_type_query->query_vars['paged'];
        // the overall amount of pages
        $max_page = $post_type_query->max_num_pages;

        // we don't have to display pagination or load more button in this case
        if( $max_page <= 1 ) return;

        // set the current page to 1 if not exists
        if( empty( $current_page ) || $current_page == 0) $current_page = 1;

        // how much links to display in pagination
        $links_in_the_middle = 3;
        $links_in_the_middle_minus_1 = $links_in_the_middle-1;

        // required to display the pagination properly for large amount of pages
        $first_link_in_the_middle = $current_page - floor( $links_in_the_middle_minus_1/2 );
        $last_link_in_the_middle = $current_page + ceil( $links_in_the_middle_minus_1/2 );

        // some calculations with $first_link_in_the_middle and $last_link_in_the_middle
        if( $first_link_in_the_middle <= 0 ) $first_link_in_the_middle = 1;
        if( ( $last_link_in_the_middle - $first_link_in_the_middle ) != $links_in_the_middle_minus_1 ) { $last_link_in_the_middle = $first_link_in_the_middle + $links_in_the_middle_minus_1; }
        if( $last_link_in_the_middle > $max_page ) { $first_link_in_the_middle = $max_page - $links_in_the_middle_minus_1; $last_link_in_the_middle = (int) $max_page; }
        if( $first_link_in_the_middle <= 0 ) $first_link_in_the_middle = 1;
    ?>
    <div class="load-more js-load-more">
        <nav class="section__pagination pagination js-pagination" role="navigation" data-max-pages="<?php echo esc_attr($post_type_query->max_num_pages); ?>">
            <ul class="page-numbers">
                <?php if ($current_page > 1) : ?>
                    <li>
                        <a href="" data-number="1" class="page-numbers first js-pagination-link">
                            <span class="page-numbers__label"><?php _e('Első', TEXT_DOMAIN); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($current_page != 1) : ?>
                    <li>
                        <a href="" class="page-numbers prev js-pagination-link">
                            <svg class="icon icon-chevron-left"><use xlink:href="#icon-chevron-left"></use></svg>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($first_link_in_the_middle >= 3 && $links_in_the_middle < $max_page) : ?>
                    <li>
                        <a href="" data-number="1" class="page-numbers number js-pagination-link">1</a>
                    </li>
    
                    <?php if( $first_link_in_the_middle != 2 ) : ?>
                        <li>
                            <span class="page-numbers dots">...</span>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $first_link_in_the_middle; $i <= $last_link_in_the_middle; $i++) : ?>
                    <?php if ($i == $current_page) : ?>
                        <li>
                            <span aria-current="page" class="page-numbers current"><?php echo esc_html($i); ?></span>
                        </li>
                    <?php else : ?>
                        <li>
                            <a href="" data-number="<?php echo esc_attr($i); ?>" class="page-numbers number js-pagination-link"><?php echo esc_html($i); ?></a>
                        </li>
                    <?php endif; ?>
                <?php endfor; ?>
    
                <?php if ( $last_link_in_the_middle < $max_page ) : ?>
                    <?php if ( $last_link_in_the_middle != ($max_page-1) ) : ?>
                        <li>
                            <span class="page-numbers dots">...</span>
                        </li>
                    <?php endif; ?>
                    
                    <li>
                        <a href="" data-number="<?php echo esc_attr($max_page); ?>" class="page-numbers number js-pagination-link"><?php echo esc_html($max_page); ?></a>
                    </li>
                <?php endif; ?>
                
                <?php if ($current_page != $last_link_in_the_middle ) : ?>
                    <li>
                        <a href="" class="page-numbers next js-pagination-link">
                            <svg class="icon icon-chevron-right"><use xlink:href="#icon-chevron-right"></use></svg>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($current_page < $max_page) : ?>
                    <li>
                        <a href="" data-number="<?php echo $max_page; ?>" class="page-numbers last js-pagination-link">
                            <span class="page-numbers__label"><?php _e('Utolsó', TEXT_DOMAIN); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
<?php else : ?>
    <p class="no-result"><?php echo esc_html__('Sorry, no posts matched your criteria.', TEXT_DOMAIN); ?></p>
<?php endif; ?>
