<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use App\Card\BlackjackGame;
use App\Card\CardGraphic;
use App\Card\CardHand;
use App\Card\Player;
use App\Card\Dealer;
use ReflectionClass;

class BlackjackGameTest extends TestCase
{
    public function testStartDealsTwoCardsToPlayerAndDealer(): void
    {
        $game = new BlackjackGame();
        $game->start();

        $this->assertCount(2, $game->getPlayer()->getHand()->getHand());
        $this->assertCount(2, $game->getDealer()->getHand()->getHand());
    }

    public function testPlayerTurnHitAddsCard(): void
    {
        $game = new BlackjackGame();

        $hand = new CardHand();
        $hand->add(new CardGraphic('hearts', '2'));
        $hand->add(new CardGraphic('spades', '3'));

        $ref = new ReflectionClass(Player::class);
        $prop = $ref->getProperty('hand');
        $prop->setAccessible(true);
        $prop->setValue($game->getPlayer(), $hand);

        $initial = count($game->getPlayer()->getHand()->getHand());

        $res = $game->playerTurn('hit');

        $this->assertTrue($res);
        $this->assertCount($initial + 1, $game->getPlayer()->getHand()->getHand());
    }

    public function testPlayerTurnDoubleAddsCard(): void
    {
        $game = new BlackjackGame();

        $hand = new CardHand();
        $hand->add(new CardGraphic('hearts', '2'));
        $hand->add(new CardGraphic('spades', '3'));

        $ref = new ReflectionClass(Player::class);
        $prop = $ref->getProperty('hand');
        $prop->setAccessible(true);
        $prop->setValue($game->getPlayer(), $hand);

        $initial = count($game->getPlayer()->getHand()->getHand());
        $res = $game->playerTurn('double');

        $this->assertTrue($res);
        $this->assertCount($initial + 1, $game->getPlayer()->getHand()->getHand());
    }

    public function testPlayerTurnStandReturnsTrueAndNoCardAdded(): void
    {
        $game = new BlackjackGame();
        $game->start();

        $initial = count($game->getPlayer()->getHand()->getHand());
        $res = $game->playerTurn('stand');

        $this->assertTrue($res);
        $this->assertCount($initial, $game->getPlayer()->getHand()->getHand());
    }

    public function testDealerTurnDrawsUntilAtLeast17(): void
    {
        $game = new BlackjackGame();
        $game->start();

        $before = $game->getDealer()->getHand()->getValue();
        $game->dealerTurn();
        $after = $game->getDealer()->getHand()->getValue();

        $this->assertGreaterThanOrEqual(17, $after);
        $this->assertGreaterThanOrEqual($before, $after);
    }

    public function testDetermineWinnerDraw(): void
    {
        $game = new BlackjackGame();
        $game->start();

        $player = $game->getPlayer();
        $dealer = $game->getDealer();

        $hand20 = new CardHand();
        $hand20->add(new CardGraphic('hearts', 'K'));
        $hand20->add(new CardGraphic('clubs', 'Q'));

        $playerReflection = new ReflectionClass(Player::class);
        $playerHandProp = $playerReflection->getProperty('hand');
        $playerHandProp->setAccessible(true);
        $playerHandProp->setValue($player, clone $hand20);

        $dealerHandProp = $playerReflection->getProperty('hand');
        $dealerHandProp->setAccessible(true);
        $dealerHandProp->setValue($dealer, clone $hand20);

        $this->assertEquals('Draw', $game->determineWinner());
    }

    public function testDetermineWinnerDealerBusts(): void
    {
        $game = new BlackjackGame();
        $game->start();

        $player = $game->getPlayer();
        $dealer = $game->getDealer();

        $handP = new CardHand();
        $handP->add(new CardGraphic('hearts', '10'));

        $handD = new CardHand();
        $handD->add(new CardGraphic('clubs', 'K'));
        $handD->add(new CardGraphic('diamonds', 'Q'));
        $handD->add(new CardGraphic('spades', 'J'));

        $playerReflection = new ReflectionClass(Player::class);
        $playerHandProp = $playerReflection->getProperty('hand');
        $playerHandProp->setAccessible(true);
        $playerHandProp->setValue($player, $handP);
        $playerHandProp->setValue($dealer, $handD);

        $this->assertEquals('Player wins', $game->determineWinner());
    }

    public function testDetermineWinnerPlayerBusts(): void
    {
        $game = new BlackjackGame();
        $game->start();

        $player = $game->getPlayer();
        $player->hit(new CardGraphic('hearts', '10'));
        $player->hit(new CardGraphic('diamonds', '10'));
        $player->hit(new CardGraphic('spades', '5'));

        $this->assertEquals('Dealer wins – player busts', $game->determineWinner());
    }

    public function testDetermineWinnerPlayerWins(): void
    {
        $game = new BlackjackGame();
        $game->start();

        $player = $game->getPlayer();
        $dealer = $game->getDealer();

        $handP = new CardHand();
        $handP->add(new CardGraphic('hearts', 'A'));
        $handP->add(new CardGraphic('spades', '9'));

        $handD = new CardHand();
        $handD->add(new CardGraphic('hearts', '8'));
        $handD->add(new CardGraphic('spades', '9'));

        $ref = new ReflectionClass(Player::class);
        $prop = $ref->getProperty('hand');
        $prop->setAccessible(true);
        $prop->setValue($player, $handP);
        $prop->setValue($dealer, $handD);

        $this->assertEquals('Player wins', $game->determineWinner());
    }

    public function testDetermineWinnerDealerWins(): void
    {
        $game = new BlackjackGame();
        $game->start();

        $player = $game->getPlayer();
        $dealer = $game->getDealer();

        $handP = new CardHand();
        $handP->add(new CardGraphic('hearts', '7'));
        $handP->add(new CardGraphic('spades', '8'));

        $handD = new CardHand();
        $handD->add(new CardGraphic('hearts', 'K'));
        $handD->add(new CardGraphic('spades', '10'));

        $ref = new ReflectionClass(Player::class);
        $prop = $ref->getProperty('hand');
        $prop->setAccessible(true);
        $prop->setValue($player, $handP);
        $prop->setValue($dealer, $handD);

        $this->assertEquals('Dealer wins', $game->determineWinner());
    }
}
