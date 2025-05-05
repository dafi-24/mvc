<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use App\Card\CardGraphic;

class CardGraphicTest extends TestCase
{
    public function testGetUnicodeCombinesValueAndSuitIcon(): void
    {
        $card = new CardGraphic('hearts', 'K');
        $this->assertEquals('K♥', $card->getUnicode());

        $card2 = new CardGraphic('spades', '10');
        $this->assertEquals('10♠', $card2->getUnicode());
    }

    public function testGetColorRedAndBlack(): void
    {
        $redCard   = new CardGraphic('diamonds', 'A');
        $blackCard = new CardGraphic('clubs', '2');

        $this->assertEquals('red', $redCard->getColor());
        $this->assertEquals('black', $blackCard->getColor());
    }

    public function testGetColoredUnicodeWrapsRedInSpan(): void
    {
        $red      = new CardGraphic('hearts', 'Q');
        $expected = '<span style="color:red;">Q♥</span>';
        $this->assertEquals($expected, $red->getColoredUnicode());
    }

    public function testGetColoredUnicodeReturnsUnicodeForBlack(): void
    {
        $black = new CardGraphic('spades', '3');
        $this->assertEquals('3♠', $black->getColoredUnicode());
    }

    public function testToStringReturnsColoredUnicode(): void
    {
        $card = new CardGraphic('hearts', '5');
        $this->assertEquals($card->getColoredUnicode(), (string) $card);
    }

    public function testInvalidSuitIconFallsBackToRawSuit(): void
    {
        $card = new CardGraphic('unknown', '7');
        $this->assertEquals('7unknown', $card->getUnicode());
    }
}
