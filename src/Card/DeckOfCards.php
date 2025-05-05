<?php

namespace App\Card;

use App\Card\Card;
use App\Card\CardGraphic;

class DeckOfCards
{
    /**
     * @var array<CardGraphic> Array som innehåller kortobjekt
     */
    private array $cards = [];

    public function __construct()
    {
        $this->resetDeck();
    }

    public function resetDeck(): void
    {
        $this->cards = [];

        $suits = ['spades', 'hearts', 'diamonds', 'clubs'];
        $values = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $this->cards[] = new CardGraphic($suit, $value);
            }
        }
    }

    public function shuffle(): void
    {
        shuffle($this->cards);
    }

    /**
     * Dra ett eller flera kort från leken.
     *
     * @param int $num Antal kort att dra
     * @return array<CardGraphic> Array med dragna kort
     */
    public function draw(int $num = 1): array
    {
        return array_splice($this->cards, 0, $num);
    }

    /**
     * Hämta alla kort i leken.
     *
     * @return array<CardGraphic> Array med kortobjekt
     */
    public function getCards(): array
    {
        return array_values($this->cards);
    }

    public function cardsLeft(): int
    {
        return count($this->cards);
    }
}
