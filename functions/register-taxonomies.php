<?php
	if ( ! function_exists( 'register_award_taxonomy' ) ) {
		/**
		 * Registers a custom taxonomy 'award'.
		 * 
		 * This taxonomy is applied to posts and custom post types.
		 * It is non-hierarchical and has a default term.
		 */
		function register_award_taxonomy() {

			$labels = array(
				'name'                       => _x( 'Díjak', 'Taxonomy General Name', TEXT_DOMAIN ),
				'singular_name'              => _x( 'Díj', 'Taxonomy Singular Name', TEXT_DOMAIN ),
				'menu_name'                  => __( 'Díjak', TEXT_DOMAIN ),
				'all_items'                  => __( 'Összes díj', TEXT_DOMAIN ),
				'parent_item'                => __( 'Szülő díj', TEXT_DOMAIN ),
				'parent_item_colon'          => __( 'Szülő díj:', TEXT_DOMAIN ),
				'new_item_name'              => __( 'Új díj neve', TEXT_DOMAIN ),
				'add_new_item'               => __( 'Új díj hozzáadása', TEXT_DOMAIN ),
				'edit_item'                  => __( 'Díj szerkesztése', TEXT_DOMAIN ),
				'update_item'                => __( 'Díj frissítése', TEXT_DOMAIN ),
				'view_item'                  => __( 'Díj megtekintése', TEXT_DOMAIN ),
				'separate_items_with_commas' => __( 'Díjakat vesszővel válasszon el', TEXT_DOMAIN ),
				'add_or_remove_items'        => __( 'Díj hozzáadása vagy eltávolítása', TEXT_DOMAIN ),
				'choose_from_most_used'      => __( 'A leggyakrabban használt díjak közül válasszon', TEXT_DOMAIN ),
				'popular_items'              => __( 'Népszerű díjak', TEXT_DOMAIN ),
				'search_items'               => __( 'Díjak keresése', TEXT_DOMAIN ),
				'not_found'                  => __( 'Nem található', TEXT_DOMAIN ),
				'no_terms'                   => __( 'Nincs díj', TEXT_DOMAIN ),
				'items_list'                 => __( 'Díjak listája', TEXT_DOMAIN ),
				'items_list_navigation'      => __( 'Díjak listájának navigációja', TEXT_DOMAIN ),
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
