<?php
    if ( ! function_exists('update_bookmark_list_handler') ) {
        /**
         * Handles AJAX requests to update the user's bookmarked posts list.
         *
         * This function retrieves the bookmarks of the currently logged-in user,
         * queries the corresponding posts, renders them using a template part,
         * and returns the HTML via JSON response.
         *
         * @return void Sends JSON response and exits.
         */
        function update_bookmark_list_handler() {
            $current_user_id = get_current_user_id();

            // Ensure the user is logged in
            if ( ! $current_user_id ) {
                wp_send_json_error([
                    'message' => __('You must be logged in', TEXT_DOMAIN)
                ], 401);
            }

            // Define post types to include
            $post_type = ['post', 'knowledge_base'];

            // Retrieve bookmarked post IDs from user meta
            $bookmark_ids = get_user_meta($current_user_id, 'user_bookmarks', true) ?: [0];

            // Capture the template output
            ob_start();
            $template_args = array('post_type' => $post_type, 'post_ids' => $bookmark_ids);
            get_template_part( 'template-parts/queries/query', 'post-type', $template_args );
            $html = ob_get_clean();

            // Return the rendered HTML as JSON
            wp_send_json_success(['html' => $html]);
        }

        // Register AJAX handlers for logged-in and non-logged-in users
        add_action('wp_ajax_update_bookmark_list_handler', 'update_bookmark_list_handler');
        add_action('wp_ajax_nopriv_update_bookmark_list_handler', 'update_bookmark_list_handler');
    }
