<?php

namespace App\Tests\Controller;

use App\Controller\APIControllerJson;
use App\Entity\Library;
use App\Card\DeckOfCards;
use App\Card\CardGraphic;
use App\Repository\LibraryRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class APIControllerJsonTest extends KernelTestCase
{
    private APIControllerJson $controller;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->controller = new APIControllerJson();
        $this->controller->setContainer($container);
    }

    public function testJsonNumberReturnsValidResponse(): void
    {
        $response = $this->controller->jsonNumber();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = json_decode($response->getContent() ?: '', true);

        $this->assertArrayHasKey('lucky-number', $data);
        $this->assertIsInt($data['lucky-number']);
        $this->assertArrayHasKey('lucky-message', $data);
        $this->assertEquals('Hi there!', $data['lucky-message']);
    }

    public function testJsonQuoteReturnsQuoteAndDate(): void
    {
        $response = $this->controller->jsonQuote();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = json_decode($response->getContent() ?: '', true);

        $this->assertArrayHasKey('quote', $data);
        $this->assertIsString($data['quote']);
        $this->assertArrayHasKey('date', $data);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $data['date']);
        $this->assertArrayHasKey('timestamp', $data);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $data['timestamp']);
    }

    public function testGetDeckInitializesAndReturnsSortedDeck(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())
            ->method('has')
            ->with('card_deck')
            ->willReturn(false);
        $session->expects($this->once())
            ->method('set')
            ->with('card_deck', $this->isInstanceOf(DeckOfCards::class));
        $deck = new DeckOfCards();
        $session->expects($this->once())
            ->method('get')
            ->with('card_deck')
            ->willReturn($deck);

        $response = $this->controller->getDeck($session);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = json_decode($response->getContent() ?: '', true);

        $this->assertCount(4, $data);
        foreach ($data as $suitCards) {
            $this->assertIsArray($suitCards);
            if (count($suitCards) > 0) {
                $this->assertIsString($suitCards[0]);
            }
        }
    }

    public function testShuffleDeckReturnsShuffledDeck(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())
            ->method('set')
            ->with('card_deck', $this->isInstanceOf(DeckOfCards::class));

        $response = $this->controller->shuffleDeck($session);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = json_decode($response->getContent() ?: '', true);

        $this->assertCount(52, $data);
        $this->assertIsString($data[0]);
    }

    public function testDrawCardsSuccess(): void
    {
        $deck = new DeckOfCards();
        $session = $this->createMock(SessionInterface::class);
        $session->method('has')->willReturn(true);
        $session->method('get')->willReturn($deck);
        $session->expects($this->once())
            ->method('set')
            ->with('card_deck', $deck);

        $response = $this->controller->drawCards($session, 5);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = json_decode($response->getContent() ?: '', true);

        $this->assertArrayHasKey('drawn_cards', $data);
        $this->assertCount(5, $data['drawn_cards']);
        $this->assertArrayHasKey('cards_left', $data);
        $this->assertEquals(47, $data['cards_left']);
    }

    public function testDrawCardsTooMany(): void
    {
        $deck = new DeckOfCards();
        $session = $this->createMock(SessionInterface::class);
        $session->method('has')->willReturn(true);
        $session->method('get')->willReturn($deck);

        $response = $this->controller->drawCards($session, 1000);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $data = json_decode($response->getContent() ?: '', true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testFormDrawRedirect(): void
    {
        $request = new Request([], ['number' => '3']);
        $response = $this->controller->formDrawRedirect($request);
        $this->assertInstanceOf(RedirectResponse::class, $response);

        $this->assertStringContainsString('/api/deck/draw/3', $response->getTargetUrl());

        $request2 = new Request([], ['number' => '-5']);
        $response2 = $this->controller->formDrawRedirect($request2);
        $this->assertStringContainsString('/api/deck/draw/1', $response2->getTargetUrl());
    }

    public function testListBooksReturnsJson(): void
    {
        $book = $this->createMock(Library::class);
        $book->method('getId')->willReturn(1);
        $book->method('getTitel')->willReturn('Test Title');
        $book->method('getAuthor')->willReturn('Author Name');
        $book->method('getIsbn')->willReturn('1234567890');
        $book->method('getImageUrl')->willReturn('http://example.com/image.jpg');

        $repo = $this->createMock(LibraryRepository::class);
        $repo->method('findAll')->willReturn([$book]);

        $response = $this->controller->listBooks($repo);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = json_decode($response->getContent() ?: '', true);

        $this->assertCount(1, $data);
        $this->assertEquals('Test Title', $data[0]['title']);
    }

    public function testGetBookByIsbnFound(): void
    {
        $isbn = '1234567890';
        $book = $this->createMock(Library::class);
        $book->method('getId')->willReturn(2);
        $book->method('getTitel')->willReturn('Another Title');
        $book->method('getAuthor')->willReturn('Another Author');
        $book->method('getIsbn')->willReturn($isbn);
        $book->method('getImageUrl')->willReturn('http://example.com/another.jpg');

        $repo = $this->createMock(LibraryRepository::class);
        $repo->method('findOneBy')->with(['isbn' => $isbn])->willReturn($book);

        $response = $this->controller->getBookByIsbn($repo, $isbn);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $data = json_decode($response->getContent() ?: '', true);
        $this->assertEquals('Another Title', $data['titel']);
    }

    public function testGetBookByIsbnNotFound(): void
    {
        $repo = $this->createMock(LibraryRepository::class);
        $repo->method('findOneBy')->willReturn(null);

        $response = $this->controller->getBookByIsbn($repo, '0000');
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $data = json_decode($response->getContent() ?: '', true);
        $this->assertArrayHasKey('error', $data);
    }
}
