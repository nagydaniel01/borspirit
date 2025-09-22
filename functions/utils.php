<?php
    if ( ! function_exists( 'get_current_url' ) ) {
        /**
         * Get the current URL of the page.
         * 
         * @return string Current URL.
         */
        function get_current_url() {
            global $wp;

            return esc_url( trailingslashit( home_url( add_query_arg( array(), $wp->request ) ) ) );
        }
    }

    if ( ! function_exists( 'get_current_slug' ) ) {
        /**
         * Get the current page slug.
         * 
         * @return string Current page slug.
         */
        function get_current_slug() {
            global $wp;

            return add_query_arg( array(), $wp->request );
        }
    }

    if ( ! function_exists( 'get_template_id' ) ) {
        /**
         * Retrieves the ID of the first page using a specified page template.
         *
         * @param string $template_name The name of the page template file.
         * @return int|null ID of the page if found, null otherwise.
         */
        function get_template_id( $template_name ) {
            $page = get_pages( array(
                'hierarchical' => false,
                'meta_key'     => '_wp_page_template',
                'meta_value'   => $template_name,
                'number'       => 1,
            ) );
            
            if ( ! empty( $page ) && isset( $page[0]->ID ) ) {
                $page_id = $page[0]->ID;
                return $page_id;
            }
            return null;
        }
    }

    if ( ! function_exists( 'get_template_url' ) ) {
        /**
         * Retrieves the permalink of the first page using a specified page template.
         *
         * @param string $template_name The name of the page template file.
         * @return string|null Permalink of the page if found, null otherwise.
         */
        function get_template_url( $template_name ) {
            $page = get_pages( array(
                'hierarchical' => false,
                'meta_key'     => '_wp_page_template',
                'meta_value'   => $template_name,
                'number'       => 1,
            ) );

            if ( ! empty( $page ) && isset( $page[0]->ID ) ) {
                $permalink = get_permalink( $page[0]->ID );
                return $permalink ? $permalink : null; // ensure null instead of false
            }

            return null;
        }
    }

    if ( ! function_exists( 'get_template_name' ) ) {
        /**
         * Get the human-readable template name for a given post ID.
         *
         * @param int $post_id The ID of the post.
         * @return string The template name or 'Default template' if not custom.
         */
        function get_template_name( $post_id ) {
            if (!get_post($post_id)) {
                return '';
            }

            $template = get_post_meta($post_id, '_wp_page_template', true);

            if ($template === 'default' || empty($template)) {
                return __('Default template');
            }

            $template_path = locate_template($template);

            if (file_exists($template_path)) {
                $template_data = get_file_data($template_path, array('name' => 'Template Name'));
                return !empty($template_data['name']) ? $template_data['name'] : basename($template);
            }

            return basename($template); // fallback if file not found
        }
    }

    if ( ! function_exists( 'load_template_part' ) ) {
        /**
         * Loads a template part into a variable instead of displaying it.
         *
         * @param string $template_name Template slug.
         * @param string|null $part_name Optional. Template part name.
         * @return string Template part contents.
         */
        function load_template_part( $template_name, $part_name = null ) {
            ob_start();
            get_template_part( $template_name, $part_name );
            $var = ob_get_contents();
            ob_end_clean();
            return $var;
        }
    }

    if ( ! function_exists( 'get_post_id_by_meta' ) ) {
        /**
         * Get a post ID by a specific post meta key and value.
         *
         * @param string $key   The meta key to search for.
         * @param string $value The meta value to match.
         *
         * @return int|null Post ID if found, null otherwise.
         */
        function get_post_id_by_meta( $key, $value ) {
            global $wpdb;

            $query = $wpdb->prepare(
                "SELECT post_id 
                FROM {$wpdb->postmeta} 
                WHERE meta_key = %s 
                AND meta_value = %s 
                LIMIT 1",
                $key,
                $value
            );

            $post_id = $wpdb->get_var( $query );

            return $post_id ? (int) $post_id : null;
        }
    }

    if ( ! function_exists( 'wp_safe_format_date' ) ) {
        /**
         * Safely format a date string into WordPress date format.
         *
         * @param mixed  $date_str       The input date string.
         * @param string $input_format   The format of the input date string. Default is 'd/m/Y'.
         * @param string $output_format  The desired output format. Default is the WordPress date format option.
         * @return string Formatted date or fallback message.
         */
        function wp_safe_format_date( $date_str, $input_format = 'd/m/Y', $output_format = '' ) {
            // Define fallback message
            $fallback = 'Invalid date.';

            // Use WordPress date format if no output format is provided
            if ( empty( $output_format ) ) {
                $output_format = get_option('date_format');
            }

            // Check if input is empty or not a string
            if ( empty($date_str) || !is_string($date_str) ) {
                return $fallback;
            }

            // Try to create a DateTime object from the input string
            try {
                $date = DateTime::createFromFormat( $input_format, $date_str );

                // Check for parsing errors
                $errors = DateTime::getLastErrors();
                if ( $date === false || ( $errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0) ) ) {
                    return $fallback;
                }

                // Format date according to the specified output format
                return date_i18n( $output_format, $date->getTimestamp() );

            } catch ( Exception $e ) {
                // Catch any unexpected exceptions
                return $fallback;
            }
        }
    }

    if ( ! function_exists( 'wp_safe_format_time' ) ) {
        /**
         * Safely format a time string into WordPress time format.
         * Handles multiple languages for AM/PM notation.
         *
         * @param mixed  $time_str       The input time string.
         * @param string $input_format   The format of the input time string. Default is 'h:i A'.
         * @param string $output_format  The desired output format. Default is the WordPress time format option.
         * @return string Formatted time or fallback message.
         */
        function wp_safe_format_time( $time_str, $input_format = 'H:i', $output_format = '' ) {
            $fallback = 'Invalid time.';

            if ( empty( $output_format ) ) {
                $output_format = get_option('time_format');
            }

            if ( empty($time_str) || !is_string($time_str) ) {
                return $fallback;
            }

            // Map common AM/PM notations in different languages to English
            $am_pm_map = [
                'am' => ['am', 'a.m.', 'vorm.', 'de.'], // English, German (vorm.), Hungarian (de.)
                'pm' => ['pm', 'p.m.', 'nachm.', 'du.'] // English, German (nachm.), Hungarian (du.)
            ];

            foreach ( $am_pm_map as $eng => $variants ) {
                $time_str = str_ireplace( $variants, $eng, $time_str );
            }

            try {
                $time = DateTime::createFromFormat( $input_format, $time_str );
                $errors = DateTime::getLastErrors();

                if ( $time === false || ( $errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0) ) ) {
                    return $fallback;
                }
                
                return date_i18n( $output_format, $time->getTimestamp() );
            } catch ( Exception $e ) {
                return $fallback;
            }
        }
    }

    if ( ! function_exists( 'wp_format_file_size' ) ) {
        /**
         * Format bytes into a human-readable file size string.
         *
         * @param int $bytes    The file size in bytes.
         * @param int $decimals The number of decimal places to include (default is 0).
         * @return string       The formatted file size string (e.g., "2 MB").
         */
        function wp_format_file_size($bytes, $decimals = 0) {
            $size = ['B','KB','MB','GB','TB'];
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor];
        }
    }

    if ( ! function_exists( 'is_external_url' ) ) {
        /**
         * Check if a given URL is external.
         *
         * @param string      $url      The URL to check.
         * @param string|null $site_url Optional. The base site URL. Defaults to get_home_url().
         * @return bool True if the URL is external, false otherwise.
         */
        function is_external_url( $url, $site_url = null ) {
            if ( ! $site_url ) {
                $site_url = get_home_url();
            }

            $url = trim( $url );
            $site_url = rtrim( trim( $site_url ), '/' );

            // Ensure the URL is absolute
            if ( ! $url || strpos( $url, 'http' ) !== 0 ) {
                return false;
            }

            $url_host      = parse_url( $url, PHP_URL_HOST );
            $site_url_host = parse_url( $site_url, PHP_URL_HOST );

            return $url_host && $site_url_host && strcasecmp( $url_host, $site_url_host ) !== 0;
        }
    }

    if ( ! function_exists( 'get_estimated_reading_time' ) ) {
        /**
         * Estimate the reading time for content.
         *
         * @param string $content Content to analyze.
         * @param int    $wpm     Words per minute reading speed. Default is 300.
         *
         * @return int Estimated reading time in minutes.
         */
        function get_estimated_reading_time( $content = '', $wpm = 300 ) {
            $clean_content = strip_tags( strip_shortcodes( $content ) );
            $word_count    = str_word_count( $clean_content );

            return ceil( $word_count / $wpm );
        }
    }

    if ( ! function_exists( 'get_youtube_video_id' ) ) {
        /**
         * Extracts the YouTube video ID from a string containing a YouTube URL or iframe.
         *
         * Supports the following URL formats:
         * - https://www.youtube.com/watch?v=VIDEO_ID
         * - https://youtu.be/VIDEO_ID
         * - https://www.youtube.com/embed/VIDEO_ID
         * - With or without query parameters
         *
         * @param string $input A YouTube iframe HTML string or URL.
         * @return string|false The extracted YouTube video ID, or false if not found.
         */
        function get_youtube_video_id( $input ) {
            // Try multiple common YouTube URL patterns
            $patterns = [
                '/youtube\.com\/watch\?v=([^\&"\'>]+)/',    // watch?v=VIDEO_ID
                '/youtube\.com\/embed\/([^\?"\'>]+)/',      // embed/VIDEO_ID
                '/youtu\.be\/([^\?"\'>]+)/',                // youtu.be/VIDEO_ID
                '/youtube\.com\/v\/([^\&\?\/]+)/',          // /v/VIDEO_ID (old flash embeds)
            ];
    
            foreach ( $patterns as $pattern ) {
                if ( preg_match( $pattern, $input, $matches ) ) {
                    return $matches[1] ?? '';
                }
            }
    
            return false;
        }
    }

    if ( ! function_exists( 'normalize_youtube_url' ) ) {
        /**
         * Normalizes any valid YouTube URL (embed, shortlink, watch, etc.)
         * into a standard YouTube watch URL.
         *
         * Supported formats:
         * - https://www.youtube.com/watch?v=VIDEO_ID
         * - https://youtu.be/VIDEO_ID
         * - https://www.youtube.com/embed/VIDEO_ID
         * - With or without query parameters
         *
         * @param string $url The YouTube URL in any supported format.
         * @return string|false The normalized watch URL, or false if no video ID found.
         */
        function normalize_youtube_url($url) {
            // Try multiple common YouTube URL patterns
            $patterns = [
                '/youtube\.com\/watch\?v=([^\&\?\/]+)/',    // watch?v=VIDEO_ID
                '/youtube\.com\/embed\/([^\&\?\/]+)/',      // embed/VIDEO_ID
                '/youtu\.be\/([^\&\?\/]+)/',                // youtu.be/VIDEO_ID
                '/youtube\.com\/v\/([^\&\?\/]+)/',          // /v/VIDEO_ID (old flash embeds)
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $url, $matches)) {
                    return 'https://www.youtube.com/watch?v=' . $matches[1] ?? '';
                }
            }

            return false; // No match found
        }
    }

    if ( ! function_exists( 'get_youtube_thumbnail_url' ) ) {
        /**
         * Extract YouTube video ID from various URL formats and return a thumbnail URL.
         *
         * @param string $url     The YouTube video URL (can be embed, share, watch, etc.)
         * @param string $quality The desired thumbnail quality: default, mqdefault, hqdefault, sddefault, maxresdefault, 0, 1, 2, 3
         *
         * @return string|null    The full URL to the thumbnail image, or null if ID not found.
         */
        function get_youtube_thumbnail_url($url, $quality = 'maxresdefault') {
            // Define acceptable quality levels
            $valid_qualities = ['default', 'mqdefault', 'hqdefault', 'sddefault', 'maxresdefault', '0', '1', '2', '3'];
            $quality = in_array($quality, $valid_qualities) ? $quality : 'maxresdefault';

            // Try multiple common YouTube URL patterns
            $patterns = [
                '/youtube\.com\/watch\?v=([^\&"\'>]+)/',    // watch?v=VIDEO_ID
                '/youtube\.com\/embed\/([^\?"\'>]+)/',      // embed/VIDEO_ID
                '/youtu\.be\/([^\?"\'>]+)/',                // youtu.be/VIDEO_ID
                '/youtube\.com\/v\/([^\&\?\/]+)/',          // /v/VIDEO_ID (old flash embeds)
            ];

            foreach ( $patterns as $pattern ) {
                if ( preg_match( $pattern, $url, $matches ) ) {
                    $video_id = $matches[1];
                    return "//img.youtube.com/vi/{$video_id}/{$quality}.jpg";
                }
            }

            return null;
        }
    }

    if ( ! function_exists( 'get_add_to_calendar_url' ) ) {
        /**
         * Generate a Google Calendar event URL from an Event post.
         *
         * @param int $event_id The event post ID.
         * @return string Google Calendar URL or empty string if invalid.
         */
        function get_add_to_calendar_url( $event_id ) {
            if ( ! $event_id || ! get_post( $event_id ) ) {
                return '';
            }

            $summary     = get_the_title( $event_id );
            $description = get_the_excerpt( $event_id );
            $location    = get_field( 'event_location', $event_id )['event_location_address'] ?? '';

            $timezone = wp_timezone_string();
            $tz       = new DateTimeZone( $timezone );

            // Start fields
            $start_date_field = get_field_object( 'event_start_date', $event_id );
            $start_time_field = get_field_object( 'event_start_time', $event_id );

            // End fields
            $end_date_field = get_field_object( 'event_end_date', $event_id );
            $end_time_field = get_field_object( 'event_end_time', $event_id );

            if ( empty( $start_date_field['value'] ) || empty( $start_time_field['value'] ) ) {
                return '';
            }

            // Start DateTime
            $start_date = DateTime::createFromFormat(
                $start_date_field['return_format'] ?? 'Y-m-d',
                $start_date_field['value'],
                $tz
            );
            if ( ! $start_date ) {
                return '';
            }

            [$hour, $minute] = explode( ':', $start_time_field['value'] );
            $start_date->setTime( (int) $hour, (int) $minute );
            $start = $start_date->format( 'Ymd\THis' );

            // End DateTime
            if ( ! empty( $end_date_field['value'] ) ) {
                $end_date = DateTime::createFromFormat(
                    $end_date_field['return_format'] ?? 'Y-m-d',
                    $end_date_field['value'],
                    $tz
                );
                if ( $end_date ) {
                    if ( ! empty( $end_time_field['value'] ) ) {
                        [$end_hour, $end_minute] = explode( ':', $end_time_field['value'] );
                        $end_date->setTime( (int) $end_hour, (int) $end_minute );
                    } else {
                        $end_date->setTime( (int) $hour, (int) $minute );
                    }
                    $end = $end_date->format( 'Ymd\THis' );
                }
            }

            // Default fallback (+1h)
            if ( empty( $end ) ) {
                $end = clone $start_date;
                $end->modify( '+1 hour' );
                $end = $end->format( 'Ymd\THis' );
            }

            $calendar_url  = 'https://www.google.com/calendar/render?action=TEMPLATE';
            $calendar_url .= '&text=' . rawurlencode( $summary );
            $calendar_url .= "&dates={$start}/{$end}";
            $calendar_url .= '&details=' . rawurlencode( $description );
            $calendar_url .= '&location=' . rawurlencode( $location );
            $calendar_url .= '&ctz=' . rawurlencode( $timezone );

            return esc_url( $calendar_url );
        }
    }

    if ( ! function_exists( 'get_add_to_calendar_ics' ) ) {
        /**
         * Generate an ICS file content for Apple / Outlook Calendar.
         *
         * @param int $event_id The event post ID.
         * @return string Download URL for the ICS file or empty string if invalid.
         */
        function get_add_to_calendar_ics( $event_id ) {
            if ( ! $event_id || ! get_post( $event_id ) ) {
                return '';
            }

            $summary     = get_the_title( $event_id );
            $description = get_the_excerpt( $event_id );
            $location    = get_field( 'event_location', $event_id )['event_location_address'] ?? '';

            $timezone = wp_timezone_string();
            $tz       = new DateTimeZone( $timezone );

            // Start fields
            $start_date_field = get_field_object( 'event_start_date', $event_id );
            $start_time_field = get_field_object( 'event_start_time', $event_id );

            // End fields
            $end_date_field = get_field_object( 'event_end_date', $event_id );
            $end_time_field = get_field_object( 'event_end_time', $event_id );

            if ( empty( $start_date_field['value'] ) || empty( $start_time_field['value'] ) ) {
                return '';
            }

            // Start DateTime
            $start_date = DateTime::createFromFormat(
                $start_date_field['return_format'] ?? 'Y-m-d',
                $start_date_field['value'],
                $tz
            );
            if ( ! $start_date ) {
                return '';
            }

            [$hour, $minute] = explode( ':', $start_time_field['value'] );
            $start_date->setTime( (int) $hour, (int) $minute );
            $start = $start_date->format( 'Ymd\THis' );

            // End DateTime
            if ( ! empty( $end_date_field['value'] ) ) {
                $end_date = DateTime::createFromFormat(
                    $end_date_field['return_format'] ?? 'Y-m-d',
                    $end_date_field['value'],
                    $tz
                );
                if ( $end_date ) {
                    if ( ! empty( $end_time_field['value'] ) ) {
                        [$end_hour, $end_minute] = explode( ':', $end_time_field['value'] );
                        $end_date->setTime( (int) $end_hour, (int) $end_minute );
                    } else {
                        $end_date->setTime( (int) $hour, (int) $minute );
                    }
                    $end = $end_date->format( 'Ymd\THis' );
                }
            }

            // Default fallback (+1h)
            if ( empty( $end ) ) {
                $end = clone $start_date;
                $end->modify( '+1 hour' );
                $end = $end->format( 'Ymd\THis' );
            }

            // Build ICS content
            $ics  = "BEGIN:VCALENDAR\r\n";
            $ics .= "VERSION:2.0\r\n";
            $ics .= "PRODID:-//YourSite//NONSGML v1.0//EN\r\n";
            $ics .= "BEGIN:VEVENT\r\n";
            $ics .= "UID:" . uniqid() . "@yoursite.com\r\n";
            $ics .= "DTSTAMP:" . gmdate( 'Ymd\THis\Z' ) . "\r\n";
            $ics .= "DTSTART;TZID={$timezone}:" . $start . "\r\n";
            $ics .= "DTEND;TZID={$timezone}:" . $end . "\r\n";
            $ics .= "SUMMARY:" . esc_html( $summary ) . "\r\n";
            $ics .= "DESCRIPTION:" . esc_html( $description ) . "\r\n";
            $ics .= "LOCATION:" . esc_html( $location ) . "\r\n";
            $ics .= "END:VEVENT\r\n";
            $ics .= "END:VCALENDAR\r\n";

            // Save file in uploads/ics
            $upload_dir = wp_upload_dir();
            $ics_dir    = trailingslashit( $upload_dir['basedir'] ) . 'ics/';
            $ics_url    = trailingslashit( $upload_dir['baseurl'] ) . 'ics/';
            if ( ! file_exists( $ics_dir ) ) {
                wp_mkdir_p( $ics_dir );
            }

            $file_name = 'event-' . $event_id . '.ics';
            file_put_contents( $ics_dir . $file_name, $ics );

            return esc_url( $ics_url . $file_name );
        }
    }

    if ( ! function_exists( 'get_map_link' ) ) {
        /**
         * Generates a map link based on the user's device:
         * - Google Maps for desktop
         * - Waze for mobile devices
         *
         * @param string $address The address to be mapped.
         * @return string HTML anchor tag with the appropriate map link.
         */
        function get_map_link($address) {
            $encodedAddress = urlencode($address);
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

            // Basic mobile detection via User-Agent
            $isMobile = preg_match('/(android|iphone|ipad|ipod|mobile)/i', $userAgent);

            if ($isMobile) {
                // Waze for mobile users
                $wazeLink = "https://waze.com/ul?q={$encodedAddress}";
                return "<a href=\"{$wazeLink}\" target=\"_blank\" rel=\"noopener noreferrer\">{$address}</a>";
            } else {
                // Google Maps for desktop users
                $googleMapsLink = "https://www.google.com/maps/search/?api=1&query={$encodedAddress}";
                return "<a href=\"{$googleMapsLink}\" target=\"_blank\" rel=\"noopener noreferrer\">{$address}</a>";
            }
        }
    }

    if ( ! function_exists( 'get_route_link' ) ) {
        /**
         * Generates a route link using the user's current location as the start.
         * - Google Maps for desktop
         * - Waze for mobile
         *
         * @param string $destination The destination address.
         * @return string HTML anchor tag with the route link.
         */
        function get_route_link($destination) {
            $encodedDest = urlencode($destination);
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $isMobile = preg_match('/(android|iphone|ipad|ipod|mobile)/i', $userAgent);

            if ($isMobile) {
                // Waze route from current location to destination
                $wazeLink = "https://waze.com/ul?q={$encodedDest}&navigate=yes";
                return "<a href=\"{$wazeLink}\" target=\"_blank\" rel=\"noopener noreferrer\">{$destination}</a>";
            } else {
                // Google Maps route from current location to destination
                $googleLink = "https://www.google.com/maps/dir/?api=1&destination={$encodedDest}&travelmode=driving";
                return "<a href=\"{$googleLink}\" target=\"_blank\" rel=\"noopener noreferrer\">{$destination}</a>";
            }
        }
    }
    