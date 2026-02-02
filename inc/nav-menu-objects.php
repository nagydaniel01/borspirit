<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! function_exists('dynamic_wineries_menu') ) {
        /**
         * Dynamically adds Hungarian and foreign wineries under "Borászatok" tabs:
         * - Magyar (menu-item-644) → flat list of wineries
         * - Külföldi (menu-item-645) → grouped by country (pa_orszag)
         */
        function dynamic_wineries_menu( $items, $args ) {
            $theme_location = 'primary_menu'; // Adjust if needed
            $hungarian_parent_id = 644;       // "Magyar" tab ID
            $foreign_parent_id   = 645;       // "Külföldi" tab ID

            if ( $args->theme_location !== $theme_location ) {
                return $items;
            }

            /**
             * Helper: Get all wineries by country condition
             */
            $get_wineries = function( $is_hungarian = true ) {
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

                if ( empty( $products ) ) {
                    return [];
                }

                $wineries = wp_get_object_terms( $products, 'pa_boraszat', ['fields' => 'all'] );
                if ( empty( $wineries ) || is_wp_error( $wineries ) ) {
                    return [];
                }

                return array_unique( $wineries, SORT_REGULAR );
            };

            /**
             * Helper: Get foreign wineries grouped by country (pa_orszag)
             */
            $get_foreign_grouped = function() {
                $tax_query = [
                    [
                        'taxonomy' => 'pa_orszag',
                        'field'    => 'name',
                        'terms'    => ['Magyarország'],
                        'operator' => 'NOT IN',
                    ],
                ];

                $products = get_posts([
                    'post_type'      => 'product',
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'tax_query'      => $tax_query,
                ]);

                if ( empty( $products ) ) {
                    return [];
                }

                $wineries = wp_get_object_terms( $products, 'pa_boraszat', ['fields' => 'all'] );
                if ( empty( $wineries ) || is_wp_error( $wineries ) ) {
                    return [];
                }

                $grouped = [];
                foreach ( $wineries as $winery ) {
                    $product_ids = get_objects_in_term( $winery->term_id, 'pa_boraszat' );
                    $countries   = wp_get_object_terms( $product_ids, 'pa_orszag', ['fields' => 'all'] );

                    foreach ( $countries as $country ) {
                        $grouped[ $country->name ]['term'] = $country;
                        $grouped[ $country->name ]['wineries'][] = $winery;
                    }
                }

                ksort( $grouped, SORT_NATURAL | SORT_FLAG_CASE );
                return $grouped;
            };

            // Fetch winery data
            $hungarian_wineries = $get_wineries(true);
            $foreign_groups      = $get_foreign_grouped();

            /**
             * Append simple flat winery list
             */
            $append_terms = function( $terms, $parent_id ) use ( &$items ) {
                if ( empty( $terms ) ) return;

                foreach ( $items as $item ) {
                    if ( (int) $item->ID === (int) $parent_id ) {
                        $item->classes[] = 'menu-item-has-children';
                    }
                }

                foreach ( $terms as $term ) {
                    $term_link = get_term_link( $term );
                    if ( is_wp_error( $term_link ) ) continue;

                    $items[] = (object) [
                        'ID'               => $term->term_id + 100000 + $parent_id,
                        'db_id'            => $term->term_id,
                        'menu_item_parent' => $parent_id,
                        'title'            => $term->name,
                        'url'              => $term_link,
                        'classes'          => ['menu-item', 'nav__item', 'level2'],
                        'type'             => 'taxonomy',
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

            /**
             * Append grouped countries and wineries for Külföldi
             */
            $append_grouped_terms = function( $groups, $parent_id ) use ( &$items ) {
                if ( empty( $groups ) ) return;

                foreach ( $items as $item ) {
                    if ( (int) $item->ID === (int) $parent_id ) {
                        $item->classes[] = 'menu-item-has-children';
                    }
                }

                foreach ( $groups as $country_name => $data ) {
                    $country  = $data['term'];
                    $wineries = $data['wineries'] ?? [];
                    $country_link = get_term_link( $country );
                    if ( is_wp_error( $country_link ) ) continue;

                    // Add country as clickable Level 2
                    $country_id = crc32( $country_name . $parent_id );
                    $items[] = (object) [
                        'ID'               => $country_id,
                        'db_id'            => $country_id,
                        'menu_item_parent' => $parent_id,
                        'title'            => esc_html( $country_name ),
                        'url'              => '',
                        'classes'          => ['menu-item', 'nav__item', 'level2', 'menu-item-has-children'],
                        'type'             => 'taxonomy',
                        'object'           => 'pa_orszag',
                        'object_id'        => $country->term_id,
                        'target'           => '',
                        'attr_title'       => '',
                        'description'      => '',
                        'xfn'              => '',
                        'status'           => '',
                    ];

                    // Add wineries under each country (Level 3)
                    foreach ( $wineries as $winery ) {
                        $term_link = get_term_link( $winery );
                        if ( is_wp_error( $term_link ) ) continue;

                        $winery_id = $winery->term_id + $country_id;
                        $items[] = (object) [
                            'ID'               => $winery_id,
                            'db_id'            => $winery_id,
                            'menu_item_parent' => $country_id,
                            'title'            => esc_html( $winery->name ),
                            'url'              => esc_attr( $term_link ),
                            'classes'          => ['menu-item', 'nav__item', 'level3'],
                            'type'             => 'taxonomy',
                            'object'           => 'pa_boraszat',
                            'object_id'        => $winery->term_id,
                            'target'           => '',
                            'attr_title'       => '',
                            'description'      => '',
                            'xfn'              => '',
                            'status'           => '',
                        ];
                    }
                }
            };

            // Append results
            $append_terms( $hungarian_wineries, $hungarian_parent_id );
            $append_grouped_terms( $foreign_groups, $foreign_parent_id );

            return $items;
        }

        add_filter( 'wp_nav_menu_objects', 'dynamic_wineries_menu', 10, 2 );
    }
