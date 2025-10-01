<?php
// Add a checkbox to each menu item in admin
add_action('wp_nav_menu_item_custom_fields', function($item_id, $item, $depth, $args) {
    $is_mega = get_post_meta($item_id, '_is_mega_menu', true);
    ?>
    <p class="description description-wide">
        <label for="edit-menu-item-is-mega-<?php echo $item_id; ?>">
            <input type="checkbox" id="edit-menu-item-is-mega-<?php echo $item_id; ?>" 
                   name="menu-item-is-mega[<?php echo $item_id; ?>]" 
                   value="1" <?php checked($is_mega, '1'); ?> />
            <?php esc_html_e('Enable Mega Menu', TEXT_DOMAIN); ?>
        </label>
    </p>
    <?php
}, 10, 4);

// Save the checkbox value
add_action('wp_update_nav_menu_item', function($menu_id, $menu_item_db_id) {
    if (isset($_POST['menu-item-is-mega'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_is_mega_menu', '1');
    } else {
        delete_post_meta($menu_item_db_id, '_is_mega_menu');
    }
}, 10, 2);