<?php
if ( ! function_exists('dynamic_wineries_menu') ) {
    /**
     * Dynamically adds Hungarian and foreign wineries under "Borászatok" tabs:
     * - Magyar (menu-item-644)
     * - Külföldi (menu-item-645)
     */
    function dynamic_wineries_menu( $items, $args ) {
        $theme_location = 'primary_menu'; // Adjust to your theme location
        $hungarian_parent_id = 644;       // "Magyar" tab ID
        $foreign_parent_id   = 645;       // "Külföldi" tab ID

        if ( $args->theme_location !== $theme_location ) {
            return $items;
        }

        // Helper function to fetch wineries by country condition
        $get_wineries = function( $is_hungarian = true ) {
            // Base query for products
            $tax_query = [
                [
                    'taxonomy' => 'pa_orszag',
                    'field'    => 'name',
                    'terms'    => ['Magyarország'],
                    'operator' => $is_hungarian ? 'IN' : 'NOT IN',
                ],
            ];

            $products = get_posts([
                'post_type'      => 'product',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'tax_query'      => $tax_query,
            ]);

            if ( empty($products) ) {
                return [];
            }

            $terms = wp_get_object_terms($products, 'pa_boraszat', ['fields' => 'all']);
            return array_unique($terms, SORT_REGULAR);
        };

        // Hungarian wineries
        $hungarian_terms = $get_wineries(true);
        // Foreign wineries
        $foreign_terms   = $get_wineries(false);

        // Function to append terms as menu children
        $append_terms = function( $terms, $parent_id ) use ( &$items ) {
            if ( empty($terms) ) return;

            foreach ( $items as $item ) {
                if ( (int) $item->ID === (int) $parent_id ) {
                    $item->classes[] = 'menu-item-has-children';
                }
            }

            foreach ( $terms as $term ) {
                $term_link = get_term_link($term);
                if ( is_wp_error($term_link) ) continue;

                $items[] = (object) [
                    'ID'               => $term->term_id + 100000 + $parent_id, // Unique fake ID
                    'db_id'            => $term->term_id,
                    'menu_item_parent' => $parent_id,
                    'title'            => $term->name,
                    'url'              => $term_link,
                    'classes'          => ['menu-item', 'nav__item', 'level2'],
                    'type'             => 'custom',
                    'object'           => 'pa_boraszat',
                    'object_id'        => $term->term_id,
                    'target'           => '',
                    'attr_title'       => '',
                    'description'      => '',
                    'xfn'              => '',
                    'status'           => '',
                ];
            }
        };

        // Attach both sets
        $append_terms($hungarian_terms, $hungarian_parent_id);
        $append_terms($foreign_terms, $foreign_parent_id);

        return $items;
    }

    add_filter('wp_nav_menu_objects', 'dynamic_wineries_menu', 10, 2);
}
