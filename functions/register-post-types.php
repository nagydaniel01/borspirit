<?php
    if ( ! function_exists( 'register_faq_post_type' ) ) {
        /**
         * Registers the "Gyakori kérdések" custom post type.
         *
         * Slug: faq
         * Icon: dashicons-editor-help
         *
         * This post type is used for Frequently Asked Questions.
         * It is private (not publicly queryable), but available in the admin UI.
         *
         * @return void
         */
        function register_faq_post_type() {
            $labels = array(
                'name'                  => _x( 'Gyakori kérdések', 'Post Type General Name', TEXT_DOMAIN ),
                'singular_name'         => _x( 'Gyakori kérdés', 'Post Type Singular Name', TEXT_DOMAIN ),
                'menu_name'             => __( 'Gyakori kérdések', TEXT_DOMAIN ),
                'name_admin_bar'        => __( 'Gyakori kérdés', TEXT_DOMAIN ),
                'archives'              => __( 'Gyakori kérdések', TEXT_DOMAIN ),
                'attributes'            => __( 'Gyakori kérdés attribútumok', TEXT_DOMAIN ),
                'parent_item_colon'     => __( 'Szülő gyakori kérdés:', TEXT_DOMAIN ),
                'all_items'             => __( 'Összes gyakori kérdés', TEXT_DOMAIN ),
                'add_new_item'          => __( 'Új gyakori kérdés hozzáadása', TEXT_DOMAIN ),
                'add_new'               => __( 'Új gyakori kérdés', TEXT_DOMAIN ),
                'new_item'              => __( 'Új gyakori kérdés', TEXT_DOMAIN ),
                'edit_item'             => __( 'Gyakori kérdés szerkesztése', TEXT_DOMAIN ),
                'update_item'           => __( 'Gyakori kérdés frissítése', TEXT_DOMAIN ),
                'view_item'             => __( 'Gyakori kérdés megtekintése', TEXT_DOMAIN ),
                'view_items'            => __( 'Gyakori kérdések megtekintése', TEXT_DOMAIN ),
                'search_items'          => __( 'Gyakori kérdések keresése', TEXT_DOMAIN ),
                'not_found'             => __( 'Nincs gyakori kérdés találat', TEXT_DOMAIN ),
                'not_found_in_trash'    => __( 'Nincs gyakori kérdés a kukában', TEXT_DOMAIN ),
                'featured_image'        => __( 'Kiemelt kép', TEXT_DOMAIN ),
                'set_featured_image'    => __( 'Kiemelt kép beállítása', TEXT_DOMAIN ),
                'remove_featured_image' => __( 'Kiemelt kép eltávolítása', TEXT_DOMAIN ),
                'use_featured_image'    => __( 'Kiemelt képként használ', TEXT_DOMAIN ),
                'insert_into_item'      => __( 'Beszúrás a gyakori kérdésbe', TEXT_DOMAIN ),
                'uploaded_to_this_item' => __( 'Feltöltve ehhez a gyakori kérdéshez', TEXT_DOMAIN ),
                'items_list'            => __( 'Gyakori kérdések listája', TEXT_DOMAIN ),
                'items_list_navigation' => __( 'Gyakori kérdések lista navigáció', TEXT_DOMAIN ),
                'filter_items_list'     => __( 'Gyakori kérdések lista szűrése', TEXT_DOMAIN ),
            );

            $args = array(
                'label'                 => __( 'Gyakori kérdés', TEXT_DOMAIN ),
                'description'           => __( '', TEXT_DOMAIN ),
                'labels'                => $labels,
                'supports'              => array( 'title', 'editor' ),
                'taxonomies'            => array(),
                'hierarchical'          => false,
                'public'                => false,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'menu_position'         => 15,
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'capability_type'       => 'post',
                'rewrite'               => array(),
                'menu_icon'             => 'dashicons-editor-help',
            );

            register_post_type( 'faq', $args );
        }
        add_action( 'init', 'register_faq_post_type', 0 );
    }
