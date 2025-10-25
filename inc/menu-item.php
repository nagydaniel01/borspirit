<?php
    /**
     * Add "Enable Mega Menu" checkbox to WordPress menu items in admin.
     */

    if ( ! function_exists( 'add_mega_menu_checkbox_to_menu_item' ) ) {

        /**
         * Display a checkbox for "Enable Mega Menu" on each menu item in admin.
         *
         * Hooks into 'wp_nav_menu_item_custom_fields'.
         *
         * @param int $item_id The ID of the menu item.
         * @param WP_Post $item The menu item object.
         * @param int $depth Depth of menu item. Used for padding.
         * @param stdClass $args Menu arguments.
         */
        function add_mega_menu_checkbox_to_menu_item( $item_id, $item, $depth, $args ) {
            $is_mega = get_post_meta( $item_id, '_is_mega_menu', true );
            ?>
            <p class="description description-wide">
                <label for="edit-menu-item-is-mega-<?php echo esc_attr( $item_id ); ?>">
                    <input type="checkbox" id="edit-menu-item-is-mega-<?php echo esc_attr( $item_id ); ?>"
                        name="menu-item-is-mega[<?php echo esc_attr( $item_id ); ?>]"
                        value="1" <?php checked( $is_mega, '1' ); ?> />
                    <?php esc_html_e( 'Enable Mega Menu', 'borspirit' ); ?>
                </label>
            </p>
            <?php
        }

        add_action( 'wp_nav_menu_item_custom_fields', 'add_mega_menu_checkbox_to_menu_item', 10, 4 );
    }


    if ( ! function_exists( 'save_mega_menu_checkbox_value' ) ) {

        /**
         * Save the value of the "Enable Mega Menu" checkbox when updating a menu item.
         *
         * Hooks into 'wp_update_nav_menu_item'.
         *
         * @param int $menu_id The ID of the menu being saved.
         * @param int $menu_item_db_id The ID of the menu item being saved.
         */
        function save_mega_menu_checkbox_value( $menu_id, $menu_item_db_id ) {
            if ( isset( $_POST['menu-item-is-mega'][ $menu_item_db_id ] ) ) {
                update_post_meta( $menu_item_db_id, '_is_mega_menu', '1' );
            } else {
                delete_post_meta( $menu_item_db_id, '_is_mega_menu' );
            }
        }

        add_action( 'wp_update_nav_menu_item', 'save_mega_menu_checkbox_value', 10, 2 );
    }
