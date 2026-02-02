<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    
    if ( ! function_exists( 'create_search_log_table' ) ) {
        /**
         * Create database table to store search queries.
         */
        function create_search_log_table() {
            global $wpdb;
            $table_name      = $wpdb->prefix . 'search_queries';
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                query VARCHAR(255) NOT NULL,
                searched_at DATETIME NOT NULL,
                ip VARCHAR(100) NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }
        add_action( 'after_switch_theme', 'create_search_log_table' );
    }

    if ( ! function_exists( 'log_search_query' ) ) {
        /**
         * Hook into template_redirect to log searches on the front-end.
         */
        function log_search_query() {
            if ( is_search() ) {
                $search_query = get_search_query();

                if ( ! empty( $search_query ) ) {
                    insert_search_log( $search_query );
                }
            }
        }
        add_action( 'template_redirect', 'log_search_query' );
    }

    if ( ! function_exists( 'insert_search_log' ) ) {
        /**
         * Insert a search query into the database.
         *
         * GDPR: IP addresses are anonymized before storing.
         *
         * @param string $term The search query term.
         */
        function insert_search_log( $term ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'search_queries';

            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
                $ip = preg_replace( '/\.\d+$/', '.xxx', $ip ); // anonymize IPv4
            } elseif ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
                $ip = preg_replace( '/:[0-9a-f]+$/i', ':xxxx', $ip ); // anonymize IPv6
            }

            // Avoid duplicate logging from same IP within 1 hour
            $exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name
                    WHERE query = %s
                    AND ip = %s
                    AND searched_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)",
                    $term,
                    $ip
                )
            );

            if ( ! $exists ) {
                $wpdb->insert(
                    $table_name,
                    [
                        'query'       => $term,
                        'searched_at' => current_time( 'mysql' ),
                        'ip'          => $ip,
                    ],
                    [ '%s', '%s', '%s' ]
                );
            }
        }
    }

    if ( ! function_exists( 'delete_old_search_logs' ) ) {
        /**
         * Delete search logs older than 90 days (GDPR compliance).
         */
        function delete_old_search_logs() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'search_queries';

            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM $table_name WHERE searched_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
                    90
                )
            );
        }

        // Schedule daily deletion
        if ( ! wp_next_scheduled( 'delete_old_search_logs_daily' ) ) {
            wp_schedule_event( time(), 'daily', 'delete_old_search_logs_daily' );
        }
        add_action( 'delete_old_search_logs_daily', 'delete_old_search_logs' );
    }

    if ( ! function_exists( 'search_logs_admin_notices' ) ) {
        /**
         * Display admin notices: GDPR info, deletion success, and export warnings.
         */
        function search_logs_admin_notices() {
            if ( current_user_can( 'manage_options' ) && isset($_GET['page']) && $_GET['page'] === 'borspirit' ) {

                // GDPR info notice
                echo '<div class="notice notice-info is-dismissible"><p><strong>Search Logs:</strong> For GDPR compliance, IPs are anonymized and logs are automatically deleted after 90 days.</p></div>';

                // Single deletion success notice
                if ( isset($_GET['search_log_deleted']) && $_GET['search_log_deleted'] == 1 ) {
                    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Selected search log were successfully deleted.', 'borspirit' ) . '</p></div>';
                }

                // Bulk deletion success notice
                if ( isset($_GET['search_logs_deleted']) && $_GET['search_logs_deleted'] == 1 ) {
                    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Selected search logs were successfully deleted.', 'borspirit' ) . '</p></div>';
                }

                // Export empty data warning
                if ( isset($_GET['search_logs_export']) && $_GET['search_logs_export'] === 'empty' ) {
                    echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'No data found to export.', 'borspirit' ) . '</p></div>';
                }
            }
        }
        add_action( 'admin_notices', 'search_logs_admin_notices' );
    }

    if ( ! function_exists( 'register_search_logs_admin_page' ) ) {
        /**
         * Register admin page for search logs.
         */
        function register_search_logs_admin_page() {
            add_management_page(
                __( 'Search Logs', 'borspirit' ),
                __( 'Search Logs', 'borspirit' ),
                'manage_options',
                'search-logs',
                'display_search_logs'
            );
        }
        add_action( 'admin_menu', 'register_search_logs_admin_page' );
    }

    if ( ! function_exists( 'display_search_logs' ) ) {
        /**
         * Display the search logs table in admin.
         */
        function display_search_logs() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'search_queries';

            $per_page = 20;
            $paged    = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
            $offset   = ( $paged - 1 ) * $per_page;

            // Build filters
            $where  = [];
            $params = [];
            if ( ! empty( $_GET['s'] ) ) {
                $where[]  = "query LIKE %s";
                $params[] = '%' . $_GET['s'] . '%';
            }
            if ( ! empty( $_GET['date_from'] ) ) {
                $where[]  = "searched_at >= %s";
                $params[] = $_GET['date_from'] . ' 00:00:00';
            }
            if ( ! empty( $_GET['date_to'] ) ) {
                $where[]  = "searched_at <= %s";
                $params[] = $_GET['date_to'] . ' 23:59:59';
            }
            $where_sql = $where ? 'WHERE ' . implode( ' AND ', $where ) : '';

            // Total rows
            $total = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name $where_sql",
                    ...$params
                )
            );

            // Fetch logs
            $args = array_merge( $params, [ $per_page, $offset ] );
            $logs = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $table_name $where_sql
                    ORDER BY searched_at DESC
                    LIMIT %d OFFSET %d",
                    ...$args
                )
            );

            echo '<div class="wrap">';
            echo '<h1>' . esc_html__( 'Search Logs', 'borspirit' ) . '</h1>';

            // Filters & buttons
            echo '<form id="posts-filter" method="GET">';
            echo '<div class="tablenav top">';
            echo '<input type="hidden" name="page" value="borspirit">';
            echo '<div class="alignleft actions">';
            echo '<input type="text" name="s" placeholder="' . esc_attr__( 'Keyword', 'borspirit' ) . '" value="' . esc_attr( $_GET['s'] ?? '' ) . '"> ';
            echo esc_html__( 'From:', 'borspirit' ) . ' <input type="date" name="date_from" value="' . esc_attr( $_GET['date_from'] ?? '' ) . '"> ';
            echo esc_html__( 'To:', 'borspirit' ) . ' <input type="date" name="date_to" value="' . esc_attr( $_GET['date_to'] ?? '' ) . '"> ';
            echo '<input type="submit" class="button" value="' . esc_attr__( 'Filter', 'borspirit' ) . '"> ';

            // Export CSV button
            $export_url = add_query_arg(
                [
                    'page'               => 'borspirit',
                    'export_search_logs' => 1,
                    's'                  => $_GET['s'] ?? '',
                    'date_from'          => $_GET['date_from'] ?? '',
                    'date_to'            => $_GET['date_to'] ?? '',
                ],
                admin_url( 'tools.php' )
            );
            echo '<a href="' . esc_url( $export_url ) . '" class="button">' . esc_html__( 'Export to CSV file', 'borspirit' ) . '</a>';

            // Delete Logs button
            $delete_url = add_query_arg(
                [
                    'page'                  => 'borspirit',
                    'delete_search_logs_bulk' => 1,
                    's'                     => $_GET['s'] ?? '',
                    'date_from'             => $_GET['date_from'] ?? '',
                    'date_to'               => $_GET['date_to'] ?? '',
                ],
                admin_url( 'tools.php' )
            );
            echo '<a href="' . esc_url( $delete_url ) . '" class="button button-danger" onclick="return confirm(\'Are you sure you want to delete these logs?\');">'. esc_html__( 'Delete Logs', 'borspirit' ) . '</a>';

            echo '</div>';
            echo '</div>';
            echo '</form>';

            if ( $logs ) {
                echo '<table class="widefat fixed striped">';
                echo '<thead><tr>';
                echo '<th>' . esc_html__( 'ID', 'borspirit' ) . '</th>';
                echo '<th>' . esc_html__( 'Query', 'borspirit' ) . '</th>';
                echo '<th>' . esc_html__( 'IP', 'borspirit' ) . '</th>';
                echo '<th>' . esc_html__( 'Timestamp', 'borspirit' ) . '</th>';
                echo '<th>' . esc_html__( 'Actions', 'borspirit' ) . '</th>';
                echo '</tr></thead><tbody>';

                foreach ( $logs as $log ) {
                    echo '<tr>';
                    echo '<td>' . esc_html( $log->id ) . '</td>';
                    echo '<td>' . esc_html( $log->query ) . '</td>';
                    echo '<td>' . esc_html( $log->ip ) . '</td>';
                    echo '<td>' . esc_html( $log->searched_at ) . '</td>';
                    echo '<td><a href="' . esc_url( add_query_arg( [ 'delete_search_log' => $log->id ] ) ) . '" onclick="return confirm(\'Are you sure you want to delete this log?\');">'. esc_html__( 'Delete', 'borspirit' ) . '</a></td>';
                    echo '</tr>';
                }

                echo '</tbody></table>';

                // Pagination
                $pages = ceil( $total / $per_page );
                if ( $pages > 1 ) {
                    echo '<div class="tablenav"><div class="tablenav-pages">';
                    for ( $i = 1; $i <= $pages; $i++ ) {
                        $class = ( $i === $paged ) ? 'current' : '';
                        $url   = add_query_arg( [ 'paged' => $i, 'page' => 'borspirit' ] );
                        echo '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '">' . esc_html( $i ) . '</a> ';
                    }
                    echo '</div></div>';
                }
            } else {
                echo '<p>' . esc_html__( 'No search logs found.', 'borspirit' ) . '</p>';
            }

            echo '</div>';
        }
    }

    if ( ! function_exists( 'export_search_logs_to_csv' ) ) {
        /**
         * Export search logs to CSV.
         */
        function export_search_logs_to_csv() {
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( __( 'Unauthorized user', 'borspirit' ) );
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'search_queries';

            $where  = [];
            $params = [];

            if ( ! empty( $_GET['s'] ) ) {
                $where[]  = "query LIKE %s";
                $params[] = '%' . $_GET['s'] . '%';
            }
            if ( ! empty( $_GET['date_from'] ) ) {
                $where[]  = "searched_at >= %s";
                $params[] = $_GET['date_from'] . ' 00:00:00';
            }
            if ( ! empty( $_GET['date_to'] ) ) {
                $where[]  = "searched_at <= %s";
                $params[] = $_GET['date_to'] . ' 23:59:59';
            }

            $where_sql = $where ? 'WHERE ' . implode( ' AND ', $where ) : '';

            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $table_name $where_sql ORDER BY searched_at DESC",
                    ...$params
                ),
                ARRAY_A
            );

            if ( empty( $results ) ) {
                wp_redirect( add_query_arg( 'search_logs_export', 'empty', admin_url( 'tools.php?page=borspirit' ) ) );
                exit;
            }

            header( 'Content-Type: text/csv; charset=utf-8' );
            header( 'Content-Disposition: attachment; filename=borspirit-' . date( 'Y-m-d-H-i-s' ) . '.csv' );
            header( 'Pragma: no-cache' );
            header( 'Expires: 0' );

            $output = fopen( 'php://output', 'w' );
            fputcsv( $output, array_keys( $results[0] ) );
            foreach ( $results as $row ) {
                fputcsv( $output, $row );
            }
            fclose( $output );
            exit;
        }
    }

    if ( ! function_exists( 'handle_search_logs_csv_export' ) ) {
        /**
         * Handle CSV export request.
         */
        function handle_search_logs_csv_export() {
            if ( isset( $_GET['export_search_logs'] ) && $_GET['export_search_logs'] == '1' ) {
                export_search_logs_to_csv();
            }
        }
        add_action( 'admin_init', 'handle_search_logs_csv_export' );
    }

    if ( ! function_exists( 'handle_search_logs_delete' ) ) {
        /**
         * Handle deletion of search logs (single or bulk).
         */
        function handle_search_logs_delete() {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'search_queries';

            // Individual deletion
            if ( isset( $_GET['delete_search_log'] ) ) {
                $id = intval( $_GET['delete_search_log'] );
                if ( $id > 0 ) {
                    $wpdb->delete( $table_name, [ 'id' => $id ], [ '%d' ] );
                    wp_redirect( add_query_arg( 'search_log_deleted', 1, remove_query_arg( 'delete_search_log' ) ) );
                    exit;
                }
            }

            // Bulk deletion
            if ( isset( $_GET['delete_search_logs_bulk'] ) ) {
                $date_from = sanitize_text_field( $_GET['date_from'] ?? '' );
                $date_to   = sanitize_text_field( $_GET['date_to'] ?? '' );

                $where  = [];
                $params = [];

                if ( $date_from ) {
                    $where[]  = "searched_at >= %s";
                    $params[] = $date_from . ' 00:00:00';
                }
                if ( $date_to ) {
                    $where[]  = "searched_at <= %s";
                    $params[] = $date_to . ' 23:59:59';
                }

                if ( $where ) {
                    $where_sql = implode( ' AND ', $where );
                    $query     = "DELETE FROM $table_name WHERE $where_sql";
                    $wpdb->query( $wpdb->prepare( $query, ...$params ) );
                } else {
                    $wpdb->query( "TRUNCATE TABLE $table_name" );
                }

                wp_redirect( add_query_arg( 'search_logs_deleted', 1, remove_query_arg( [ 'delete_search_logs_bulk', 'date_from', 'date_to' ] ) ) );
                exit;
            }
        }
        add_action( 'admin_init', 'handle_search_logs_delete' );
    }
