<?php
    if ( ! function_exists( 'clean_wp_head' ) ) {
        /**
         * Clean up unnecessary elements from the WordPress <head> section.
         *
         * This function removes default WordPress actions that inject metadata, links, and scripts
         * into the <head> of your HTML. These can be removed to improve performance and reduce clutter.
         *
         * Note: This keeps comment functionality intact.
         */
        function clean_wp_head() {
            // Remove the WordPress version number
            remove_action( 'wp_head', 'wp_generator' );
        
            // Remove Really Simple Discovery (RSD) link
            remove_action( 'wp_head', 'rsd_link' );
        
            // Remove Windows Live Writer manifest link
            remove_action( 'wp_head', 'wlwmanifest_link' );
        
            // Remove shortlink for the current page
            remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
        
            // Remove REST API link tag
            remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
        
            // Remove oEmbed discovery links
            remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
        
            // Remove oEmbed host JavaScript
            remove_action( 'wp_head', 'wp_oembed_add_host_js' );
        
            // Remove adjacent posts relational links
            remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
        
            // Disable emoji scripts and styles
            remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
            remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
            remove_action( 'wp_print_styles', 'print_emoji_styles' );
            remove_action( 'admin_print_styles', 'print_emoji_styles' );
        
            // Remove default feed links (RSS)
            remove_action( 'wp_head', 'feed_links', 2 );
            remove_action( 'wp_head', 'feed_links_extra', 3 );
        }

        add_action( 'init', 'clean_wp_head' );
    }

    if ( ! function_exists( 'dequeue_unwanted_styles' ) ) {
        /**
         * Dequeue unwanted frontend styles to reduce CSS bloat.
         *
         * This removes default Gutenberg, WooCommerce, and global theme styles.
         */
        function dequeue_unwanted_styles() {
            // Dequeue block editor styles
            wp_dequeue_style( 'wp-block-library' );
            wp_dequeue_style( 'wp-block-library-theme' );

            // Dequeue WooCommerce block styles
            wp_dequeue_style( 'wc-block-style' );

            // Dequeue Classic and Global Theme styles
            wp_dequeue_style( 'classic-theme-styles' );
            wp_dequeue_style( 'global-styles' );
        }

        add_action( 'wp_enqueue_scripts', 'dequeue_unwanted_styles', 100 );
    }

    if ( ! function_exists( 'load_jquery_by_cdn' ) ) {
        /**
         * Load jQuery from Google's CDN on the frontend.
         *
         * This function replaces the default WordPress jQuery core script
         * URL with the corresponding version hosted on Google's CDN.
         * It only runs on the frontend (non-admin pages).
         *
         * Note: This function modifies the global $wp_scripts object directly,
         * changing the 'jquery-core' script's source URL to the CDN URL.
         *
         * @return void
         */
        function load_jquery_by_cdn() {
            if (is_admin()) {
                return;
            }

            $protocol = is_ssl() ? 'https' : 'http';

            /** @var WP_Scripts $wp_scripts */
            global $wp_scripts;

            // Modify jQuery Core to load from CDN
            if (isset($wp_scripts->registered['jquery-core'])) {
                $core = $wp_scripts->registered['jquery-core'];
                $core_version = $core->ver;
                $core->src = "$protocol://ajax.googleapis.com/ajax/libs/jquery/$core_version/jquery.min.js";
            }

            // Ensure 'jquery' depends only on 'jquery-core'
            if (isset($wp_scripts->registered['jquery'])) {
                $jquery = $wp_scripts->registered['jquery'];
                $jquery->deps = ['jquery-core'];
            }
        }
        
        add_action( 'init', 'load_jquery_by_cdn', 20 );
    }

    if ( ! function_exists( 'add_jquery_by_cdn' ) ) {
        /**
         * Register and enqueue jQuery from Google's CDN in the footer.
         *
         * This function deregisters the default WordPress jQuery script and
         * registers a new one from Google's CDN, using the jQuery version
         * currently registered in WordPress core (or a default fallback).
         * The script is loaded in the footer for better page load performance.
         *
         * This runs only on the frontend, not in admin pages.
         *
         * @return void
         */
        function add_jquery_by_cdn() {
            // Only modify scripts on the frontend
            if ( is_admin() ) {
                return;
            }

            global $wp_scripts;

            // Ensure $wp_scripts is initialized and is an instance of WP_Scripts
            if ( ! ( $wp_scripts instanceof WP_Scripts ) ) {
                return;
            }

            // Default jQuery version fallback
            $default_version = '3.7.1';
            $jquery_version  = $default_version;

            // Attempt to get jQuery version from 'jquery-core'
            if ( isset( $wp_scripts->registered['jquery-core'] ) && is_object( $wp_scripts->registered['jquery-core'] ) ) {
                $core_script = $wp_scripts->registered['jquery-core'];

                if ( isset( $core_script->ver ) && is_string( $core_script->ver ) && preg_match( '/^\d+\.\d+(\.\d+)?$/', $core_script->ver ) ) {
                    $jquery_version = $core_script->ver;
                }
            }

            $protocol       = is_ssl() ? 'https' : 'http';
            $cdn_url        = "{$protocol}://ajax.googleapis.com/ajax/libs/jquery/{$jquery_version}/jquery.min.js";

            // Deregister jQuery if it's already registered
            if ( wp_script_is( 'jquery', 'registered' ) ) {
                wp_deregister_script( 'jquery' );
            }

            // Register and enqueue the CDN jQuery version in the footer
            wp_register_script( 'jquery', esc_url( $cdn_url ), [], $jquery_version, true );
            wp_enqueue_script( 'jquery' );
        }

        add_action( 'wp_enqueue_scripts', 'add_jquery_by_cdn', 20 );
    }

    if ( ! function_exists( 'add_defer_to_scripts' ) ) {
        /**
         * Adds the 'defer' attribute to script tags for non-logged-in users, excluding jQuery.
         *
         * @param string $tag The HTML script tag.
         * @param string $handle The script's registered handle.
         * @param string $src The script source URL.
         * @return string Modified script tag with 'defer' attribute if appropriate.
         */
        function add_defer_to_scripts( $tag, $handle, $src ) {
            // Only apply for non-logged-in users
            if ( is_user_logged_in() ) {
                return $tag;
            }

            // Skip jQuery and any known dependencies that shouldn't be deferred
            $excluded_handles = array(
                'jquery',
                'jquery-core',
                'jquery-migrate',
            );

            if ( in_array( $handle, $excluded_handles, true ) ) {
                return $tag;
            }

            // Ensure the tag contains a JS file
            if ( strpos( $src, '.js' ) === false ) {
                return $tag;
            }

            // Add 'defer' if not already present
            if ( false === strpos( $tag, ' defer' ) ) {
                $tag = str_replace( '<script ', '<script defer ', $tag );
            }

            return $tag;
        }

        //add_filter( 'script_loader_tag', 'add_defer_to_scripts', 10, 3 );
    }

    if ( ! function_exists( 'remove_jquery_migrate' ) ) {
        /**
         * Optional: Remove jQuery Migrate if not needed on the front end.
         *
         * @param WP_Scripts $scripts The WP_Scripts object.
         */
        function remove_jquery_migrate( $scripts ) {
            if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
                $script = $scripts->registered['jquery'];
                if ( $script->deps ) {
                    $script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
                }
            }
        }

        add_action( 'wp_default_scripts', 'remove_jquery_migrate' );
    }

    //add_filter( 'doing_it_wrong_trigger_error', '__return_false' );
    add_filter( 'wp_img_tag_add_auto_sizes', '__return_false' );