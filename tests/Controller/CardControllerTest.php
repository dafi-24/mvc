<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CardControllerTest extends WebTestCase
{
    public function testCardHome()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/card');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('body');
    }

    public function testDeckRouteCreatesDeck()
    {
        $client = static::createClient();
        $client->request('GET', '/card/deck');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', '♥');
    }

    public function testDeckShuffleRoute()
    {
        $client = static::createClient();
        $client->request('GET', '/card/deck/shuffle');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.card');
    }

    public function testDrawOneCard()
    {
        $client = static::createClient();
        $client->request('GET', '/card/deck/shuffle');
        $client->request('GET', '/card/deck/draw');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.card');
    }

    public function testDrawMultipleCards()
    {
        $client = static::createClient();
        $client->request('GET', '/card/deck/shuffle');
        $client->request('GET', '/card/deck/draw/3');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.card');
    }

    public function testDrawTooManyCards()
    {
        $client = static::createClient();

        $client->request('GET', '/card/deck/draw/53');
        $this->assertResponseStatusCodeSame(500);
        $this->assertStringContainsString('Du kan inte dra mer än', $client->getResponse()->getContent());
    }

    public function testSessionDelete()
    {
        $client = static::createClient();
        $client->request('GET', '/session/delete');

        $this->assertResponseRedirects('/card');
    }
}
