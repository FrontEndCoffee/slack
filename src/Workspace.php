<?php declare(strict_types = 1);

namespace UptimeProject\Slack;

use GuzzleHttp\Client;
use UptimeProject\Slack\Exceptions\SlackMessageException;

class Workspace
{
    /** @var string */
    private $webhook;

    /** @var Client */
    private $client;

    public function __construct(string $webhook)
    {
        $this->webhook = $webhook;
        $this->client  = new Client();
    }

    public function from(string $username, ?string $icon = null): MessageDraft
    {
        if (is_null($icon)) {
            return new MessageDraft($this, $username);
        }

        if (strpos($icon, 'http') === 0) {
            return new MessageDraft($this, $username, null, $icon);
        }

        return new MessageDraft($this, $username, $icon, null);
    }

    /** @param array<string, string> $payload */
    public function sendRaw(array $payload): void
    {
        $response = $this->client->post($this->webhook, [
            'http_errors' => false,
            'json' => $payload,
        ]);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return;
        }

        throw new SlackMessageException("Error: {$response->getBody()}");
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}
