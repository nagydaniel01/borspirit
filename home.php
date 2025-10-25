<?php get_header(); ?>

<?php
    global $wp_query;
    
    $show_on_front  = get_option('show_on_front');
    $page_title     = get_the_title();

    if ($show_on_front === 'page') {
        $page_for_posts = get_option('page_for_posts');
        if ($page_for_posts) {
            $page_title = get_the_title($page_for_posts);
            $post_type  = 'post';
        }
    }
    
    $post_type_obj  = get_post_type_object($post_type);
    $posts_per_page = get_option('posts_per_page');

    $categories     = get_categories(['taxonomy' => 'category', 'hide_empty' => true]);
    //$post_tag       = get_terms(['taxonomy' => 'post_tag', 'hide_empty' => true]);

    // Get authors (only those who have published posts)
    $authors = get_users(['who' => 'authors', 'has_published_posts' => ['post'], 'orderby' => 'display_name', 'order' => 'ASC']);
?>

<main class="page page--archive page--archive-<?php echo esc_attr(get_post_type()); ?>">
    <section class="section section--archive section--archive-<?php echo esc_attr(get_post_type()); ?>" data-post-type="<?php echo esc_attr($post_type); ?>" data-posts-per-page="<?php echo esc_attr($posts_per_page); ?>">
        <div class="container">
            <header class="section__header">
                <?php if ( function_exists('rank_math_the_breadcrumbs') ) rank_math_the_breadcrumbs(); ?>

                <div class="section__title-wrapper">
                    <h1 class="section__title"><?php echo esc_html($page_title); ?></h1>
                    <input type="text" name="filter-search" id="filter-search" class="form-control filter filter--search js-filter-search" placeholder="<?php echo esc_attr(sprintf(__('Search for %s', 'borspirit'), strtolower($post_type_obj->labels->name))); ?>" >
                </div>

                <div class="section__toolbar">
                    <div class="row mt-3"> <!-- Bootstrap row wrapper -->
                        <?php if (!empty($categories) && is_array($categories)) : ?>
                            <div class="col-md-4 mb-3">
                                <fieldset id="filter-categories">
                                    <legend>
                                        <?php 
                                            $filter_label = __('Categories', 'borspirit');
                                            echo esc_html(sprintf(__('Filter by %s', 'borspirit'), strtolower($filter_label)));
                                        ?>
                                    </legend>
    
                                    <?php 
                                        $selected_categories = (array) get_query_var('category_filter');
                                    ?>
    
                                    <?php foreach ($categories as $category) : ?>
                                        <div class="form-check">
                                            <input type="checkbox" name="category[]" value="<?php echo esc_attr($category->slug); ?>" id="category-<?php echo esc_attr($category->slug); ?>" class="form-check-input filter js-filter js-filter-default" data-filter="category" <?php checked(in_array($category->slug, $selected_categories, true)); ?>>
                                            <label class="form-check-label" for="category-<?php echo esc_attr($category->slug); ?>">
                                                <?php echo esc_html($category->name); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </fieldset>
                            </div>
                        <?php endif; ?>
    
                        <?php if (!empty($authors) && count($authors) > 1) : ?>
                            <div class="col-md-4 mb-3">
                                <?php $filter_label = __('Authors', 'borspirit'); ?>
                                <select name="author[]" multiple="multiple" id="filter-author" class="form-select filter js-filter js-filter-default" data-filter="author" data-placeholder="<?php echo esc_attr(sprintf(__('Filter by %s', 'borspirit'), strtolower($filter_label))); ?>">
                                    <?php foreach ($authors as $key => $author) : ?>
                                        <option value="<?php echo esc_attr($author->ID); ?>" <?php selected(get_query_var('author_filter'), $author->ID); ?>>
                                            <?php echo esc_html($author->display_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div> <!-- end row -->
                </div>
            </header>
            
            <div class="section__body">
                <div id="post-list" class="section__content">
                    <?php 
                        $template_args = [
                            'post_type' => esc_attr($post_type)
                        ];
                        get_template_part( 'template-parts/queries/query', 'post-type', $template_args );
                    ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>