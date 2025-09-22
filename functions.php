<?php
    // PHPDoc-style comments (docblocks)
    if ( ! function_exists( 'include_files_recursively' ) ) {
        /**
         * Recursively includes all PHP files from a given directory and its subdirectories.
         *
         * Useful for auto-loading modular PHP files (e.g., custom functions, classes).
         *
         * @param string $directory The starting absolute directory path.
         * @return void
         */
        function include_files_recursively( $directory ) {
            if ( ! is_dir( $directory ) || ! is_readable( $directory ) ) {
                error_log("Directory not found or not readable: {$directory}");
                return;
            }

            try {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator( $directory, RecursiveDirectoryIterator::SKIP_DOTS ),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ( $iterator as $file ) {
                    // Include only PHP files
                    if ( $file->isFile() && strtolower( $file->getExtension() ) === 'php' ) {
                        require_once $file->getRealPath();
                    }
                }
            } catch ( Exception $e ) {
                // Log any exceptions for debugging purposes
                error_log('Error including files: ' . $e->getMessage());
            }
        }
    }

    // Initialize: Include all PHP files under /functions directory in your theme
    $functions_dir = get_template_directory() . '/functions';
    include_files_recursively($functions_dir);
    