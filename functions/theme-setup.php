<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! function_exists( 'theme_setup' ) ) {
        /**
         * Sets up theme defaults and registers support for various WordPress features.
         *
         * Note that this function is hooked into the after_setup_theme hook, which runs
         * before the init hook. The init hook is too late for some features, such as indicating
         * support post thumbnails.
         * 
         * @return void
         * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/
         */
        function theme_setup() {
            /** 
             * Post thumbnail
             */
            add_theme_support( 'post-thumbnails' );
            set_post_thumbnail_size( 150, 150 ); // 50 pixels wide by 50 pixels tall, resize mode

            // Add image sizes
            add_image_size( 'post-thumbnail', 500, 500, true );
            //add_image_size('post-thumbnail', 500, 9999);
            //add_image_size( 'icon', 24, 24, true );
            //add_image_size( 'avatar', 64, 64, true );

            /**
             * Supported formats
             * @link https://developer.wordpress.org/themes/functionality/post-formats/#supported-formats
             */
            add_theme_support( 
                'post-formats', 
                array(
                    'aside',
                    'gallery',
                    'link',
                    'image',
                    'quote',
                    'status',
                    'video',
                    'audio',
                    'chat'
                ) 
            );
            /* Supported formats END */

            /**
             * Feed Links
             */
            add_theme_support( 'automatic-feed-links' );
            
            /**
             * Title Tag
             */
            add_theme_support( 'title-tag' );

            /**
             * HTML5
             */
            add_theme_support(
                'html5', 
                array( 
                    'comment-list', 
                    'comment-form', 
                    'search-form', 
                    'gallery', 
                    'caption', 
                    'style', 
                    'script'
                )
            );

            /**
             *  Custom Logo
             */
            add_theme_support( 
                'custom-logo', 
                array(
                    'height'               => 100,
                    'width'                => 400,
                    'flex-height'          => true,
                    'flex-width'           => true,
                    'header-text'          => array( 'site-title', 'site-description' ),
                    'unlink-homepage-logo' => true,
                ) 
            );

            /**
             * Editor Style.
             */
            add_editor_style( 'classic-editor.css' );

            /**
             * Block Editor Theme Support
             */
            add_theme_support( 'align-wide' );
            add_theme_support( 'wp-block-styles' );
            add_theme_support( 'responsive-embeds' );

            /**
             * Register theme support for Rank Math breadcrumbs
             */
            add_theme_support( 'rank-math-breadcrumbs' );

            /**
             * WooCommerce.
             */
            // WooCommerce in general.
            add_theme_support( 'woocommerce' );

            // Add support for LearnDash features
            add_theme_support( 'learndash' );

            // Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
            add_theme_support( 'wc-product-gallery-zoom' );       // zoom
            add_theme_support( 'wc-product-gallery-lightbox' );   // lightbox
            add_theme_support( 'wc-product-gallery-slider' );     // swipe

            // Restoring the classic Widgets Editor
            remove_theme_support( 'widgets-block-editor' );

            // Register menu
            register_nav_menus(
                array(
                    'primary_menu'      => __( 'Header menu', TEXT_DOMAIN ),
                    'footer_menu_1'     => __( 'Footer menu 1', TEXT_DOMAIN ),
                    'footer_menu_2'     => __( 'Footer menu 2', TEXT_DOMAIN ),
                    'footer_menu_3'     => __( 'Footer menu 3', TEXT_DOMAIN ),
                    'footer_menu_4'     => __( 'Footer menu 4', TEXT_DOMAIN )
                )
            );

        }

        add_action( 'after_setup_theme', 'theme_setup' );
    }

    if ( ! function_exists( 'theme_textdomain_init' ) ) {
        /**
         * Loads the theme textdomain for translations.
         *
         * @return void
         */
        function theme_textdomain_init() {
            load_theme_textdomain( TEXT_DOMAIN, get_template_directory() . '/languages' );
            //load_theme_textdomain( TEXT_DOMAIN, WP_LANG_DIR . '/themes' );
        }
        add_action( 'init', 'theme_textdomain_init' );
    }

    if ( ! function_exists( 'theme_init' ) ) {
        /**
         * Initializes additional features like taxonomy support for custom post types.
         *
         * @return void
         */
        function theme_init() {
            //add_post_type_support( 'post', 'post-formats' );
            //add_post_type_support( 'page', 'excerpt' );

            //remove_post_type_support( 'post', 'post-formats' );
            //remove_post_type_support( 'page', 'excerpt' );

            register_taxonomy_for_object_type( 'post_tag', 'announcement' );
            register_taxonomy_for_object_type( 'post_tag', 'event' );
            register_taxonomy_for_object_type( 'post_tag', 'podcast' );
            register_taxonomy_for_object_type( 'post_tag', 'press' );
            register_taxonomy_for_object_type( 'post_tag', 'project' );
            register_taxonomy_for_object_type( 'post_tag', 'publication' );
            register_taxonomy_for_object_type( 'post_tag', 'research' );
            register_taxonomy_for_object_type( 'post_tag', 'video' );
        }

        add_action( 'init', 'theme_init' );
    }

    // Enable native lazy loading for images
    add_filter( 'wp_lazy_loading_enabled', '__return_true' );

    /*
    if ( wp_lazy_loading_enabled( 'img', 'wp_get_attachment_image' ) ) {
        echo 'Lazy loading is enabled';
    } else {
        echo 'Lazy loading is not enabled';
    }
    */

    if ( ! function_exists( 'force_lazyload_wp_block_images' ) ) {
        /**
         * Force lazy loading on all <img> tags inside wp-block-image blocks in post content.
         *
         * This function scans the post content and ensures that every <img> tag within
         * `wp-block-image` blocks has the `loading="lazy"` attribute. If the attribute
         * is missing, it will be added.
         *
         * @param string $content The post content.
         *
         * @return string Modified post content with enforced lazy loading on images.
         */
        function force_lazyload_wp_block_images( $content ) {
            // Add lazyload to <img> inside wp-block-image if missing
            $content = preg_replace(
                '/(<img[^>]+)(?<!loading=["\']lazy["\'])/',
                '$1 loading="lazy"',
                $content
            );
            return $content;
        }
        add_filter( 'the_content', 'force_lazyload_wp_block_images' );
    }

    if ( ! function_exists( 'theme_body_classes' ) ) {
        /**
         * Modifies the body_class output to remove unwanted classes.
         *
         * @param array $classes The current body classes.
         * @return array Modified body classes.
         */
        function theme_body_classes( $classes ) {
            // Remove the 'page' class
            if ( ( $key = array_search( 'page', $classes ) ) !== false ) {
                unset( $classes[ $key ] );
            }
        
            // Add a custom class
            //$classes[] = 'my-custom-class';

            $is_woocommerce_page = false;

            // Only check WooCommerce conditions if plugin is active
            if ( class_exists( 'WooCommerce' ) ) {
                if ( is_woocommerce() ) {
                    $is_woocommerce_page = true;
                }
            }

            // If not a WooCommerce page, add the 'woocommerce' class
            if ( ! $is_woocommerce_page && ! in_array( 'woocommerce', $classes, true ) ) {
                $classes[] = 'woocommerce';
            }
        
            return $classes;
        }

        add_filter( 'body_class', 'theme_body_classes' );
    }

    if ( ! function_exists( 'theme_mime_types' ) ) {
        /**
         * Adds SVG file support to the list of allowed mime types for uploads.
         *
         * @param array $mimes Existing allowed mime types.
         * @return array Modified mime types.
         */
        function theme_mime_types( $mimes ) {
            $mimes['svg'] = 'image/svg+xml';
            return $mimes;
        }
        add_filter( 'upload_mimes', 'theme_mime_types' );
    }

    if ( ! function_exists( 'add_all_settings_menu' ) ) {
        /**
         * Adds a link to "All Settings" in the WordPress admin menu (for administrators only).
         *
         * @return void
         */
        function add_all_settings_menu() {
            if ( is_admin() && current_user_can('administrator') ) {
                add_options_page( 
                    __('All Settings'), 
                    __('All Settings'), 
                    'administrator', 
                    'options.php' 
                );
            }
        }

        add_action( 'admin_menu', 'add_all_settings_menu' );
    }

    if ( ! function_exists( 'custom_restrict_options_admin_access' ) ) {
        /**
         * Restricts access to certain admin settings pages for non-primary administrators.
         *
         * @return void
         */
        function custom_restrict_options_admin_access() {
            $user = wp_get_current_user();

            if ( in_array('administrator', (array) $user->roles, true) && $user->ID !== 1 ) {
                // Remove menu items
                add_action( 'admin_menu', 'custom_remove_options_admin_menus', 999 );

                // Block access to specific admin pages
                add_action( 'admin_enqueue_scripts', 'custom_block_options_admin_pages' );
            }
        }
        add_action( 'init', 'custom_restrict_options_admin_access' );
    }

    if ( ! function_exists( 'custom_remove_options_admin_menus' ) ) {
        /**
         * Removes specific admin menus for secondary administrators.
         *
         * @return void
         */
        function custom_remove_options_admin_menus() {
            $menus_to_remove = [
                //'options-general.php', // Settings menu
            ];
    
            foreach ($menus_to_remove as $menu) {
                remove_menu_page($menu);
            }
    
            $submenus_to_remove = [
                ['options-general.php', 'options.php'], // "All Settings"
            ];
    
            foreach ($submenus_to_remove as $submenu) {
                remove_submenu_page($submenu[0], $submenu[1]);
            }
        }
    }
    
    if ( ! function_exists( 'custom_block_options_admin_pages' ) ) {
        /**
         * Blocks direct access to restricted admin pages for secondary administrators.
         *
         * @param string $hook The current admin page hook.
         * @return void
         */
        function custom_block_options_admin_pages( $hook ) {
            $restricted_hooks = [
                'options.php', // Block direct access to "All Settings"
            ];
    
            if ( in_array($hook, $restricted_hooks, true) ) {
                wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
            }
        }
    }