<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use App\Card\CardHand;
use App\Card\Card;

class CardHandTest extends TestCase
{
    private CardHand $hand;

    protected function setUp(): void
    {
        $this->hand = new CardHand();
    }

    public function testAddAddsCardToHand(): void
    {
        $card = $this->createMock(Card::class);
        $this->hand->add($card);
        $this->assertCount(1, $this->hand->getHand());
        $this->assertSame($card, $this->hand->getHand()[0]);
    }

    public function testGetValueHandlesAcesAndFaces(): void
    {
        $card1 = $this->createConfiguredMock(Card::class, ['getValue' => '5']);
        $card2 = $this->createConfiguredMock(Card::class, ['getValue' => 'K']);
        $card3 = $this->createConfiguredMock(Card::class, ['getValue' => 'A']);

        $this->hand->add($card1);
        $this->hand->add($card2);
        $this->hand->add($card3);

        $this->assertEquals(16, $this->hand->getValue());
    }

    public function testIsBustWhenValueOver21(): void
    {
        $face = $this->createConfiguredMock(Card::class, ['getValue' => 'K']);
        $this->hand->add($face);
        $this->hand->add($face);
        $this->hand->add($face);

        $this->assertTrue($this->hand->isBust());
    }

    public function testIsNotBustWhenValue21OrLess(): void
    {
        $card = $this->createConfiguredMock(Card::class, ['getValue' => '10']);
        $ace  = $this->createConfiguredMock(Card::class, ['getValue' => 'A']);
        $this->hand->add($card);
        $this->hand->add($ace);

        $this->assertFalse($this->hand->isBust());
    }
}
