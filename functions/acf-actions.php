<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    /** 
     * Licenckulcs:
     * b3JkZXJfaWQ9MTA2Mzk3fHR5cGU9ZGV2ZWxvcGVyfGRhdGU9MjAxNy0wNS0xNSAxMTowNzozMw==
     */

    if ( ! function_exists( 'acf_admin_notice' ) ) {
        /**
         * Checks if the Advanced Custom Fields (ACF) plugin is active.
         * If not active, displays an admin notice and prevents dependent theme code from running.
         *
         * @return void
         */
        function acf_admin_notice() {
            // If ACF's get_field() function does not exist, warn the admin.
            if ( ! function_exists( 'get_field' ) && is_admin() ) {
                echo '<div class="notice notice-error"><p><strong>Advanced Custom Fields</strong> is required for this theme. Please install and activate it.</p></div>';
            }
        }
        //add_action( 'admin_notices', 'acf_admin_notice' );
    }

    if ( ! function_exists( 'check_acf_before_theme_activation' ) ) {
        /**
         * Prevent theme activation if ACF is not active.
         *
         * @return void
         */
        function check_acf_before_theme_activation() {
            if ( ! function_exists( 'get_field' ) ) {
                // Switch back to previous theme
                switch_theme( WP_DEFAULT_THEME );
                
                // Remove 'Theme activated' message
                unset( $_GET['activated'] );

                // Show admin error
                add_action( 'admin_notices', function() {
                    echo '<div class="notice notice-error"><p><strong>Advanced Custom Fields</strong> is required for this theme. The theme has been deactivated.</p></div>';
                });
            }
        }
        add_action( 'after_switch_theme', 'check_acf_before_theme_activation' );
    }

    if ( ! function_exists( 'mytheme_register_acf_options_pages' ) ) {
        /**
         * Register ACF options pages for Sablon beállítások.
         *
         * This function adds a main "Sablon beállítások" options page,
         * along with "Header" and "Footer" subpages, using
         * Advanced Custom Fields (ACF) Pro's Options Page feature.
         *
         * @return void
         */
        function mytheme_register_acf_options_pages() {
            if ( function_exists( 'acf_add_options_page' ) ) {

                // Main options page
                acf_add_options_page( array(
                    'page_title'    => 'Sablon beállítások',
                    'menu_title'    => 'Sablon beállítások',
                    'menu_slug'     => 'theme-settings',
                    'capability'    => 'edit_posts',
                    'redirect'      => false,
                ) );
            }
        }
        add_action( 'acf/init', 'mytheme_register_acf_options_pages' );
    }

    if ( ! function_exists( 'add_theme_settings_link' ) ) {
        /**
         * Add a Theme Settings link with a gear icon to the WordPress Admin Bar.
         *
         * @param WP_Admin_Bar $wp_admin_bar The WordPress Admin Bar object.
         * 
         * @return void
         */
        function add_theme_settings_link( $wp_admin_bar ) {
            if ( ! class_exists( 'WP_Admin_Bar' ) ) {
                return;
            }

            if ( ! is_object( $wp_admin_bar ) || ! method_exists( $wp_admin_bar, 'add_node' ) ) {
                return;
            }

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            $args = array(
                'id'    => 'theme-settings',
                'title' => '<span class="ab-icon dashicons dashicons-admin-generic"></span><span class="ab-label">Sablon beállítások</span>',
                'href'  => esc_url( admin_url( 'themes.php?page=theme-settings' ) ),
                'meta'  => array(
                    'class' => 'theme-settings-link',
                    'title' => esc_attr__( '', TEXT_DOMAIN )
                )
            );

            $wp_admin_bar->add_node( $args );
        }
        add_action( 'admin_bar_menu', 'add_theme_settings_link', 999 );
    }

    if ( ! function_exists( 'my_acf_add_wc_prod_attr_rule_type' ) ) {
        /**
         * Add a custom rule type to ACF location rules.
         *
         * @param array $choices Existing ACF rule types.
         * @return array Modified ACF rule types with custom WC product attribute.
         */
        function my_acf_add_wc_prod_attr_rule_type( $choices ) {
            if ( ! is_array( $choices ) ) {
                $choices = [];
            }

            $choices[ __( 'Other', TEXT_DOMAIN ) ]['wc_prod_attr'] = __( 'WC Product Attribute', TEXT_DOMAIN );

            return $choices;
        }
    }
    add_filter( 'acf/location/rule_types', 'my_acf_add_wc_prod_attr_rule_type' );

    if ( ! function_exists( 'my_acf_add_wc_prod_attr_rule_values' ) ) {
        /**
         * Add custom rule values (list of WooCommerce product attributes).
         *
         * @param array $choices Existing rule values.
         * @return array Modified rule values with WooCommerce attributes.
         */
        function my_acf_add_wc_prod_attr_rule_values( $choices ) {
            if ( ! is_array( $choices ) ) {
                $choices = [];
            }

            if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
                $attributes = wc_get_attribute_taxonomies();

                if ( ! empty( $attributes ) && is_array( $attributes ) ) {
                    foreach ( $attributes as $attr ) {
                        if ( isset( $attr->attribute_name, $attr->attribute_label ) ) {
                            $pa_name = wc_attribute_taxonomy_name( $attr->attribute_name );
                            $choices[ $pa_name ] = $attr->attribute_label;
                        }
                    }
                }
            }

            return $choices;
        }
    }
    add_filter( 'acf/location/rule_values/wc_prod_attr', 'my_acf_add_wc_prod_attr_rule_values' );

    if ( ! function_exists( 'my_acf_match_wc_prod_attr_rule' ) ) {
        /**
         * Match the custom rule against the current screen options.
         *
         * @param bool  $match   Whether the rule matches or not.
         * @param array $rule    Rule data (operator and value).
         * @param array $options Current screen options from ACF.
         * @return bool True if rule matches, false otherwise.
         */
        function my_acf_match_wc_prod_attr_rule( $match, $rule, $options ) {
            if ( ! is_array( $rule ) || ! isset( $rule['operator'], $rule['value'] ) ) {
                return (bool) $match;
            }

            if ( isset( $options['taxonomy'] ) ) {
                if ( '==' === $rule['operator'] ) {
                    $match = ( $rule['value'] === $options['taxonomy'] );
                } elseif ( '!=' === $rule['operator'] ) {
                    $match = ( $rule['value'] !== $options['taxonomy'] );
                }
            }

            return (bool) $match;
        }
    }
    add_filter( 'acf/location/rule_match/wc_prod_attr', 'my_acf_match_wc_prod_attr_rule', 10, 3 );

    if ( ! function_exists( 'add_post_to_relationship_field' ) ) {
        /**
         * Add a related post ID to an ACF relationship field.
         *
         * @param int    $post_id         The ID of the post containing the relationship field.
         * @param string $field_key       The field key or field name of the relationship field.
         * @param int    $related_post_id The ID of the related post to add.
         *
         * @return void
         */
        function add_post_to_relationship_field( $post_id, $field_key, $related_post_id ) {
            $current_value = get_field( $field_key, $post_id );

            if ( ! is_array( $current_value ) ) {
                $current_value = array();
            }

            if ( ! in_array( $related_post_id, $current_value ) ) {
                $current_value[] = $related_post_id;
            }

            update_field( $field_key, $current_value, $post_id );
        }
    }

    if ( ! function_exists( 'remove_post_from_relationship_field' ) ) {
        /**
         * Remove a related post ID from an ACF relationship field.
         *
         * @param int    $post_id         The ID of the post containing the relationship field.
         * @param string $field_key       The field key or field name of the relationship field.
         * @param int    $related_post_id The ID of the related post to remove.
         *
         * @return void
         */
        function remove_post_from_relationship_field( $post_id, $field_key, $related_post_id ) {
            $current_value = get_field( $field_key, $post_id );

            if ( is_array( $current_value ) ) {
                $key = array_search( $related_post_id, $current_value );

                if ( $key !== false ) {
                    unset( $current_value[ $key ] );

                    // Reindex array after removal
                    $current_value = array_values( $current_value );

                    update_field( $field_key, $current_value, $post_id );
                }
            }
        }
    }

    // Populate ACF select field options with Gravity Forms forms
    if ( ! function_exists( 'acf_populate_gform_ids' ) ) {
        /**
         * Populate ACF select field options with available Gravity Forms forms.
         *
         * @param array $field The ACF field array.
         * @return array Modified field array with form choices.
         */
        function acf_populate_gform_ids( $field ) {
            if ( ! class_exists( 'GFFormsModel' ) ) {
                return $field;
            }

            $choices = [ 'none' => __( 'None', TEXT_DOMAIN ) ];
            $forms   = GFFormsModel::get_forms();

            if ( ! empty( $forms ) ) {
                foreach ( $forms as $key => $form ) {
                    $choices[ $form->id ] = $form->title;
                }
            }

            $field['choices'] = $choices;

            return $field;
        }
        add_filter( 'acf/load_field/name=gform', 'acf_populate_gform_ids' );
    }

    function acf_load_food_pairing_tip_checkboxes( $field ) {
        
        // Reset choices
        $field['choices'] = array();

        // Check if the repeater has rows of data (replace 'option' if not on Options page)
        if( have_rows('food_pairing_tip_items', 'option') ) {
            
            while( have_rows('food_pairing_tip_items', 'option') ) {
                the_row();
                
                // Get subfields
                $text = get_sub_field('food_pairing_tip_text');

                if ( $text ) {
                    // Use sanitized text as value and original text as label
                    $field['choices'][ sanitize_title( $text ) ] = $text;
                }
            }
            
        }

        return $field;
    }

    add_filter('acf/load_field/name=product_food_pairing_tips', 'acf_load_food_pairing_tip_checkboxes');

    function acf_load_icon_checkboxes( $field ) {
        
        // Reset choices
        $field['choices'] = array();

        // Add fixed choices
        $field['choices']['free_shipping_limit_message'] = __( 'Free Shipping Limit', TEXT_DOMAIN );
        $field['choices']['estimated_delivery_message'] = __( 'Estimated Delivery', TEXT_DOMAIN );

        // Check if the repeater has rows of data (replace 'option' if not on Options page)
        if( have_rows('icon_items', 'option') ) {
            
            while( have_rows('icon_items', 'option') ) {
                the_row();
                
                // Get subfields
                $text = get_sub_field('icon_text');

                if ( $text ) {
                    // Use sanitized text as value and original text as label
                    $field['choices'][ sanitize_title( $text ) ] = $text;
                }
            }
            
        }

        return $field;
    }

    add_filter('acf/load_field/name=product_page_icons', 'acf_load_icon_checkboxes');
    add_filter('acf/load_field/name=product_icons', 'acf_load_icon_checkboxes');