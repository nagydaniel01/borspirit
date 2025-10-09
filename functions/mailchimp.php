<?php
/**
 * MailchimpService
 * A clean OOP wrapper for interacting with the Mailchimp API.
 */
class MailchimpService
{
    private string $apiKey;
    private string $audienceId;
    private string $dataCenter;
    private string $auth;
    private string $baseUrl;

    /**
     * Constructor
     *
     * @param string $apiKey
     * @param string $audienceId
     */
    public function __construct(string $apiKey, string $audienceId)
    {
        $this->apiKey     = $apiKey;
        $this->audienceId = $audienceId;
        $this->dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);
        $this->auth       = base64_encode("user:{$apiKey}");
        $this->baseUrl    = "https://{$this->dataCenter}.api.mailchimp.com/3.0/lists/{$audienceId}/members";
    }

    /**
     * Subscribe or update a Mailchimp member.
     */
    public function subscribe(string $email, ?string $firstName, ?string $lastName, array $tags, string $status): array
    {
        $firstName = $firstName ?? '';
        $lastName  = $lastName ?? '';

        $currentStatus = $this->getStatusByEmail($email);
        $validStatuses = ['subscribed', 'pending', 'unsubscribed'];

        if (in_array($currentStatus, $validStatuses, true)) {
            if (is_user_logged_in()) {
                $response = $this->updateMember($email, $firstName, $lastName, $tags, $status);
                return $this->handleResponse($response, __('Your subscription has been updated successfully.', 'text-domain'));
            }
            return $this->jsonResponse(true, __('Your e-mail address is already in our list.', 'text-domain'));
        }

        $response = $this->createMember($email, $firstName, $lastName, $tags);
        return $this->handleResponse($response, __('You have been subscribed successfully.', 'text-domain'));
    }

    /**
     * Update an existing Mailchimp member.
     */
    public function updateMember(string $email, string $firstName, string $lastName, array $tags, string $status)
    {
        return $this->sendRequest('PATCH', $this->getMemberUrl($email), $this->buildPayload($email, $firstName, $lastName, $tags, $status));
    }

    /**
     * Create a new Mailchimp member.
     */
    public function createMember(string $email, string $firstName, string $lastName, array $tags)
    {
        return $this->sendRequest('POST', $this->baseUrl, $this->buildPayload($email, $firstName, $lastName, $tags, 'subscribed'));
    }

    /**
     * Retrieve full member data by email.
     */
    public function getMemberByEmail(string $email): array
    {
        $response = $this->sendRequest('GET', $this->getMemberUrl($email));

        if (is_wp_error($response)) {
            return ['error' => $response->get_error_message()];
        }

        $data = $this->parseResponse($response);
        return $data['status'] === 404 ? ['error' => 'Subscriber not found'] : $data;
    }

    /**
     * Get subscription status by email.
     */
    public function getStatusByEmail(string $email): string
    {
        $response = $this->sendRequest('GET', $this->getMemberUrl($email));
        if (is_wp_error($response)) {
            return 'error';
        }

        $data = $this->parseResponse($response);
        return $data['status'] ?? 'notexist';
    }

    /**
     * Get subscriber first name.
     */
    public function getFirstName(string $email): string
    {
        $data = $this->getMemberByEmail($email);
        return $data['merge_fields']['FNAME'] ?? '';
    }

    /**
     * Get subscriber last name.
     */
    public function getLastName(string $email): string
    {
        $data = $this->getMemberByEmail($email);
        return $data['merge_fields']['LNAME'] ?? '';
    }

    /**
     * Helper: Build payload for API requests.
     */
    private function buildPayload(string $email, string $firstName, string $lastName, array $tags, string $status): string
    {
        return json_encode([
            'email_address' => $email,
            'status'        => $status,
            'merge_fields'  => [
                'FNAME' => $firstName,
                'LNAME' => $lastName,
            ],
            'tags' => $tags,
        ]);
    }

    /**
     * Helper: Generate request headers.
     */
    private function headers(): array
    {
        return [
            'Content-Type'  => 'application/json',
            'Authorization' => "Basic {$this->auth}",
        ];
    }

    /**
     * Helper: Get a specific member URL.
     */
    private function getMemberUrl(string $email): string
    {
        $encoded = md5(strtolower($email));
        return "{$this->baseUrl}/{$encoded}";
    }

    /**
     * Helper: Send API request.
     */
    private function sendRequest(string $method, string $url, ?string $body = null)
    {
        $args = [
            'method'  => $method,
            'headers' => $this->headers(),
        ];

        if ($body !== null) {
            $args['body'] = $body;
        }

        return wp_remote_request($url, $args);
    }

    /**
     * Helper: Decode API response safely.
     */
    private function parseResponse($response): array
    {
        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true) ?: [];
    }

    /**
     * Helper: Standard JSON response.
     */
    private function jsonResponse(bool $success, string $message): array
    {
        return [
            'success' => $success,
            'message' => $message,
        ];
    }

    /**
     * Helper: Handle API response with success/failure.
     */
    private function handleResponse($response, string $successMessage): array
    {
        if (is_wp_error($response)) {
            return $this->jsonResponse(false, sprintf(__('Error: %s', TEXT_DOMAIN), $response->get_error_message()));
        }

        return $this->jsonResponse(true, $successMessage);
    }
}
