<?php
    defined( 'ABSPATH' ) || exit;
	
	if ( ! function_exists( 'register_award_taxonomy' ) ) {
		/**
		 * Registers a custom taxonomy 'award'.
		 * 
		 * This taxonomy is applied to posts and custom post types.
		 * It is non-hierarchical and has a default term.
		 */
		function register_award_taxonomy() {

			$labels = array(
				'name'                       => _x( 'Awards', 'Taxonomy General Name', 'borspirit' ),
				'singular_name'              => _x( 'Award', 'Taxonomy Singular Name', 'borspirit' ),
				'menu_name'                  => __( 'Awards', 'borspirit' ),
				'all_items'                  => __( 'All Awards', 'borspirit' ),
				'parent_item'                => __( 'Parent Award', 'borspirit' ),
				'parent_item_colon'          => __( 'Parent Award:', 'borspirit' ),
				'new_item_name'              => __( 'New Award Name', 'borspirit' ),
				'add_new_item'               => __( 'Add New Award', 'borspirit' ),
				'edit_item'                  => __( 'Edit Award', 'borspirit' ),
				'update_item'                => __( 'Update Award', 'borspirit' ),
				'view_item'                  => __( 'View Award', 'borspirit' ),
				'separate_items_with_commas' => __( 'Separate awards with commas', 'borspirit' ),
				'add_or_remove_items'        => __( 'Add or remove awards', 'borspirit' ),
				'choose_from_most_used'      => __( 'Choose from the most used awards', 'borspirit' ),
				'popular_items'              => __( 'Popular Awards', 'borspirit' ),
				'search_items'               => __( 'Search Awards', 'borspirit' ),
				'not_found'                  => __( 'Not found', 'borspirit' ),
				'no_terms'                   => __( 'No awards', 'borspirit' ),
				'items_list'                 => __( 'Awards list', 'borspirit' ),
				'items_list_navigation'      => __( 'Awards list navigation', 'borspirit' ),
			);

			$rewrite = array(
				'slug'                       => 'award',
				'with_front'                 => true,
				'hierarchical'               => false,
			);

			$default_term = array(
				'name'        => 'Other',
				'slug'        => 'other',
				'description' => '',
			);

			$args = array(
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'show_tagcloud'     => true,
				'rewrite'           => $rewrite,
				//'default_term'      => $default_term,
			);

			// Attach to 'product' post type
			register_taxonomy( 'award', array( 'product' ), $args );
		}

		add_action( 'init', 'register_award_taxonomy', 0 );
	}
