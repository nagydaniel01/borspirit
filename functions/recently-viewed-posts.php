<?php
    if ( ! function_exists( 'start_custom_session' ) ) {
        /**
         * Start a custom session if not already started.
         */
        function start_custom_session() {
            if ( ! session_id() ) {
                session_start();
            }
        }

        add_action( 'init', 'start_custom_session' );
    }

    if ( ! function_exists( 'add_recently_viewed' ) ) {
        /**
         * Add a post to the recently viewed list stored in the session.
         *
         * @param int $post_id The ID of the post to add to the recently viewed list.
         */
        function add_recently_viewed( $post_id ) {
            if ( ! isset( $_SESSION['recently_viewed'] ) ) {
                $_SESSION['recently_viewed'] = [];
            }

            array_unshift( $_SESSION['recently_viewed'], $post_id );
            $_SESSION['recently_viewed'] = array_unique( $_SESSION['recently_viewed'] );
        }
    }

    if ( ! function_exists( 'track_recently_viewed' ) ) {
        /**
         * Track the recently viewed post and store it in the session.
         *
         * @param int $post_id The ID of the post being tracked.
         */
        function track_recently_viewed( $post_id ) {
            if ( ! is_single() ) {
                return;
            }

            if ( empty( $post_id ) ) {
                global $post;
                $post_id = $post->ID;
            }

            add_recently_viewed( $post_id );
        }

        add_action( 'wp_head', 'track_recently_viewed' );
    }

    if ( ! function_exists( 'get_recently_viewed' ) ) {
        /**
         * Get the list of recently viewed posts from the session.
         *
         * @return array An array of post IDs of recently viewed posts.
         */
        function get_recently_viewed() {
            if ( isset( $_SESSION['recently_viewed'] ) ) {
                return $_SESSION['recently_viewed'];
            }

            return [];
        }
    }
