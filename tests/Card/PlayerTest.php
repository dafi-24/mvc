<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use App\Card\Player;
use App\Card\Card;
use App\Card\CardHand;

class PlayerTest extends TestCase
{
    private Player $player;

    protected function setUp(): void
    {
        $this->player = new Player();
    }

    public function testNewPlayerHasEmptyHand(): void
    {
        $hand = $this->player->getHand();
        $this->assertInstanceOf(CardHand::class, $hand);
        $this->assertCount(0, $hand->getHand());
        $this->assertFalse($this->player->isBust());
        $this->assertEquals(0, $this->player->getValue());
    }

    public function testHitAddsCardAndUpdatesValue(): void
    {
        $card = $this->createConfiguredMock(Card::class, ['getValue' => '9']);
        $this->player->hit($card);

        $this->assertCount(1, $this->player->getHand()->getHand());
        $this->assertEquals(9, $this->player->getValue());
    }

    public function testStandDoesNotChangeHand(): void
    {
        $card = $this->createConfiguredMock(Card::class, ['getValue' => '10']);
        $this->player->hit($card);
        $this->player->stand();

        $this->assertCount(1, $this->player->getHand()->getHand());
    }

    public function testIsBustWhenValueExceeds21(): void
    {
        $face = $this->createConfiguredMock(Card::class, ['getValue' => 'K']);
        $this->player->hit($face);
        $this->player->hit($face);
        $this->player->hit($face);

        $this->assertTrue($this->player->isBust());
    }
}
