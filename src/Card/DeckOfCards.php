<?php

namespace App\Card;

use App\Card\Card;
use App\Card\CardGraphic;

/**
 * Klass DeckOfCards
 *
 * Representerar en komplett lek med grafiska spelkort.
 * Innehåller funktionalitet för att återställa, blanda och dra kort.
 */
class DeckOfCards
{
    /**
     * @var array<CardGraphic> $cards Array som innehåller kortobjekt i leken.
     */
    private array $cards = [];

    /**
     * Konstruktor för DeckOfCards.
     *
     * Initialiserar leken genom att skapa en fullständig kortlek.
     */
    public function __construct()
    {
        $this->resetDeck();
    }

    /**
     * Återställ leken till en hel, ordnad kortlek.
     *
     * Skapar 52 kort (A–K i fyra färger) och fyller arrayen på nytt.
     *
     * @return void
     */
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

    /**
     * Blanda kortleken slumpmässigt.
     *
     * @return void
     */
    public function shuffle(): void
    {
        shuffle($this->cards);
    }

    /**
     * Dra ett eller flera kort från toppen av leken.
     *
     * Tar bort och returnerar de första $num korten i leken.
     *
     * @param int $num Antal kort att dra (standard 1).
     * @return array<CardGraphic> Array med de dragna korten.
     */
    public function draw(int $num = 1): array
    {
        return array_splice($this->cards, 0, $num);
    }

    /**
     * Hämta alla återstående kort i leken utan att ta bort dem.
     *
     * @return array<CardGraphic> Array med nuvarande kort i leken.
     */
    public function getCards(): array
    {
        return array_values($this->cards);
    }

    /**
     * Räkna hur många kort som finns kvar i leken.
     *
     * @return int Antal kvarvarande kort.
     */
    public function cardsLeft(): int
    {
        return count($this->cards);
    }
}
