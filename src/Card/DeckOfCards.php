<?php

namespace App\Card;

use App\Card\Card;

class DeckOfCards
{
    /**
     * @var Card[]
     */
    private array $cards = [];

    public function __construct()
    {
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
     * Dra ett eller flera kort från kortleken.
     *
     * @param int $num Antal kort att dra.
     * @return Card[] De dragna korten.
     */
    public function draw(int $num = 1): array
    {
        return array_splice($this->cards, 0, $num);
    }

    /**
     * Hämta alla kort i leken.
     *
     * @return Card[] Kortleken.
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    public function cardsLeft(): int
    {
        return count($this->cards);
    }
}
