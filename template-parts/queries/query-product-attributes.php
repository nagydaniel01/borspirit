<?php
// Get WooCommerce product attributes
$product_attributes = wc_get_attribute_taxonomies();

// Query all published products (IDs only)
$args = [
    'post_type'              => 'product',
    'post_status'            => 'publish',
    'posts_per_page'         => -1,
    'no_found_rows'          => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,
    'fields'                 => 'ids',
];

$query  = new WP_Query($args);
$posts  = $query->have_posts() ? $query->posts : [];
$meta   = [];

// Collect ACF field values from posts
if ($posts) {
    foreach ($posts as $post_id) {
        $fields = get_field_objects($post_id);

        if (!$fields) {
            continue;
        }

        foreach ($fields as $field) {
            $type  = $field['type'];
            $name  = $field['name'];
            $value = $field['value'];

            if (in_array($type, ['text', 'number', 'date_picker'], true)) {
                if (!isset($meta[$name])) {
                    $meta[$name] = [];
                }
                if ($value !== null && !in_array($value, $meta[$name], true)) {
                    $meta[$name][] = $value;
                }
                sort($meta[$name]);
            }
        }
    }
}

// Helper to render a taxonomy filter
function render_taxonomy_filter($taxonomy, $label = '') {
    $terms = (new WP_Term_Query(['taxonomy' => $taxonomy]))->terms ?? [];
    if (empty($terms)) {
        return;
    }

    if (!$label) {
        $taxonomy_obj = get_taxonomy($taxonomy);
        $label = $taxonomy_obj ? $taxonomy_obj->labels->singular_name : ucfirst($taxonomy);
    }
    ?>
    <div id="filter-<?php echo esc_attr($taxonomy); ?>"
         class="filter filter--taxonomy js-filter"
         data-filter="<?php echo esc_attr($taxonomy); ?>">
        <h3 class="filter__title"><?php echo esc_html($label); ?></h3>
        <div class="filter__body">
            <ul class="filter__list">
                <?php foreach ($terms as $term) : ?>
                    <li class="filter__item js-filter-item" data-value="<?php echo esc_attr($term->slug); ?>">
                        <button type="button" class="filter__link js-filter-link">
                            <span class="filter__text"><?php echo sprintf('%s (%d)', esc_html($term->name), intval($term->count)); ?></span>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php
}
?>

<?php
// Render product categories
render_taxonomy_filter('product_cat', __('Categories', 'vince'));

// Render product tags
render_taxonomy_filter('product_tag', __('Tags', 'vince'));
?>

<?php if ($product_attributes) : ?>
    <?php foreach ($product_attributes as $attribute) : ?>
        <?php
        $taxonomy = 'pa_' . $attribute->attribute_name;
        $terms    = (new WP_Term_Query(['taxonomy' => $taxonomy]))->terms ?? [];
        ?>

        <?php if (!empty($terms)) : ?>
            <div id="filter-<?php echo esc_attr($attribute->attribute_name); ?>"
                 class="filter filter--products js-filter"
                 data-filter="<?php echo esc_attr($taxonomy); ?>">
                <h3 class="filter__title"><?php echo esc_html($attribute->attribute_label); ?></h3>
                <div class="filter__body">
                    <ul class="filter__list">
                        <?php foreach ($terms as $term) : ?>
                            <li class="filter__item js-filter-item" data-value="<?php echo esc_attr($term->slug); ?>">
                                <button type="button" class="filter__link js-filter-link">
                                    <span class="filter__text"><?php echo sprintf('%s (%d)', esc_html($term->name), intval($term->count)); ?></span>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($fields)) : ?>
    <?php $tab_counter = 0; ?>

    <?php foreach ($fields as $field) : ?>
        <?php
        $type  = $field['type'];
        $name  = $field['name'];
        $label = $field['label'] ?? '';

        // Check if this filter is a custom field (ACF) starting with "product_filter_"
        if (strpos($name, 'product_filter_') !== 0) {
            continue;
        }

        if ($type === 'tab') {
            $tab_counter++;
            continue;
        }

        if ($tab_counter >= 2) {
            continue;
        }

        // Build field choices
        $choices = [];
        if (in_array($type, ['radio', 'checkbox'], true)) {
            $choices = $field['choices'] ?? [];
        } elseif (in_array($type, ['text', 'number', 'date_picker'], true) && isset($meta[$name])) {
            $choices = $meta[$name];
        }
        ?>
        
        <div id="filter-<?php echo esc_attr($name); ?>"
             class="filter js-filter"
             data-filter="<?php echo esc_attr($name); ?>">
            <h3 class="filter__title"><?php echo esc_html($label); ?></h3>
            <div class="filter__body">
                <?php if (!empty($choices)) : ?>
                    <ul class="filter__list">
                        <?php foreach ($choices as $choice) : ?>
                            <li class="filter__item js-filter-item" data-value="<?php echo esc_attr($choice); ?>">
                                <a class="filter__link js-filter-link">
                                    <span class="filter__text"><?php echo esc_html($choice); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
