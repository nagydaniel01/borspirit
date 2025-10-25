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
				'name'                       => _x( 'Díjak', 'Taxonomy General Name', 'borspirit' ),
				'singular_name'              => _x( 'Díj', 'Taxonomy Singular Name', 'borspirit' ),
				'menu_name'                  => __( 'Díjak', 'borspirit' ),
				'all_items'                  => __( 'Összes díj', 'borspirit' ),
				'parent_item'                => __( 'Szülő díj', 'borspirit' ),
				'parent_item_colon'          => __( 'Szülő díj:', 'borspirit' ),
				'new_item_name'              => __( 'Új díj neve', 'borspirit' ),
				'add_new_item'               => __( 'Új díj hozzáadása', 'borspirit' ),
				'edit_item'                  => __( 'Díj szerkesztése', 'borspirit' ),
				'update_item'                => __( 'Díj frissítése', 'borspirit' ),
				'view_item'                  => __( 'Díj megtekintése', 'borspirit' ),
				'separate_items_with_commas' => __( 'Díjakat vesszővel válasszon el', 'borspirit' ),
				'add_or_remove_items'        => __( 'Díj hozzáadása vagy eltávolítása', 'borspirit' ),
				'choose_from_most_used'      => __( 'A leggyakrabban használt díjak közül válasszon', 'borspirit' ),
				'popular_items'              => __( 'Népszerű díjak', 'borspirit' ),
				'search_items'               => __( 'Díjak keresése', 'borspirit' ),
				'not_found'                  => __( 'Nem található', 'borspirit' ),
				'no_terms'                   => __( 'Nincs díj', 'borspirit' ),
				'items_list'                 => __( 'Díjak listája', 'borspirit' ),
				'items_list_navigation'      => __( 'Díjak listájának navigációja', 'borspirit' ),
			);

			$rewrite = array(
				'slug'                       => 'award',
				'with_front'                 => true,
				'hierarchical'               => false,
			);

			$default_term = array(
				'name'        => 'Egyéb',
				'slug'        => 'egyeb',
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
