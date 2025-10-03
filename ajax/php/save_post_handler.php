<?php
    if ( ! function_exists('save_post_handler') ) {
        /**
         * Handles AJAX requests to save or remove a post from user bookmarks.
         *
         * This function toggles the bookmark status of a post for the current logged-in user.
         * If the post is already bookmarked, it removes it; otherwise, it adds it.
         * Responds with a JSON success message.
         *
         * @return void Sends JSON response and exits.
         */
        function save_post_handler() {
            // Security check: ensure post_id is provided
            if ( ! isset($_POST['post_id']) ) {
                wp_send_json_error([
                    'message' => __('Invalid request', TEXT_DOMAIN)
                ], 400);
            }

            $current_user_id = get_current_user_id();
            $save_post_id    = intval($_POST['post_id']);

            // Ensure the user is logged in
            if ( ! $current_user_id ) {
                wp_send_json_error([
                    'message' => __('You must be logged in.', TEXT_DOMAIN)
                ], 401);
            }

            // Get existing bookmarks from user meta
            $bookmarks = get_user_meta($current_user_id, 'user_bookmarks', true);
            if ( ! is_array($bookmarks) ) {
                $bookmarks = [];
            }

            // Toggle bookmark: remove if exists, add if not
            if ( in_array($save_post_id, $bookmarks) ) {
                $bookmarks = array_diff($bookmarks, [$save_post_id]);
            } else {
                $bookmarks[] = $save_post_id;
            }

            // Update user meta with the modified bookmarks
            update_user_meta($current_user_id, 'user_bookmarks', $bookmarks);

            // Return success response
            wp_send_json_success([
                'message' => __('The post saved successfully', TEXT_DOMAIN),
            ], 200);
        }

        // Register AJAX handlers for logged-in and non-logged-in users
        add_action('wp_ajax_save_post_handler', 'save_post_handler');
        add_action('wp_ajax_nopriv_save_post_handler', 'save_post_handler');
    }
