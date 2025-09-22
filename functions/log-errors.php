<?php
    register_shutdown_function(function () {
        // Get the last error that occurred
        $lastError = error_get_last();
    
        // Check if there is an error
        if ($lastError) {
            // Retrieve the error details
            $errorType      = $lastError['type'];
            $errorMessage   = $lastError['message'];
            $errorFile      = $lastError['file'];
            $errorLine      = $lastError['line'];
    
            // Map error types to human-readable descriptions
            $errorDescriptions = [
                E_ERROR => 'Fatal Error',
                E_WARNING => 'Warning',
                E_PARSE => 'Parse Error',
                E_NOTICE => 'Notice',
                E_CORE_ERROR => 'Core Fatal Error',
                E_CORE_WARNING => 'Core Warning',
                E_COMPILE_ERROR => 'Compile Fatal Error',
                E_USER_ERROR => 'User Fatal Error',
                E_USER_WARNING => 'User Warning',
                E_USER_NOTICE => 'User Notice',
                //E_STRICT => 'Strict Notice',
                E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
                E_DEPRECATED => 'Deprecated Warning',
            ];
    
            // Get the error description based on the error type
            $errorTypeDescription = $errorDescriptions[$errorType] ?? 'Unknown Error';
    
            // Only send the error to Discord if it's one of the specified types
            if (!in_array($errorType, [E_ERROR, E_USER_ERROR, E_WARNING, E_PARSE, E_NOTICE])) {
                return; // Exit early if not in array
            }

            // Format the error message
            $formattedMessage = "PHP {$errorTypeDescription} [{$errorType}]: {$errorMessage} in {$errorFile} on line {$errorLine}";

            // Send the formatted message to Discord
            send_discord_channel($formattedMessage, 'alerts', 'PHP Bot');
        }
    });