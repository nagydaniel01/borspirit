<?php
    defined( 'ABSPATH' ) || exit;
    
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
                'name'                  => _x( 'Gyakori kérdések', 'Post Type General Name', 'borspirit' ),
                'singular_name'         => _x( 'Gyakori kérdés', 'Post Type Singular Name', 'borspirit' ),
                'menu_name'             => __( 'Gyakori kérdések', 'borspirit' ),
                'name_admin_bar'        => __( 'Gyakori kérdés', 'borspirit' ),
                'archives'              => __( 'Gyakori kérdések', 'borspirit' ),
                'attributes'            => __( 'Gyakori kérdés attribútumok', 'borspirit' ),
                'parent_item_colon'     => __( 'Szülő gyakori kérdés:', 'borspirit' ),
                'all_items'             => __( 'Összes gyakori kérdés', 'borspirit' ),
                'add_new_item'          => __( 'Új gyakori kérdés hozzáadása', 'borspirit' ),
                'add_new'               => __( 'Új gyakori kérdés', 'borspirit' ),
                'new_item'              => __( 'Új gyakori kérdés', 'borspirit' ),
                'edit_item'             => __( 'Gyakori kérdés szerkesztése', 'borspirit' ),
                'update_item'           => __( 'Gyakori kérdés frissítése', 'borspirit' ),
                'view_item'             => __( 'Gyakori kérdés megtekintése', 'borspirit' ),
                'view_items'            => __( 'Gyakori kérdések megtekintése', 'borspirit' ),
                'search_items'          => __( 'Gyakori kérdések keresése', 'borspirit' ),
                'not_found'             => __( 'Nincs gyakori kérdés találat', 'borspirit' ),
                'not_found_in_trash'    => __( 'Nincs gyakori kérdés a kukában', 'borspirit' ),
                'featured_image'        => __( 'Kiemelt kép', 'borspirit' ),
                'set_featured_image'    => __( 'Kiemelt kép beállítása', 'borspirit' ),
                'remove_featured_image' => __( 'Kiemelt kép eltávolítása', 'borspirit' ),
                'use_featured_image'    => __( 'Kiemelt képként használ', 'borspirit' ),
                'insert_into_item'      => __( 'Beszúrás a gyakori kérdésbe', 'borspirit' ),
                'uploaded_to_this_item' => __( 'Feltöltve ehhez a gyakori kérdéshez', 'borspirit' ),
                'items_list'            => __( 'Gyakori kérdések listája', 'borspirit' ),
                'items_list_navigation' => __( 'Gyakori kérdések lista navigáció', 'borspirit' ),
                'filter_items_list'     => __( 'Gyakori kérdések lista szűrése', 'borspirit' ),
            );

            $args = array(
                'label'                 => __( 'Gyakori kérdés', 'borspirit' ),
                'description'           => __( '', 'borspirit' ),
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
