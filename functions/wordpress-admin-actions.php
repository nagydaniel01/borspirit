<?php
    // ============================================================
    // ADMIN PAGE
    // ============================================================

    if ( ! function_exists( 'wpdocs_remove_dashboard_widgets' ) ) {
        /**
         * Removes unwanted widgets from the WordPress Dashboard.
         *
         * @return void
         */
        function wpdocs_remove_dashboard_widgets() {
            // Side widgets (small column)
            //remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );   // Quick Draft
            remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );       // WordPress Events and News

            // Normal widgets (main area)
            remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );   // At a Glance (summary of posts, pages, comments)
            //remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );    // Activity (recent posts, comments)
        }
        add_action( 'wp_dashboard_setup', 'wpdocs_remove_dashboard_widgets' );
    }

    if ( ! function_exists( 'remove_footer_admin' ) ) {
        /**
         * Replaces the WordPress admin footer text with theme information and PHP version.
         *
         * Displays the active theme's name, version, and the current PHP version
         * in the admin footer area.
         *
         * @return void
         */
        function remove_footer_admin() {
            $theme = wp_get_theme();
            $text = sprintf(
                /* translators: 1: Theme name, 2: Theme version, 3: PHP version */
                __('Sablon: %1$s %2$s verzió, PHP: %3$s verzió', TEXT_DOMAIN),
                $theme->get( 'Name' ),
                $theme->get( 'Version' ),
                phpversion()
            );
            echo esc_html( $text );
        }
        add_filter( 'admin_footer_text', 'remove_footer_admin' );
    }
    
    // ============================================================
    // APPLY TAXONOMY FILTERS TO ADMIN QUERY
    // ============================================================

    if ( ! function_exists( 'add_taxonomy_filter_dropdown' ) ) {
        /**
         * Adds taxonomy filter dropdowns for all applicable taxonomies to the admin post list.
         *
         * @return void
         */
        function add_taxonomy_filter_dropdown() {
            global $typenow;

            // Get all publicly queryable taxonomies with UI enabled
            $taxonomies = get_taxonomies(array('show_ui' => true, 'public' => true), 'objects');

            foreach ($taxonomies as $taxonomy) {
                // Skip taxonomies that don't apply to the current post type
                if (!in_array($typenow, $taxonomy->object_type)) {
                    continue;
                }

                // Skip 'category' taxonomy for the 'post' post type to avoid duplication
                if ('category' === $taxonomy->name && $typenow === 'post') {
                    continue;
                }

                // Get the terms associated with the current taxonomy
                $terms = get_terms(array(
                    'taxonomy'   => $taxonomy->name,
                    'hide_empty' => true,
                ));

                // Skip taxonomies with no terms or errors
                if (is_wp_error($terms) || empty($terms)) {
                    continue;
                }

                // Set the selected term from the query string (if any)
                $selected = isset($_GET[$taxonomy->name]) ? esc_attr($_GET[$taxonomy->name]) : '';

                // If the taxonomy is 'post_tag', use a custom select dropdown
                if ('post_tag' === $taxonomy->name) {
                    echo '<select name="' . esc_attr($taxonomy->name) . '" class="postform">';
                    echo '<option value="">' . esc_html( sprintf( __('Összes %s', TEXT_DOMAIN), strtolower($taxonomy->labels->singular_name) ) ) . '</option>';

                    foreach ($terms as $term) {
                        printf(
                            '<option value="%1$s"%2$s>%3$s</option>',
                            esc_attr($term->slug),
                            selected($selected, $term->slug, false),
                            esc_html($term->name)
                        );
                    }

                    echo '</select>';
                } else {
                    // Use wp_dropdown_categories for non-tag taxonomies
                    wp_dropdown_categories(array(
                        'show_option_all' => esc_html( sprintf( __('Összes %s', TEXT_DOMAIN), strtolower($taxonomy->labels->singular_name) ) ),
                        'taxonomy'        => esc_attr($taxonomy->name),
                        'name'            => esc_attr($taxonomy->name),
                        'selected'        => $selected,
                        'hierarchical'    => true,
                        'value_field'     => 'slug',
                        'depth'           => 3,
                        'show_count'      => false,
                        'hide_empty'      => true,
                    ));
                }
            }
        }

        add_action( 'restrict_manage_posts', 'add_taxonomy_filter_dropdown' );
    }

    // ============================================================
    // APPLY AUTHOR FILTER TO ADMIN QUERY
    // ============================================================

    if ( ! function_exists( 'add_author_filter_dropdown' ) ) {
        /**
         * Adds an author filter dropdown to the admin post list.
         *
         * @return void
         */
        function add_author_filter_dropdown() {
            global $typenow;

            if (post_type_supports($typenow, 'author')) { // Only show if post type supports 'author'
                $selected_author = isset($_GET['author']) ? intval($_GET['author']) : 0;

                $authors = get_users(array(
                    //'who'                   => 'authors',
                    'capability'            => array('edit_posts'),
                    'has_published_posts'   => true,
                    'orderby'               => 'display_name',
                    'order'                 => 'ASC',
                ));

                if (!empty($authors)) {
                    echo '<select name="author" class="postform">';
                    echo '<option value="">' . esc_html__('Összes szerző', TEXT_DOMAIN) . '</option>';
                    foreach ($authors as $author) {
                        printf(
                            '<option value="%1$s"%2$s>%3$s</option>',
                            esc_attr($author->ID),
                            selected($selected_author, $author->ID, false),
                            esc_html($author->display_name)
                        );
                    }
                    echo '</select>';
                }
            }
        }

        add_action( 'restrict_manage_posts', 'add_author_filter_dropdown' );
    }

    // ============================================================
    // POST COLUMNS: POST ID
    // ============================================================

    if ( ! function_exists( 'add_post_id_column_to_all_post_types' ) ) {
        /**
         * Adds a sortable Post ID column to all post types in the WordPress admin.
         */
        function add_post_id_column_to_all_post_types() {
            $post_types = get_post_types( [ 'show_ui' => true ], 'names' );

            foreach ( $post_types as $post_type ) {
                // Add ID column
                add_filter( "manage_{$post_type}_posts_columns", function ( $columns ) {
                    $columns['post_id'] = 'ID';
                    return $columns;
                } );

                /*
                add_filter( "manage_{$post_type}_posts_columns", function ( $columns ) {
                    $new_columns = [];

                    foreach ( $columns as $key => $value ) {
                        $new_columns[ $key ] = $value;

                        // Insert our column right after the 'date' column
                        if ( $key === 'date' ) {
                            $new_columns['post_id'] = __( 'ID', TEXT_DOMAIN );
                        }
                    }

                    return $new_columns;
                } );
                */

                // Populate ID column
                add_action( "manage_{$post_type}_posts_custom_column", function ( $column, $post_id ) {
                    if ( $column === 'post_id' ) {
                        echo esc_html( $post_id );
                    }
                }, 10, 2 );

                // Make column sortable
                add_filter( "manage_edit-{$post_type}_sortable_columns", function ( $columns ) {
                    $columns['post_id'] = 'ID';
                    return $columns;
                } );
            }
        }
        add_action( 'admin_init', 'add_post_id_column_to_all_post_types' );
    }

    if ( ! function_exists( 'add_featured_image_column_to_all_post_types' ) ) {
        /**
         * Add a "Featured Image" column to the admin list table
         * for all public post types (built-in and custom).
         *
         * @return void
         */
        function add_featured_image_column_to_all_post_types() {
            $post_types = get_post_types( [ 'public' => true ], 'names' );

            // Exclude WooCommerce products
            $excluded_post_types = [ 'product' ];

            foreach ( $post_types as $post_type ) {
                if ( in_array( $post_type, $excluded_post_types ) ) {
                    continue;
                }

                // Add column header
                add_filter( "manage_{$post_type}_posts_columns", function ( $columns ) {
                    $columns['featured_image'] = __( 'Featured image' );
                    return $columns;
                } );

                // Add column content
                add_action( "manage_{$post_type}_posts_custom_column", function ( $column_name, $post_id ) {
                    if ( $column_name === 'featured_image' ) {
                        $thumbnail = get_the_post_thumbnail( $post_id, [ 60, 60 ] );
                        echo $thumbnail ?: '—';
                    }
                }, 10, 2 );
            }
        }

        add_action( 'admin_init', 'add_featured_image_column_to_all_post_types' );
    }

    // ============================================================
    // POST COLUMNS: PAGE TEMPLATE NAME
    // ============================================================

    if ( ! function_exists( 'add_template_column_with_tooltip' ) ) {
        /**
         * Add a "Template" column to the Pages admin list for administrators only.
         *
         * @param array $columns Existing column headers.
         * @return array Modified column headers with the Template column.
         */
        function add_template_column_with_tooltip($columns) {
            if (current_user_can('administrator')) {
                $columns['page_template'] = __('Template');
            }
            return $columns;
        }
        add_filter( 'manage_pages_columns', 'add_template_column_with_tooltip' );
    }

    if ( ! function_exists( 'show_template_column_with_tooltip' ) ) {

        /**
         * Populate the custom "Template" column with the readable template name
         * and show the template file path in the title attribute as a tooltip.
         *
         * @param string $column_name The name of the column to display.
         * @param int    $post_id     The current post ID.
         */
        function show_template_column_with_tooltip( $column_name, $post_id ) {
            if ( $column_name !== 'page_template' || ! current_user_can( 'administrator' ) ) {
                return;
            }

            $template = get_post_meta( $post_id, '_wp_page_template', true );

            if ( $template === 'default' ) {
                echo __( 'Default template' );
            } else {
                $template_path = locate_template( $template );
                $template_name = '';

                if ( file_exists( $template_path ) ) {
                    $template_data = get_file_data( $template_path, array( 'name' => 'Template Name' ) );
                    $template_name = $template_data['name'] ?: basename( $template );
                } else {
                    $template_name = basename( $template );
                }

                echo '<span title="' . esc_attr( $template ) . '">' . esc_html( $template_name ) . '</span>';
            }
        }
        add_action( 'manage_pages_custom_column', 'show_template_column_with_tooltip', 10, 2 );
    }

    // ============================================================
    // POST COLUMNS: POST FORMAT
    // ============================================================

    if ( ! function_exists( 'add_post_format_column_with_tooltip' ) ) {
        /**
         * Add a "Format" column to the Posts admin list for administrators only.
         *
         * @param array $columns Existing column headers.
         * @return array Modified column headers with the Format column.
         */
        function add_post_format_column_with_tooltip( $columns ) {
            if ( current_user_can( 'administrator' ) ) {
                $columns['post_format'] = __( 'Format' );
            }
            return $columns;
        }
        add_filter( 'manage_posts_columns', 'add_post_format_column_with_tooltip' );
    }

    if ( ! function_exists( 'show_post_format_column_with_tooltip' ) ) {
        /**
         * Populate the custom "Format" column with the localized post format
         * name and show the raw format key in the title attribute as a tooltip.
         *
         * @param string $column_name The name of the column to display.
         * @param int    $post_id     The current post ID.
         */
        function show_post_format_column_with_tooltip( $column_name, $post_id ) {
            if ( $column_name !== 'post_format' || ! current_user_can( 'administrator' ) ) {
                return;
            }

            $format = get_post_format( $post_id );

            // Get localized post format names
            $format_names = get_post_format_strings();

            if ( ! $format ) {
                echo '<span title="standard">' . esc_html( $format_names['standard'] ) . '</span>';
            } elseif ( isset( $format_names[ $format ] ) ) {
                echo '<span title="' . esc_attr( $format ) . '">' . esc_html( $format_names[ $format ] ) . '</span>';
            } else {
                // Fallback: display raw format
                echo '<span title="' . esc_attr( $format ) . '">' . esc_html( ucfirst( $format ) ) . '</span>';
            }
        }
        add_action( 'manage_posts_custom_column', 'show_post_format_column_with_tooltip', 10, 2 );
    }

    // ============================================================
    // TERM COLUMNS: TERM ID
    // ============================================================

    if ( ! function_exists( 'add_term_id_column_to_all_taxonomies' ) ) {
        /**
         * Adds a sortable Term ID column to all taxonomies (built-in and custom) in the WordPress admin.
         */
        function add_term_id_column_to_all_taxonomies() {
            $taxonomies = get_taxonomies([], 'names'); // Include both built-in and custom taxonomies

            foreach ( $taxonomies as $taxonomy ) {
                // Add Term ID column
                add_filter( "manage_edit-{$taxonomy}_columns", function ( $columns ) {
                    $columns['term_id'] = 'ID';
                    return $columns;
                } );

                // Display Term ID in the column
                add_filter( "manage_{$taxonomy}_custom_column", function ( $content, $column_name, $term_id ) {
                    if ( $column_name === 'term_id' ) {
                        return $term_id;
                    }
                    return $content;
                }, 10, 3 );

                // Make Term ID column sortable
                add_filter( "manage_edit-{$taxonomy}_sortable_columns", function ( $columns ) {
                    $columns['term_id'] = 'term_id';
                    return $columns;
                } );
            }
        }
        add_action( 'admin_init', 'add_term_id_column_to_all_taxonomies' );
    }

    // ============================================================
    // ADD TAXONOMY IMAGES
    // ============================================================

    if ( ! function_exists( 'add_custom_taxonomy_image' ) ) {
        /**
         * Display image upload field on the taxonomy add form.
         *
         * @param string $taxonomy The taxonomy slug.
         */
        function add_custom_taxonomy_image( $taxonomy ) { ?>
            <div class="form-field term-group">
                <label for="term_thumbnail_id"><?php _e( 'Image', 'wordpress' ); ?></label>
                <input type="hidden" id="term_thumbnail_id" name="term_thumbnail_id" value="">
                <div id="image_wrapper"></div>
                <p>
                    <input type="button" class="button button-secondary taxonomy_media_button" id="taxonomy_media_button" value="<?php _e( 'Add Image', 'wordpress' ); ?>">
                    <input type="button" class="button button-secondary taxonomy_media_remove" id="taxonomy_media_remove" value="<?php _e( 'Remove Image', 'wordpress' ); ?>">
                </p>
            </div>
        <?php }
        add_action( 'category_add_form_fields', 'add_custom_taxonomy_image' );
        add_action( 'taxonomy_add_form_fields', 'add_custom_taxonomy_image' );
    }

    if ( ! function_exists( 'save_custom_taxonomy_image' ) ) {
        /**
         * Save the taxonomy image on term creation.
         *
         * @param int $term_id Term ID.
         */
        function save_custom_taxonomy_image( $term_id ) {
            if ( isset( $_POST['term_thumbnail_id'] ) && '' !== $_POST['term_thumbnail_id'] ) {
                add_term_meta( $term_id, '_thumbnail_id', sanitize_text_field( $_POST['term_thumbnail_id'] ), true );
            }
        }
        add_action( 'created_category', 'save_custom_taxonomy_image' );
        add_action( 'created_taxonomy', 'save_custom_taxonomy_image' );
    }

    if ( ! function_exists( 'update_custom_taxonomy_image' ) ) {
        /**
         * Display the image upload field on the taxonomy edit form.
         *
         * @param WP_Term $term The term object.
         */
        function update_custom_taxonomy_image( $term ) { 
            $term_thumbnail_id = get_term_meta( $term->term_id, '_thumbnail_id', true ); ?>
            <tr class="form-field term-group-wrap">
                <th scope="row">
                    <label for="term_thumbnail_id"><?php _e( 'Image', 'wordpress' ); ?></label>
                </th>
                <td>
                    <input type="hidden" id="term_thumbnail_id" name="term_thumbnail_id" value="<?php echo esc_attr( $term_thumbnail_id ); ?>">
                    <div id="image_wrapper">
                        <?php if ( $term_thumbnail_id ) {
                            echo wp_get_attachment_image( $term_thumbnail_id, 'thumbnail' );
                        } ?>
                    </div>
                    <p>
                        <input type="button" class="button button-secondary taxonomy_media_button" value="<?php _e( 'Add Image', 'wordpress' ); ?>">
                        <input type="button" class="button button-secondary taxonomy_media_remove" value="<?php _e( 'Remove Image', 'wordpress' ); ?>">
                    </p>
                </td>
            </tr>
        <?php }
        add_action( 'category_edit_form_fields', 'update_custom_taxonomy_image' );
        add_action( 'taxonomy_edit_form_fields', 'update_custom_taxonomy_image' );
    }

    if ( ! function_exists( 'updated_custom_taxonomy_image' ) ) {
        /**
         * Save the taxonomy image on term update.
         *
         * @param int $term_id Term ID.
         */
        function updated_custom_taxonomy_image( $term_id ) {
            if ( isset( $_POST['term_thumbnail_id'] ) ) {
                update_term_meta( $term_id, '_thumbnail_id', sanitize_text_field( $_POST['term_thumbnail_id'] ) );
            }
        }
        add_action( 'edited_category', 'updated_custom_taxonomy_image' );
        add_action( 'edited_taxonomy', 'updated_custom_taxonomy_image' );
    }

    if ( ! function_exists( 'custom_taxonomy_load_media' ) ) {
        /**
         * Enqueue WordPress media scripts for taxonomy pages.
         */
        function custom_taxonomy_load_media() {
            if ( isset( $_GET['taxonomy'] ) ) {
                wp_enqueue_media();
            }
        }
        add_action( 'admin_enqueue_scripts', 'custom_taxonomy_load_media' );
    }

    if ( ! function_exists( 'add_custom_taxonomy_script' ) ) {    
        /**
         * JavaScript for handling the image upload functionality.
         */
        function add_custom_taxonomy_script() {
            if ( isset( $_GET['taxonomy'] ) ) { ?>
                <script>
                    jQuery(document).ready(function($) {
                        function taxonomy_media_upload(button_class) {
                            var custom_media = true;
                            var original_attachment = wp.media.editor.send.attachment;

                            $('body').on('click', button_class, function(e) {
                                e.preventDefault();
                                var button = $(this);

                                wp.media.editor.send.attachment = function(props, attachment) {
                                    if (custom_media) {
                                        $('#term_thumbnail_id').val(attachment.id);
                                        $('#image_wrapper').html('<img class="custom_media_image" src="' + attachment.url + '" style="max-height:100px;"/>');
                                    } else {
                                        return original_attachment.apply(button, [props, attachment]);
                                    }
                                };

                                wp.media.editor.open(button);
                                return false;
                            });

                            $('body').on('click', '.taxonomy_media_remove', function() {
                                $('#term_thumbnail_id').val('');
                                $('#image_wrapper').html('');
                            });
                        }

                        taxonomy_media_upload('.taxonomy_media_button');
                    });
                </script>
            <?php }
        }
        add_action( 'admin_footer', 'add_custom_taxonomy_script' );
    }

    if ( ! function_exists( 'display_custom_taxonomy_image_column_heading' ) ) {
        /**
         * Add custom column heading for taxonomy images.
         *
         * @param array $columns List of columns.
         * @return array Updated list of columns.
         */
        function display_custom_taxonomy_image_column_heading( $columns ) {
            $columns['term_image'] = __( 'Image', 'wordpress' );
            return $columns;
        }
        add_filter( 'manage_edit-category_columns', 'display_custom_taxonomy_image_column_heading' );
        add_filter( 'manage_edit-taxonomy_columns', 'display_custom_taxonomy_image_column_heading' );
    }

    if ( ! function_exists( 'display_custom_taxonomy_image_column_value' ) ) {
        /**
         * Display the taxonomy image in the admin column.
         *
         * @param string $columns Column content.
         * @param string $column The column name.
         * @param int    $id Term ID.
         * @return string Updated column content.
         */
        function display_custom_taxonomy_image_column_value( $columns, $column, $id ) {
            if ( 'term_image' === $column ) {
                $term_thumbnail_id = get_term_meta( $id, '_thumbnail_id', true );
                if ( $term_thumbnail_id ) {
                    $columns = wp_get_attachment_image( $term_thumbnail_id, array( 50, 50 ) );
                }
            }
            return $columns;
        }
        add_action( 'manage_category_custom_column', 'display_custom_taxonomy_image_column_value', 10, 3 );
        add_action( 'manage_taxonomy_custom_column', 'display_custom_taxonomy_image_column_value', 10, 3 );
    }

    // ============================================================
    // USER COLUMNS: USER ID AND USER REGISTRATION DATE
    // ============================================================

    if ( ! function_exists( 'add_custom_user_columns' ) ) {
        /**
         * Add custom columns to the Users list table.
         *
         * Adds 'User ID' and 'User Registration Date' columns.
         *
         * @param array $columns Existing columns in the Users list table.
         * @return array Modified columns including the new custom columns.
         */
        function add_custom_user_columns( $columns ) {
            $columns['user_id']                = __('User ID');
            $columns['user_registration_date'] = __('User Registration Date');
            return $columns;
        }

        add_filter( 'manage_users_columns', 'add_custom_user_columns' );
    }

    if ( ! function_exists( 'render_custom_user_columns' ) ) {
        /**
         * Render the content for custom columns in the Users list table.
         *
         * Displays the user ID and formatted user registration date.
         *
         * @param string $value       Current value of the column (default empty).
         * @param string $column_name The name/key of the column being rendered.
         * @param int    $user_id     The ID of the user for the current row.
         * @return string             The value to display in the custom column.
         */
        function render_custom_user_columns( $value, $column_name, $user_id ) {
            if ( $column_name == 'user_id' ) {
                return $user_id;
            }

            if ( $column_name == 'user_registration_date' ) {
                $user_info = get_userdata( $user_id );
                return date( 'Y-m-d H:i:s', strtotime( $user_info->user_registered ) ); // Format date as 'Y-m-d H:i:s'
            }
            return $value;
        }

        add_action( 'manage_users_custom_column', 'render_custom_user_columns', 10, 3 );
    }

    if ( ! function_exists( 'make_registration_date_column_sortable' ) ) {
        /**
         * Make the custom registration date column sortable.
         *
         * Associates the 'user_registration_date' column with the 'user_registered' user field for sorting.
         *
         * @param array $columns Array of sortable columns keyed by column name.
         * @return array Modified array including 'user_registration_date' sortable key.
         */
        function make_registration_date_column_sortable($columns) {
            $columns['user_registration_date'] = 'user_registered';
            return $columns;
        }

        add_filter( 'manage_users_sortable_columns', 'make_registration_date_column_sortable' );
    }

    // ============================================================
    // SWITCH TO USER ACCOUNT
    // ============================================================

    if ( ! function_exists( 'switch_to_user_account' ) ) {
        /**
         * Adds a "Switch To" link to each user row in the admin Users list for administrators.
         *
         * @param array   $actions The list of row actions.
         * @param WP_User $user    The user object for the current row.
         * @return array Modified list of row actions with 'Switch To' link added.
         */
        function switch_to_user_account( $actions, $user ) {
            // Ensure only administrators can switch to other users
            if ( ! current_user_can( 'administrator' ) || get_current_user_id() === $user->ID ) {
                return $actions; // Prevent switching to self
            }

            // Create a secure nonce for the URL
            $nonce = wp_create_nonce( 'secure_user_switch' );
            $url   = add_query_arg(
                array(
                    'switch_user' => $user->user_login,
                    'token'       => $nonce,
                ),
                site_url()
            );

            // Add the 'Switch To' action link
            $actions['switch_to'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Fiók váltás', TEXT_DOMAIN ) . '</a>';

            return $actions;
        }
        add_filter( 'user_row_actions', 'switch_to_user_account', 10, 2 );
    }


    if ( ! function_exists( 'handle_user_switching' ) ) {
        /**
         * Switches the current logged-in admin to another user if a valid request is made.
         * Verifies nonce and prevents switching to self.
         *
         * @return void
         */
        function handle_user_switching() {
            // Ensure the user is logged in and has admin privileges
            if ( ! is_user_logged_in() || ! current_user_can( 'administrator' ) ) {
                return;
            }

            // Check if the 'switch_user' and 'token' parameters exist in the URL
            if ( isset( $_GET['switch_user'], $_GET['token'] ) ) {
                $user_login = sanitize_user( $_GET['switch_user'] );
                $token      = sanitize_text_field( $_GET['token'] );

                // Verify the nonce for security
                if ( wp_verify_nonce( $token, 'secure_user_switch' ) ) {
                    $user = get_user_by( 'login', $user_login );

                    if ( $user && $user->ID !== get_current_user_id() ) {
                        // Switch to the specified user
                        wp_set_current_user( $user->ID );
                        wp_set_auth_cookie( $user->ID );
                        wp_redirect( admin_url() ); // Redirect to the admin dashboard
                        exit;
                    }
                }
            }
        }
        add_action( 'init', 'handle_user_switching' );
    }
