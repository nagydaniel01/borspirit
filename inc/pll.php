<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! function_exists( 'my_custom_language_switcher' ) ) {
        /**
         * Display a custom Polylang language switcher with full error handling.
         *
         * @param array $args Optional. Arguments to customize the switcher.
         */
        function my_custom_language_switcher( $args = [] ) {

            // Ensure Polylang is active
            if ( ! function_exists( 'pll_the_languages' ) ) {
                trigger_error( 'Polylang is not active or pll_the_languages() function is missing.', E_USER_WARNING );
                return null;
            }

            // Default settings
            $defaults = [
                'dropdown'               => 0,
                'show_names'             => 1,
                'display_names_as'       => 'name',
                'show_flags'             => 0,
                'hide_if_empty'          => 1,
                'force_home'             => 0,
                'echo'                   => 0,
                'hide_if_no_translation' => 0,
                'hide_current'           => 1,
                'post_id'                => null,
                'raw'                    => 1,
                'wrapper_class'          => 'pll__list',  // ul class
                'inner_class'            => 'pll__item',  // li class
                'link_class'             => 'pll__link',
                'flag_class'             => 'pll__flag',
                'label_class'            => 'pll__label visually-hidden',
            ];

            // Merge defaults with user args
            $args = wp_parse_args( $args, $defaults );

            // Validate post_id
            if ( ! empty( $args['post_id'] ) ) {
                $args['post_id'] = absint( $args['post_id'] ) ?: null;
            }

            // Get languages
            $languages = pll_the_languages( $args );

            if ( empty( $languages ) || ! is_array( $languages ) ) {
                if ( ! $args['hide_if_empty'] ) {
                    echo '<!-- No languages found -->';
                }
                return null;
            }

            // Start wrapper
            echo '<ul class="' . esc_attr( $args['wrapper_class'] ) . '">';

            foreach ( $languages as $lang ) {

                // Skip invalid entries
                if ( ! is_array( $lang ) ) {
                    continue;
                }

                $url  = $lang['url']  ?? '';
                $name = $lang['name'] ?? '';
                $slug = $lang['slug'] ?? '';
                $flag = $lang['flag'] ?? '';

                if ( empty( $url ) || empty( $name ) ) {
                    continue;
                }

                // Determine flag URL
                $custom_flag_file = get_template_directory() . "/assets/src/svg/flags/{$slug}.svg";
                $custom_flag_url  = get_template_directory_uri() . "/assets/src/svg/flags/{$slug}.svg";
                $flag_url         = file_exists( $custom_flag_file ) ? $custom_flag_url : ( $flag ?: '' );

                // Output list item
                printf(
                    '<li class="%s"><a href="%s" class="%s">%s<span class="%s">%s</span></a></li>',
                    esc_attr( $args['inner_class'] ),
                    esc_url( $url ),
                    esc_attr( $args['link_class'] ),
                    ! empty( $flag_url )
                        ? sprintf(
                            '<img src="%s" alt="%s" class="%s">',
                            esc_url( $flag_url ),
                            esc_attr( $name ),
                            esc_attr( $args['flag_class'] )
                        )
                        : '',
                    esc_attr( $args['label_class'] ),
                    esc_html( $name )
                );
            }

            echo '</ul>';
        }
    }
