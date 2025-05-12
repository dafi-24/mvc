<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Card\DeckOfCards;

class CardControllerTest extends WebTestCase
{
    /**
     * Test för startsidan (home).
     */
    public function testHome(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->method('all')->willReturn(['some_key' => 'some_value']);  // mockar sessiondata

        $client = static::createClient();
        $client->request('GET', '/card');
        
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Utforska funktionerna');
    }

    /**
     * Test för visning av sessionens data (index).
     */
    public function testIndex(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->method('all')->willReturn(['some_key' => 'some_value']);  // mockar sessiondata

        $client = static::createClient();
        $client->request('GET', '/session');
        
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Session Debug');
    }

    /**
     * Test för visning av kortleken (deck).
     */
    public function testDeck(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $deck = new DeckOfCards();  // skapa en deck för testet
        $session->method('get')->willReturn($deck);  // mocka sessionens kortlek

        $client = static::createClient();
        $client->request('GET', '/card/deck');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Visa Kortleken');  // Kontrollera att Deck visas i body
    }

    /**
     * Test för blandning av kortleken (deckShuffle).
     */
    public function testDeckShuffle(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $deck = new DeckOfCards();  // skapa en deck
        $deck->shuffle();  // blanda kortleken
        $session->method('get')->willReturn($deck);  // mocka sessionens kortlek

        $client = static::createClient();
        $client->request('GET', '/card/deck/shuffle');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Blandad Kortlek');  // Kontrollera att Shuffle visas i body
    }

    /**
     * Test för att dra ett kort från kortleken (deckDraw).
     */
    public function testDeckDraw(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $deck = new DeckOfCards();  // skapa en deck
        $draw = $deck->draw(1);  // dra ett kort
        $session->method('get')->willReturn($deck);  // mocka sessionens kortlek

        $client = static::createClient();
        $client->request('GET', '/card/deck/draw');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Dra kort');  // Kontrollera att drawnCards visas i body
    }

    /**
     * Test för att dra ett angivet antal kort från kortleken (deckDrawNumber).
     */
    public function testDeckDrawNumber(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $deck = new DeckOfCards();  // skapa en deck
        $draw = $deck->draw(3);  // dra 3 kort
        $session->method('get')->willReturn($deck);  // mocka sessionens kortlek

        $client = static::createClient();
        $client->request('GET', '/card/deck/draw/3');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Dra kort');  // Kontrollera att drawnCards visas i body
    }
}
