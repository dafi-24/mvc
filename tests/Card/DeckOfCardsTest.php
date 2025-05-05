<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use App\Card\DeckOfCards;
use App\Card\CardGraphic;

class DeckOfCardsTest extends TestCase
{
    private DeckOfCards $deck;

    protected function setUp(): void
    {
        $this->deck = new DeckOfCards();
    }

    public function testResetDeck(): void
    {
        $this->deck->resetDeck();
        $this->assertCount(52, $this->deck->getCards());

        $cards = $this->deck->getCards();
        $suits = ['spades', 'hearts', 'diamonds', 'clubs'];
        $values = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];

        $suitsValues = array_map(function ($suit) use ($values) {
            return array_map(fn ($value) => $suit . '-' . $value, $values);
        }, $suits);
        $suitsValues = array_merge(...$suitsValues);

        foreach ($cards as $card) {
            $this->assertContains($card->getSuit() . '-' . $card->getValue(), $suitsValues);
        }
    }

    public function testShuffle(): void
    {
        $this->deck->resetDeck();
        $initialCards = $this->deck->getCards();

        $this->deck->shuffle();
        $shuffledCards = $this->deck->getCards();

        $this->assertNotEquals($initialCards, $shuffledCards);
    }

    public function testDraw(): void
    {
        $this->deck->resetDeck();
        $drawnCards = $this->deck->draw(1);
        $this->assertCount(1, $drawnCards);
        $this->assertCount(51, $this->deck->getCards());

        $this->deck->resetDeck();
        $drawnCards = $this->deck->draw(5);
        $this->assertCount(5, $drawnCards);
        $this->assertCount(47, $this->deck->getCards());
    }

    public function testCardsLeft(): void
    {
        $this->deck->resetDeck();
        $this->assertEquals(52, $this->deck->cardsLeft());

        $this->deck->draw(1);
        $this->assertEquals(51, $this->deck->cardsLeft());

        $this->deck->draw(51);
        $this->assertEquals(0, $this->deck->cardsLeft());
    }

    public function testGetCards(): void
    {
        $this->deck->resetDeck();
        $this->assertCount(52, $this->deck->getCards());
    }
}
