<?php declare(strict_types = 1);

namespace UptimeProject\Slack\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use UptimeProject\Slack\Exceptions\SlackMessageException;
use UptimeProject\Slack\MessageDraft;
use UptimeProject\Slack\Workspace;

class SendMessageTest extends TestCase
{
    const EXPECTED_HTTP_METHOD = 'POST';
    const EXPECTED_URL = 'https://webhook.test';

    /** @return array<string, array<int, mixed>> */
    public function message_data(): array
    {
        return [
            'test_send_message' => [
                'John',         // username
                'Hello world!', // message
                null,           // icon
                null,           // channel
                200,            // http_response
                null,           // exception
                null,           // icon_is_url
            ],
            'test_send_message_icon_emoji' => [
                'John',         // username
                'Hello world!', // message
                ':tada:',       // icon
                null,           // channel
                200,            // http_response
                null,           // exception
                false,          // icon_is_url
            ],
            'test_send_message_icon_url' => [
                'John',         // username
                'Hello world!', // message
                'https://example.com/test.png', // icon
                null,           // channel
                200,            // http_response
                null,           // exception
                true,           // icon_is_url
            ],
            'test_send_message_to_channel' => [
                'John',         // username
                'Hello world!', // message
                null,           // icon
                '#general',     // channel
                200,            // http_response
                null,           // exception
                null,           // icon_is_url
            ],
            'test_send_message_error' => [
                'John',         // username
                'Hello world!', // message
                null,           // icon
                null,           // channel
                500,            // http_response
                SlackMessageException::class, // exception
                null,           // icon_is_url
            ],
        ];
    }

    /** @dataProvider message_data */
    public function test_send_message(
        string $username,
        string $message,
        ?string $icon,
        ?string $channel,
        int $httpResponse,
        ?string $expectedException,
        ?bool $iconIsUrl
    ): void {
        $mock = new MockHandler([
            new Response($httpResponse),
        ]);
        $handlerStack = HandlerStack::create($mock);

        $handlerStack->push(function (callable $handler) use ($username, $message, $icon, $channel, $iconIsUrl) {
            return function (RequestInterface $request, array $options) use ($handler, $username, $message, $icon, $channel, $iconIsUrl) {
                $assertMethod = SendMessageTest::EXPECTED_HTTP_METHOD;
                $assertUrl = SendMessageTest::EXPECTED_URL;
                // Make some assertions
                Assert::assertSame($assertUrl, (string) $request->getUri());
                Assert::assertSame(strtolower($assertMethod), strtolower($request->getMethod()));
                $body = json_decode((string) $request->getBody(), true);
                Assert::assertSame($username, $body['username'] ?? null);
                Assert::assertSame($message, $body['text'] ?? null);
                if (is_bool($iconIsUrl)) {
                    $key = $iconIsUrl ? 'icon_url' : 'icon_emoji';
                    Assert::assertSame($icon, $body[$key] ?? null);
                }
                Assert::assertSame($channel, $body['channel'] ?? null);

                // Go on with business.
                return $handler($request, $options);
            };
        });

        $client = new Client(['handler' => $handlerStack]);
        $workspace = new Workspace(self::EXPECTED_URL);
        $workspace->setClient($client);

        $draft = $workspace->from($username, $icon);
        Assert::assertInstanceOf(MessageDraft::class, $draft);

        if (is_string($expectedException)) {
            $this->expectException($expectedException);
        }
        $draft->send($message, $channel);
    }
}
