<?php
    if ( ! function_exists( 'post_filter' ) ) {
        /**
         * AJAX handler for filtering posts.
         *
         * This function receives POST parameters via AJAX,
         * sanitizes them, checks that the query template exists,
         * and then includes the template file to handle the query output.
         *
         * Expected POST parameters:
         * - post_type     (string) The WordPress post type to filter.
         * - event_type    (string) Optional event type filter.
         * - filter_object (string) Optional additional filter parameter.
         *
         * @since 1.0.0
         *
         * @return void
         */
        function post_filter() {
            // Sanitize and validate incoming POST data
            $post_type     = isset($_POST['post_type']) ? $_POST['post_type'] : '';
            $event_type    = isset($_POST['event_type']) ? $_POST['event_type'] : '';
            $post_ids      = isset($_POST['post_ids']) ? $_POST['post_ids'] : '';
            $filter_object = isset($_POST['filter_object']) ? $_POST['filter_object'] : '';

            // Handle bookmarks ids
            if (!empty($post_ids)) {
                $post_ids = array_map('intval', $post_ids);
            }

            // Locate query template
            $query_template = get_template_directory() . '/template-parts/queries/query-post-type.php';
            if (!file_exists($query_template)) {
                wp_send_json_error(['message' => __('Query template not found.', TEXT_DOMAIN)], 500);
            }

            // Make variables available to the included template
            $args = [
                'post_type'     => $post_type,
                'event_type'    => $event_type,
                'post_ids'      => $post_ids,
                'filter_object' => $filter_object,
            ];

            include $query_template;

            wp_die(); // Always die at the end of an AJAX handler
        }
        add_action( 'wp_ajax_post_filter', 'post_filter' );
        add_action( 'wp_ajax_nopriv_post_filter', 'post_filter' );
    }

    if ( ! function_exists( 'filter_post_title_by_alphabet' ) ) {
        /**
         * Modify the WHERE clause to filter posts by the first letter(s) of the post title.
         *
         * This filter checks the 'alphabet' query variable in WP_Query and modifies
         * the SQL WHERE clause to include posts whose titles start with one or more
         * specified letters.
         *
         * Example usage:
         *     new WP_Query([
         *         'post_type' => 'post',
         *         'alphabet'  => ['A', 'B']
         *     ]);
         *
         * @since 1.0.0
         *
         * @global wpdb $wpdb WordPress database abstraction object.
         *
         * @param string   $where Existing WHERE clause for the query.
         * @param WP_Query $query The WP_Query instance (passed by reference).
         * @return string Modified WHERE clause.
         */
        function filter_post_title_by_alphabet($where, $query) {
            global $wpdb;

            if (!empty($query->query_vars['alphabet'])) {
                $alphabet_values = (array) $query->query_vars['alphabet'];
                $like_conditions = [];

                foreach ($alphabet_values as $letter) {
                    // Ensure safe query building
                    $letter = sanitize_text_field($letter);
                    if (preg_match('/^[a-zA-Z]$/', $letter)) {
                        $like_conditions[] = $wpdb->prepare(
                            "{$wpdb->posts}.post_title LIKE %s",
                            $letter . '%'
                        );
                    }
                }

                if (!empty($like_conditions)) {
                    $where .= " AND (" . implode(" OR ", $like_conditions) . ")";
                }
            }

            return $where;
        }
        add_filter('posts_where', 'filter_post_title_by_alphabet', 10, 2);
    }