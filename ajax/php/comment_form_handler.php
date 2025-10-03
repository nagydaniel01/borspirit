<?php
    if ( ! function_exists('comment_form_handler') ) {

        /**
         * Handles AJAX comment submissions.
         *
         * This function validates POST requests, processes the comment via WordPress core functions,
         * sets cookies for the commenter, calculates the comment depth, and returns the generated
         * HTML for the comment using the theme's comment callback.
         *
         * @return void Sends JSON response and exits.
         */
        function comment_form_handler() {
            try {
                // Only allow POST requests
                if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
                    wp_send_json_error([
                        'message' => __('Invalid request method.', TEXT_DOMAIN)
                    ], 405);
                }

                // Verify that required fields exist and are valid
                if ( empty( $_POST['comment_post_ID'] ) || ! is_numeric( $_POST['comment_post_ID'] ) ) {
                    wp_send_json_error([
                        'message' => __('Invalid post ID.', TEXT_DOMAIN)
                    ], 400);
                }

                // Process comment submission using WordPress core function
                $comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
                if ( is_wp_error( $comment ) ) {
                    wp_send_json_error([
                        'message' => $comment->get_error_message()
                    ], 400);
                }

                if ( ! $comment || empty( $comment->comment_ID ) ) {
                    wp_send_json_error([
                        'message' => __('Comment could not be saved.', TEXT_DOMAIN),
                    ], 500);
                }

                // Set comment cookies for the user
                $user = wp_get_current_user();
                do_action( 'set_comment_cookies', $comment, $user );

                // Calculate comment depth for threaded comments
                $comment_depth = 1;
                $comment_parent = $comment->comment_parent;
                while ( $comment_parent ) {
                    $comment_depth++;
                    $parent_comment = get_comment( $comment_parent );
                    $comment_parent = $parent_comment->comment_parent;
                }

                // Get max depth from WP Discussion Settings
                $max_depth = get_option( 'thread_comments_depth', 5 );

                // Set globals needed by comment template functions
                $GLOBALS['comment'] = $comment;
                $GLOBALS['comment_depth'] = $comment_depth;

                // Generate comment HTML using the theme's comment callback
                ob_start();
                mytheme_comment( $comment, array(
                    'avatar_size' => 64,
                    'style'       => 'ol',
                    'max_depth'   => $max_depth
                ), $comment_depth );
                $comment_html = ob_get_clean();

                // Return success response with comment HTML
                wp_send_json_success([
                    'comment'     => $comment_html,
                    'comment_id'  => $comment->comment_ID,
                    'message'     => __('Your comment has been sent successfully!', TEXT_DOMAIN)
                ], 200);

            } catch ( Exception $e ) {
                // Catch any unexpected errors
                wp_send_json_error([
                    'message' => sprintf(__('Unexpected error: %s', TEXT_DOMAIN), $e->getMessage())
                ], 500);
            }
        }

        // Register AJAX handlers for logged-in and non-logged-in users
        add_action( 'wp_ajax_ajaxcomments', 'comment_form_handler' );
        add_action( 'wp_ajax_nopriv_ajaxcomments', 'comment_form_handler' );
    }
