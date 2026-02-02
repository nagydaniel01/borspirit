<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    
    /**
     * Conditionally disable comments and pingbacks if they are enabled in admin settings.
     * This allows site-wide suppression, but only if the settings are turned on.
     */
    
    if ( ! function_exists( 'conditionally_disable_comments' ) ) {
        /**
         * Disable comments site-wide if the site allows them in settings.
         */
        function conditionally_disable_comments() {
            $comments_allowed = get_option('default_comment_status') === 'open';
            $pings_allowed    = get_option('default_ping_status') === 'open';

            // If neither are enabled, exit early
            if ($comments_allowed && $pings_allowed) {
                return;
            }

            // Remove comment and ping support from all post types
            foreach (get_post_types() as $post_type) {
                if (post_type_supports($post_type, 'comments')) {
                    remove_post_type_support($post_type, 'comments');
                    remove_post_type_support($post_type, 'trackbacks');
                }
            }

            // Disable comments/pings on the frontend
            add_filter('comments_open', '__return_false', 20, 2);
            add_filter('pings_open', '__return_false', 20, 2);

            // Hide existing comments
            add_filter('comments_array', '__return_empty_array', 10, 2);

            // Remove comments page in admin
            add_action('admin_menu', function() {
                remove_menu_page('edit-comments.php');
            });

            // Redirect from comments admin page if accessed directly
            add_action('admin_init', function() {
                global $pagenow;
                if ($pagenow === 'edit-comments.php') {
                    wp_redirect(admin_url()); exit;
                }
            });

            // Remove comments from dashboard
            add_action('admin_init', function() {
                remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
            });

            // Remove comments from admin bar
            add_action('init', function() {
                if (is_admin_bar_showing()) {
                    remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
                }
            });
        }

        add_action( 'init', 'conditionally_disable_comments' );
    }