<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Log function
 */
function wp_log_action( $message ) {
    // Define the log directory inside your WordPress root
    $log_dir = ABSPATH . 'logs/';

    // Create the folder if it doesn't exist
    if ( ! file_exists( $log_dir ) ) {
        mkdir( $log_dir, 0755, true );
    }

    // Define the log file path
    $log_file = $log_dir . 'wp-action-log.txt';

    // Prepare the log entry
    $date = date('Y-m-d H:i:s');
    $entry = "[$date] $message" . PHP_EOL;

    // Append to the log file
    file_put_contents( $log_file, $entry, FILE_APPEND );
}

/* ---------------------------
   Capture Old Versions Before Updates
----------------------------*/

// Plugins
add_filter('pre_set_site_transient_update_plugins', function($transient) {
    set_transient('wp_plugin_old_versions', get_plugins());
    return $transient;
});

// Themes
add_filter('pre_set_site_transient_update_themes', function($transient) {
    $old_versions = [];
    foreach (wp_get_themes() as $slug => $theme) {
        $old_versions[$slug] = $theme->get('Version');
    }
    set_transient('wp_theme_old_versions', $old_versions);
    return $transient;
});

// Core
add_action('pre_current_active_plugins', function() {
    // store current WP version before updates
    set_transient('wp_core_old_version', get_option('wp_version'));
});

/* ---------------------------
   Plugin Hooks
----------------------------*/

// Plugin activated
add_action( 'activated_plugin', function($plugin) {
    wp_log_action( "Plugin activated: $plugin" );
});

// Plugin deactivated
add_action( 'deactivated_plugin', function($plugin) {
    wp_log_action( "Plugin deactivated: $plugin" );
});

// Plugin deleted
add_action( 'deleted_plugin', function($plugin) {
    wp_log_action( "Plugin deleted: $plugin" );
});

// Plugin updated
add_action( 'upgrader_process_complete', function($upgrader_object, $options) {
    if ( $options['type'] === 'plugin' && !empty($options['plugins']) ) {
        $old_plugins = get_transient('wp_plugin_old_versions') ?: [];
        foreach ( $options['plugins'] as $plugin_file ) {
            $old_version = $old_plugins[$plugin_file]['Version'] ?? 'unknown';
            $new_version = get_plugins()[ $plugin_file ]['Version'] ?? 'unknown';
            wp_log_action( "Plugin updated: $plugin_file (from $old_version to $new_version)" );
        }
        delete_transient('wp_plugin_old_versions');
    }
}, 10, 2);

/* ---------------------------
   Theme Hooks
----------------------------*/

// Theme switched
add_action( 'switch_theme', function($new_theme_name, $new_theme) {
    wp_log_action( "Theme switched to: $new_theme_name" );
}, 10, 2);

// Theme deleted
add_action( 'delete_theme', function($stylesheet) {
    wp_log_action( "Theme deleted: $stylesheet" );
}, 10, 1);

// Theme updated
add_action( 'upgrader_process_complete', function($upgrader_object, $options) {
    if ( $options['type'] === 'theme' && !empty($options['themes']) ) {
        $old_versions = get_transient('wp_theme_old_versions') ?: [];
        foreach ( $options['themes'] as $theme_slug ) {
            $old_version = $old_versions[$theme_slug] ?? 'unknown';
            $new_version = wp_get_theme($theme_slug)->get('Version');
            wp_log_action( "Theme updated: $theme_slug (from $old_version to $new_version)" );
        }
        delete_transient('wp_theme_old_versions');
    }
}, 10, 2);

/* ---------------------------
   WordPress Core Updates
----------------------------*/

add_action( 'upgrader_process_complete', function($upgrader_object, $options) {
    if ( $options['type'] === 'core' ) {
        $old_version = get_transient('wp_core_old_version') ?: 'unknown';
        $new_version = get_bloginfo('version');
        wp_log_action( "WordPress core updated (from $old_version to $new_version)" );
        delete_transient('wp_core_old_version');
    }
}, 10, 2);
