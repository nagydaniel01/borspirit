<?php
    declare(strict_types=1);

    if (!function_exists('dd')) {
        /**
         * Dump variables and terminate execution.
         * Provides call location and stack trace (if WP_DEBUG is enabled).
         *
         * @param mixed ...$vars Variables to dump.
         * @return never
         */
        function dd(...$vars): never
        {
            dump_internal(true, ...$vars);
        }
    }

    if (!function_exists('dump')) {
        /**
         * Dump variables without terminating execution.
         * Provides call location and stack trace (if WP_DEBUG is enabled).
         *
         * @param mixed ...$vars Variables to dump.
         * @return void
         */
        function dump(...$vars): void
        {
            dump_internal(false, ...$vars);
        }
    }

    /**
     * Core dump logic used by dd() and dump().
     *
     * @param bool  $terminate Whether to terminate execution (dd) or not (dump).
     * @param mixed ...$vars    Variables to dump.
     * @return void
     */
    function dump_internal(bool $terminate, ...$vars): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $caller = $trace[1] ?? ['file' => 'unknown', 'line' => '?'];

        $is_debug = defined('WP_DEBUG') && WP_DEBUG;
        $is_cli   = PHP_SAPI === 'cli';

        if ($is_cli) {
            // CLI output
            fwrite(STDOUT, sprintf("Dumped at %s (line %s)\n\n", $caller['file'], $caller['line']));

            foreach ($vars as $var) {
                print_r($var);
                echo "\n";
            }

            if ($is_debug) {
                fwrite(STDOUT, "Call Stack:\n");
                foreach ($trace as $i => $frame) {
                    $file = $frame['file'] ?? '[internal]';
                    $line = $frame['line'] ?? '?';
                    $func = ($frame['class'] ?? '') . ($frame['type'] ?? '') . ($frame['function'] ?? 'unknown');
                    fwrite(STDOUT, sprintf("#%d %s(%s): %s\n", $i, $file, $line, $func));
                }
            }
        } else {
            // Browser (HTML) output
            echo '<div style="
                background:#1e1e1e;
                color:#fff;
                padding:20px;
                font-family:monospace;
                border-radius:8px;
                margin:20px;
                overflow:auto;
                max-height:80vh;
                box-shadow:0 2px 10px rgba(0,0,0,0.5);
            ">';

            // Call location
            echo sprintf(
                '<div style="color:#0ff;margin-bottom:10px;">Dumped at %s (line %s)</div>',
                htmlspecialchars($caller['file']),
                htmlspecialchars((string)$caller['line'])
            );

            // Dump variables
            foreach ($vars as $var) {
                echo '<pre style="
                    background:#2d2d2d;
                    padding:10px;
                    border-radius:8px;
                    margin-bottom:0;
                    white-space:pre-wrap;
                    word-wrap:break-word;
                ">';
                echo htmlspecialchars(print_r($var, true));
                echo '</pre>';
            }

            // Stack trace (only if WP_DEBUG is enabled)
            if ($is_debug) {
                echo '<h4 style="color:#ff0;margin-top:20px;margin-bottom:5px;">Call Stack:</h4>';
                echo '<pre style="
                    background:#2d2d2d;
                    padding:10px;
                    border-radius:8px;
                    white-space:pre-wrap;
                    word-wrap:break-word;
                ">';
                foreach ($trace as $i => $frame) {
                    $file = $frame['file'] ?? '[internal function]';
                    $line = $frame['line'] ?? '?';
                    $func = ($frame['class'] ?? '') . ($frame['type'] ?? '') . ($frame['function'] ?? 'unknown');
                    echo sprintf("#%d %s(%s): %s\n", $i, $file, $line, $func);
                }
                echo '</pre>';
            }

            echo '</div>';
        }

        if ($terminate) {
            exit;
        }
    }

    /**
     * Custom Error Handler - Runtime (non-fatal) errors
     */
    set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        // Map non-fatal error types
        $errorTypes = [
            E_WARNING           => 'Warning',
            E_NOTICE            => 'Notice',
            E_USER_WARNING      => 'User Warning',
            //E_USER_NOTICE       => 'User Notice',
            //E_DEPRECATED        => 'Deprecated',
            //E_USER_DEPRECATED   => 'User Deprecated',
            E_STRICT            => 'Strict Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
        ];

        // Skip if this error is not in our map (fatal errors go to shutdown handler)
        if (!isset($errorTypes[$errno])) {
            return false;
        }

        // Skip if error_reporting() does not include this error
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $errorTypeDescription = $errorTypes[$errno];
        $formattedMessage = "PHP {$errorTypeDescription} [{$errno}]: {$errstr} in {$errfile} on line {$errline}";

        if (defined('WP_DEBUG') && WP_DEBUG) {
            echo '<div style="
                background:#1e3a8a;
                color:#fff;
                padding:20px;
                font-family: monospace;
                border-radius:8px;
                margin:20px;
                overflow:auto;
                max-height:80vh;
                box-shadow:0 2px 10px rgba(0,0,0,0.5);
            ">' . $formattedMessage . '</div>';
        } else {
            error_log($formattedMessage);
        }

        return true; // prevent default handler
    });

    /**
     * Shutdown handler for fatal errors
     */
    register_shutdown_function(function () {
        $lastError = error_get_last();

        if (!$lastError) {
            return;
        }

        // Fatal error types
        $fatalErrors = [
            E_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
        ];

        if (!in_array($lastError['type'], $fatalErrors, true)) {
            return; // Ignore non-fatal errors
        }

        $errorDescriptions = [
            E_ERROR         => 'Fatal Error',
            E_PARSE         => 'Parse Error',
            E_CORE_ERROR    => 'Core Fatal Error',
            E_COMPILE_ERROR => 'Compile Fatal Error',
        ];

        $errorType            = $lastError['type'];
        $errorTypeDescription = $errorDescriptions[$errorType] ?? 'Unknown Fatal Error';
        $formattedMessage     = "PHP {$errorTypeDescription} [{$errorType}]: {$lastError['message']} in {$lastError['file']} on line {$lastError['line']}";

        if (defined('WP_DEBUG') && WP_DEBUG) {
            echo '<div style="
                background:#8b0000;
                color:#fff;
                padding:20px;
                font-family: monospace;
                border-radius:8px;
                margin:20px;
                overflow:auto;
                max-height:80vh;
                box-shadow:0 2px 10px rgba(0,0,0,0.5);
            ">' . $formattedMessage . '</div>';
        } else {
            error_log($formattedMessage);
        }
    });

    /**
     * Custom Exception Handler
     */
    set_exception_handler(function ($exception) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            echo '<div style="
                background:#8b0000;
                color:#fff;
                padding:20px;
                font-family: monospace;
                border-radius:8px;
                margin:20px;
                overflow:auto;
                max-height:80vh;
                box-shadow:0 2px 10px rgba(0,0,0,0.5);
            ">';
            echo "<strong>Uncaught Exception:</strong> " . $exception->getMessage() . "<br>";
            echo "<strong>File:</strong> " . $exception->getFile() . "<br>";
            echo "<strong>Line:</strong> " . $exception->getLine() . "<br>";
            echo "<pre style='margin-top:10px;background:#2d2d2d;padding:10px;border-radius:8px;'>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
            echo '</div>';
        } else {
            error_log("Exception: " . $exception->getMessage());
        }
    });
