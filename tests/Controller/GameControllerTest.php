<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testHomePageLoads(): void
    {
        $this->client->request('GET', '/game');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
    }

    public function testStartRedirectsToPlayAndSessionIsSet(): void
    {
        $this->client->request('GET', '/game/start');
        $this->assertResponseRedirects('/game/play');

        $this->client->followRedirect();
        $session = $this->client->getRequest()->getSession();
        $this->assertTrue($session->has('game'));
    }

    public function testPlayPageLoads(): void
    {
        $this->client->request('GET', '/game/start');
        $this->client->followRedirect();

        $this->client->request('GET', '/game/play');

        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();

        $this->assertStringContainsString('<h2>Dealer</h2>', $content);
        $this->assertStringContainsString('<h2>Player</h2>', $content);
        $this->assertStringContainsString('Poäng', $content);
    }

    public function testHitRedirectsProperly(): void
    {
        $this->client->request('GET', '/game/start');
        $this->client->followRedirect();

        $this->client->request('GET', '/game/hit');
        $location = $this->client->getResponse()->headers->get('Location');
        $this->assertMatchesRegularExpression('#/game/(play|result)$#', $location);
    }

    public function testStandRedirectsToResult(): void
    {
        $this->client->request('GET', '/game/start');
        $this->client->followRedirect();

        $this->client->request('GET', '/game/stand');
        $this->assertResponseRedirects('/game/result');
    }

    public function testDoubleRedirectsToResult(): void
    {
        $this->client->request('GET', '/game/start');
        $this->client->followRedirect();

        $this->client->request('GET', '/game/double');
        $this->assertResponseRedirects('/game/result');
    }

    public function testResultPageLoads(): void
    {
        $this->client->request('GET', '/game/start');
        $this->client->followRedirect();
        $this->client->request('GET', '/game/stand');
        $this->client->followRedirect();

        $this->client->request('GET', '/game/result');

        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('result', strtolower($content));
    }

    public function testDocPageLoads(): void
    {
        $this->client->request('GET', '/game/doc');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
    }
}