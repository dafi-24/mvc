<?php

namespace App\Tests\Controller;

use App\Card\BlackjackGame;
use App\Card\Player;
use App\Card\Dealer;
use App\Card\CardHand;
use App\Controller\GameController;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment as TwigEnvironment;

class GameControllerTest extends TestCase
{
    private GameController $controller;
    /** @var TwigEnvironment|MockObject */
    private $twig;
    private Container $container;

    protected function setUp(): void
    {
        $this->twig = $this->getMockBuilder(TwigEnvironment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->twig->method('render')->willReturn('rendered');

        $router = $this->getMockBuilder(UrlGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $router->method('generate')->willReturnCallback(function(string $route, array $params = []) {
            return $route === 'game_play' ? '/game/play' : '/game/result';
        });

        $this->container = new Container();
        $this->container->set('twig', $this->twig);
        $this->container->set('router', $router);

        $this->controller = new GameController();
        $this->controller->setContainer($this->container);
    }

    public function testHomeRenders(): void
    {
        $response = $this->controller->home();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('rendered', $response->getContent());
    }

    public function testDocRenders(): void
    {
        $response = $this->controller->doc();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('rendered', $response->getContent());
    }

    public function testStartCreatesGameAndRedirects(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())
            ->method('set')
            ->with('game', $this->isInstanceOf(BlackjackGame::class));

        $response = $this->controller->start($session);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/game/play', $response->getTargetUrl());
    }

    public function testPlayRendersWithGameData(): void
    {
        $handObj = $this->getMockBuilder(CardHand::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getHand'])
            ->getMock();
        $handObj->method('getHand')->willReturn(['A', 'K']);

        $player = $this->getMockBuilder(Player::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getHand', 'getValue'])
            ->getMock();
        $player->method('getHand')->willReturn($handObj);
        $player->method('getValue')->willReturn(21);

        $dealer = $this->getMockBuilder(Dealer::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getHand', 'getValue'])
            ->getMock();
        $dealer->method('getHand')->willReturn($handObj);
        $dealer->method('getValue')->willReturn(18);

        $game = $this->createMock(BlackjackGame::class);
        $game->method('getPlayer')->willReturn($player);
        $game->method('getDealer')->willReturn($dealer);

        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->with('game')->willReturn($game);

        $response = $this->controller->play($session);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('rendered', $response->getContent());
    }

    public function testHitAliveRedirects(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $game = $this->createMock(BlackjackGame::class);
        $game->method('playerTurn')->with('hit')->willReturn(true);
        $session->method('get')->with('game')->willReturn($game);
        $session->expects($this->once())->method('set')->with('game', $game);

        $response = $this->controller->hit($session);
        $this->assertSame('/game/play', $response->getTargetUrl());
    }

    public function testHitDeadRedirects(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $game = $this->createMock(BlackjackGame::class);
        $game->method('playerTurn')->with('hit')->willReturn(false);
        $session->method('get')->with('game')->willReturn($game);
        $session->expects($this->once())->method('set')->with('game', $game);

        $response = $this->controller->hit($session);
        $this->assertSame('/game/result', $response->getTargetUrl());
    }

    public function testStandAlwaysRedirectsToResult(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $game = $this->createMock(BlackjackGame::class);
        $game->expects($this->once())->method('playerTurn')->with('stand');
        $game->expects($this->once())->method('dealerTurn');
        $session->method('get')->with('game')->willReturn($game);
        $session->expects($this->once())->method('set')->with('game', $game);

        $response = $this->controller->stand($session);
        $this->assertSame('/game/result', $response->getTargetUrl());
    }

    public function testDoubleHandlesAliveAndRedirects(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $game = $this->createMock(BlackjackGame::class);
        $game->method('playerTurn')->with('double')->willReturn(true);
        $game->expects($this->once())->method('dealerTurn');
        $session->method('get')->with('game')->willReturn($game);
        $session->expects($this->once())->method('set')->with('game', $game);

        $response = $this->controller->double($session);
        $this->assertSame('/game/result', $response->getTargetUrl());
    }

    public function testResultRendersWithWinner(): void
    {
        $handObj = $this->getMockBuilder(CardHand::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getHand'])
            ->getMock();
        $handObj->method('getHand')->willReturn(['10', '7']);

        $player = $this->getMockBuilder(Player::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getHand', 'getValue'])
            ->getMock();
        $player->method('getHand')->willReturn($handObj);
        $player->method('getValue')->willReturn(17);

        $dealer = $this->getMockBuilder(Dealer::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getHand', 'getValue'])
            ->getMock();
        $dealer->method('getHand')->willReturn($handObj);
        $dealer->method('getValue')->willReturn(17);

        $game = $this->createMock(BlackjackGame::class);
        $game->method('getPlayer')->willReturn($player);
        $game->method('getDealer')->willReturn($dealer);
        $game->method('determineWinner')->willReturn('draw');

        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->with('game')->willReturn($game);

        $response = $this->controller->result($session);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('rendered', $response->getContent());
    }
}
