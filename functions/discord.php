<?php
    if ( ! function_exists( 'send_discord_channel' ) ) {
        /**
         * Sends a message to a specified Discord channel via webhook.
         *
         * @param string      $content   The content of the message.
         * @param string      $channel   The channel name (default: 'general').
         * @param string      $username  The username to display (default: 'Bot').
         * @param string|null $avatarURL Optional avatar URL.
         *
         * @return string Success or error message.
         */
        function send_discord_channel( $content, $channel = "general", $username = "Bot", $avatarURL = null ) {
            // Define a map of channels to their webhook URLs
            $channelWebhooks = [
                "general"   => 'https://discord.com/api/webhooks/1309183308042862654/PuUe0LHLcQ0SJFS8jN4XLbn46dLyEQ-PR6OQloYPJht9AAUqn6IOZivOiTLQoctTqPp2',
                "test"      => 'https://discord.com/api/webhooks/1309218777296011305/u5CTbAMBjpV63ZRnP1PYoumgAYbNs7jP4MqmsiZ-tgPBrXNQte3Q3JZTgwRVA2UhlCqO',
                "alerts"    => 'https://discord.com/api/webhooks/1317179758882197676/V7gbH3KJ10-Uuxne4X0vnt1YkU-J-Le5xVPjX9wby-tE119V8v1DCNvtfQv0xMqcwBJC',
                //"support" => 'https://discord.com/api/webhooks/192837465019283746/ExampleWebhookKeySupport',
            ];
        
            // Check if the channel exists in the map
            if ( ! isset($channelWebhooks[$channel]) ) {
                return "Error: Channel '$channel' not found or webhook not configured.";
            }

            // Get the webhook URL for the specified or default channel
            $webhookURL = $channelWebhooks[$channel];

            // Validate webhook URL format
            if ( ! filter_var($webhookURL, FILTER_VALIDATE_URL) ) {
                return "Error: Invalid webhook URL for channel '$channel'.";
            }

            // Server information from $_SERVER variables
            $serverName     = $_SERVER['SERVER_NAME']; // The name of the host server (e.g., example.com)
            $serverIP       = $_SERVER['SERVER_ADDR']; // The IP address of the host server
            //$serverSoftware = $_SERVER['SERVER_SOFTWARE']; // Server software version (e.g., Apache/2.4.41)
            //$serverPort     = $_SERVER['SERVER_PORT']; // Server port (e.g., 80 or 443)
            //$protocol       = $_SERVER['SERVER_PROTOCOL']; // The protocol (e.g., HTTP/1.1)

            // Add a timestamp to the message content
            $timestamp = date('d-M-Y H:i:s T');

            // Build the message payload
            $message = [
                "content" => "[".$timestamp."] Server: $serverName (IP: $serverIP) - " . $content,
                "username" => $username,
            ];

            // Add avatar URL if provided
            if ($avatarURL) {
                $message["avatar_url"] = $avatarURL;
            }

            // Encode message into JSON format
            $jsonData = json_encode($message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            // Initialize cURL
            $ch = curl_init($webhookURL);

            // Set cURL options
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            // Execute the request
            $response = curl_exec($ch);

            // Check for cURL errors
            if ( curl_errno($ch) ) {
                $errorMessage = curl_error($ch);
                curl_close($ch);
                return "Error: Failed to send message. cURL error: $errorMessage";
            }

            // Get HTTP status code
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Check for Discord API response errors
            if ($httpStatusCode < 200 || $httpStatusCode >= 300) {
                return "Error: Discord API returned HTTP status code $httpStatusCode. Response: $response";
            }

            // Return success message
            return "Message sent successfully to channel: $channel!";
        }
    }