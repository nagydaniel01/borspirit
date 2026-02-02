<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! function_exists( 'start_custom_session' ) ) {
        /**
         * Start a custom session if not already started.
         */
        function start_custom_session() {
            if ( ! session_id() ) {
                session_start();
            }
        }
        add_action( 'init', 'start_custom_session', 1 );
    }

    if ( ! function_exists( 'add_recently_viewed' ) ) {
        /**
         * Add a post to the recently viewed list stored in the session.
         *
         * @param int $post_id The ID of the post to add to the recently viewed list.
         */
        function add_recently_viewed( $post_id ) {
            if ( ! isset( $_SESSION['recently_viewed'] ) || ! is_array( $_SESSION['recently_viewed'] ) ) {
                $_SESSION['recently_viewed'] = [];
            }

            // Add to the beginning of the array
            array_unshift( $_SESSION['recently_viewed'], $post_id );
            $_SESSION['recently_viewed'] = array_unique( $_SESSION['recently_viewed'] );

            // Optional: limit the number of items stored (e.g., last 10 posts)
            //$_SESSION['recently_viewed'] = array_slice( $_SESSION['recently_viewed'], 0, 10 );

            // Immediately close the session to prevent blocking other requests
            session_write_close();
        }
    }

    if ( ! function_exists( 'track_recently_viewed' ) ) {
        /**
         * Track the recently viewed post and store it in the session.
         */
        function track_recently_viewed() {
            if ( ! is_single() ) {
                return;
            }

            global $post;
            if ( empty( $post->ID ) ) {
                return;
            }

            add_recently_viewed( $post->ID );
        }
        add_action( 'wp', 'track_recently_viewed' );
    }

    if ( ! function_exists( 'get_recently_viewed' ) ) {
        /**
         * Get the list of recently viewed posts from the session.
         *
         * @return array An array of post IDs of recently viewed posts.
         */
        function get_recently_viewed() {
            if ( isset( $_SESSION['recently_viewed'] ) && is_array( $_SESSION['recently_viewed'] ) ) {
                return $_SESSION['recently_viewed'];
            }

            return [];
        }
    }
