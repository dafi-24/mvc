<?php

namespace App\Tests\Controller;

use App\Controller\CardController;
use App\Card\DeckOfCards;
use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment as TwigEnvironment;

class CardControllerTest extends TestCase
{
    private CardController $controller;
    /** @var TwigEnvironment|MockObject */
    private $twig;
    private Container $container;

    protected function setUp(): void
    {
        $this->twig = $this->getMockBuilder(TwigEnvironment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->twig
            ->expects($this->any())
            ->method('render')
            ->willReturn('rendered');

        $router = $this->getMockBuilder(UrlGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $router
            ->method('generate')
            ->with('card_home', [])
            ->willReturn('/card');

        $this->container = new Container();
        $this->container->set('twig', $this->twig);
        $this->container->set('router', $router);

        $this->controller = new CardController();
        $this->controller->setContainer($this->container);
    }

    public function testHomeRendersWithSessionData(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->once())
            ->method('all')
            ->willReturn(['foo' => 'bar']);

        $response = $this->controller->home($session);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('rendered', $response->getContent());
    }

    public function testIndexRendersWithSessionData(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $sessionData = ['a' => 1, 'b' => 2];
        $session
            ->expects($this->once())
            ->method('all')
            ->willReturn($sessionData);

        $response = $this->controller->index($session);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('rendered', $response->getContent());
    }

    public function testSessionDeleteClearsSessionAndRedirects(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->once())
            ->method('clear');

        $response = $this->controller->sessionDelete($session);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/card', $response->getTargetUrl());
    }

    public function testDeckCreatesNewDeckWhenNoneInSession(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session
            ->method('has')
            ->with('card_deck')
            ->willReturn(false);
        $session
            ->expects($this->once())
            ->method('set')
            ->with('card_deck', $this->isInstanceOf(DeckOfCards::class));
        $session
            ->method('get')
            ->with('card_deck')
            ->willReturn(new DeckOfCards());

        $response = $this->controller->deck($session);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('rendered', $response->getContent());
    }

    public function testDeckShuffleAlwaysShufflesAndRenders(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->once())
            ->method('set')
            ->with('card_deck', $this->callback(fn($deck) => $deck instanceof DeckOfCards));

        $response = $this->controller->deckShuffle($session);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('rendered', $response->getContent());
    }

    public function testDeckDrawCreatesDeckIfMissingAndDrawsOneCard(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->method('has')->with('card_deck')->willReturn(false);
        $session
            ->expects($this->exactly(2))
            ->method('set')
            ->with('card_deck', $this->isInstanceOf(DeckOfCards::class));
        $session
            ->method('get')
            ->with('card_deck')
            ->willReturn(new DeckOfCards());

        $response = $this->controller->deckDraw($session);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('rendered', $response->getContent());
    }

    public function testDeckDrawNumberThrowsWhenTooMany(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Du kan inte dra mer än antalet kort i leken!');

        $session = $this->createMock(SessionInterface::class);
        $this->controller->deckDrawNumber(53, $session);
    }

    public function testDeckDrawNumberDrawsSpecifiedCount(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->method('has')->with('card_deck')->willReturn(false);
        $session
            ->expects($this->exactly(2))
            ->method('set')
            ->with('card_deck', $this->isInstanceOf(DeckOfCards::class));
        $session
            ->method('get')
            ->with('card_deck')
            ->willReturn(new DeckOfCards());

        $response = $this->controller->deckDrawNumber(5, $session);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('rendered', $response->getContent());
    }
}
