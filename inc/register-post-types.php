<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    
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
                'name'                  => _x( 'Frequently Asked Questions', 'Post Type General Name', 'borspirit' ),
                'singular_name'         => _x( 'FAQ', 'Post Type Singular Name', 'borspirit' ),
                'menu_name'             => __( 'Frequently Asked Questions', 'borspirit' ),
                'name_admin_bar'        => __( 'FAQ', 'borspirit' ),
                'archives'              => __( 'FAQ Archives', 'borspirit' ),
                'attributes'            => __( 'FAQ Attributes', 'borspirit' ),
                'parent_item_colon'     => __( 'Parent FAQ:', 'borspirit' ),
                'all_items'             => __( 'All FAQs', 'borspirit' ),
                'add_new_item'          => __( 'Add New FAQ', 'borspirit' ),
                'add_new'               => __( 'Add New FAQ', 'borspirit' ),
                'new_item'              => __( 'New FAQ', 'borspirit' ),
                'edit_item'             => __( 'Edit FAQ', 'borspirit' ),
                'update_item'           => __( 'Update FAQ', 'borspirit' ),
                'view_item'             => __( 'View FAQ', 'borspirit' ),
                'view_items'            => __( 'View FAQs', 'borspirit' ),
                'search_items'          => __( 'Search FAQs', 'borspirit' ),
                'not_found'             => __( 'No FAQs found', 'borspirit' ),
                'not_found_in_trash'    => __( 'No FAQs found in Trash', 'borspirit' ),
                'featured_image'        => __( 'Featured Image', 'borspirit' ),
                'set_featured_image'    => __( 'Set featured image', 'borspirit' ),
                'remove_featured_image' => __( 'Remove featured image', 'borspirit' ),
                'use_featured_image'    => __( 'Use as featured image', 'borspirit' ),
                'insert_into_item'      => __( 'Insert into FAQ', 'borspirit' ),
                'uploaded_to_this_item' => __( 'Uploaded to this FAQ', 'borspirit' ),
                'items_list'            => __( 'FAQ list', 'borspirit' ),
                'items_list_navigation' => __( 'FAQ list navigation', 'borspirit' ),
                'filter_items_list'     => __( 'Filter FAQ list', 'borspirit' ),
            );

            $args = array(
                'label'                 => __( 'FAQ', 'borspirit' ),
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
