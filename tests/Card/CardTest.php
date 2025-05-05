<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use App\Card\Card;

class CardTest extends TestCase
{
    public function testGettersReturnAssignedValues(): void
    {
        $card = new Card('hearts', 'A');
        $this->assertEquals('hearts', $card->getSuit());
        $this->assertEquals('A', $card->getValue());
    }

    public function testGetSuitIconReturnsCorrectSymbol(): void
    {
        $card = new Card('hearts', 'A');
        $this->assertEquals('♠', $card->getSuitIcon('spades'));
        $this->assertEquals('♥', $card->getSuitIcon('hearts'));
        $this->assertEquals('♦', $card->getSuitIcon('diamonds'));
        $this->assertEquals('♣', $card->getSuitIcon('clubs'));
        $this->assertEquals('unknown', $card->getSuitIcon('unknown'));
    }

    public function testGetColorRedAndBlack(): void
    {
        $red = new Card('hearts', '10');
        $black = new Card('spades', 'K');
        $this->assertEquals('red', $red->getColor());
        $this->assertEquals('black', $black->getColor());
    }
}
