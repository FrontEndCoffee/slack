<?php declare(strict_types = 1);

namespace UptimeProject\Slack\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use UptimeProject\Slack\Exceptions\SlackMessageException;
use UptimeProject\Slack\MessageDraft;
use UptimeProject\Slack\Workspace;

class WebhookTest extends TestCase
{
    public function test_send_message()
    {
        $mock = new MockHandler([
            new Response(200),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $workspace = new Workspace('https://webhook.test');
        $workspace->setClient($client);

        $draft = $workspace->from('John');
        $this->assertInstanceOf(MessageDraft::class, $draft);

        $draft->send('Hello world!');
    }

    public function test_send_message_icon_emoji()
    {
        $mock = new MockHandler([
            new Response(200),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $workspace = new Workspace('https://webhook.test');
        $workspace->setClient($client);

        $draft = $workspace->from('John', ':tada:');
        $this->assertInstanceOf(MessageDraft::class, $draft);

        $draft->send('Hello world!');
    }

    public function test_send_message_icon_url()
    {
        $mock = new MockHandler([
            new Response(200),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $workspace = new Workspace('https://webhook.test');
        $workspace->setClient($client);

        $draft = $workspace->from('John', 'https://example.com/test.png');
        $this->assertInstanceOf(MessageDraft::class, $draft);

        $draft->send('Hello world!');
    }

    public function test_send_message_to_channel()
    {
        $mock = new MockHandler([
            new Response(200),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $workspace = new Workspace('https://webhook.test');
        $workspace->setClient($client);

        $draft = $workspace->from('John');
        $this->assertInstanceOf(MessageDraft::class, $draft);

        $draft->send('Hello world!', '#general');
    }

    public function test_send_message_error()
    {
        $mock = new MockHandler([
            new Response(500),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $workspace = new Workspace('https://webhook.test');
        $workspace->setClient($client);

        $draft = $workspace->from('John');
        $this->assertInstanceOf(MessageDraft::class, $draft);

        $this->expectException(SlackMessageException::class);
        $draft->send('Hello world!');
    }
}
