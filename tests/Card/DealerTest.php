<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use App\Card\Dealer;
use App\Card\DeckOfCards;
use App\Card\Player;
use App\Card\CardHand;
use ReflectionClass;

class DealerTest extends TestCase
{
    public function testDealerInheritsFromPlayer(): void
    {
        $dealer = new Dealer();
        $this->assertInstanceOf(Player::class, $dealer);
    }

    public function testPlayTurnStopsAtSeventeenOrMore(): void
    {
        $deck = $this->createMock(DeckOfCards::class);
        $hand = $this->createMock(CardHand::class);

        $hand->method('getValue')
             ->willReturnOnConsecutiveCalls(10, 15, 17, 17);

        $deck->expects($this->exactly(2))
             ->method('draw')
             ->with(1)
             ->willReturn([ $this->createMock(\App\Card\Card::class) ]);

        $dealer = new Dealer();

        $ref = new ReflectionClass(Dealer::class);
        $parentClass = $ref->getParentClass();
        if ($parentClass !== false) {
            $prop = $parentClass->getProperty('hand');
            $prop->setAccessible(true);
            $prop->setValue($dealer, $hand);
        }

        $dealer->playTurn($deck);

        $this->assertGreaterThanOrEqual(17, $hand->getValue());
    }

    public function testPlayTurnDoesNotDrawIfAlreadySeventeenOrMore(): void
    {
        $deck = $this->createMock(DeckOfCards::class);
        $hand = $this->createConfiguredMock(CardHand::class, ['getValue' => 20]);

        $deck->expects($this->never())->method('draw');

        $dealer = new Dealer();
        $ref = new ReflectionClass(Dealer::class);
        $parentClass = $ref->getParentClass();
        if ($parentClass !== false) {
            $prop = $parentClass->getProperty('hand');
            $prop->setAccessible(true);
            $prop->setValue($dealer, $hand);
        }

        $dealer->playTurn($deck);
    }
}
