<?php
namespace App\Card;

use App\Card\CardGraphic;
use App\Card\Card;
use App\Card\CardHand;

// Spelar-modell
class Player
{
    /** @var CardHand */
    private $hand;

    public function __construct()
    {
        $this->hand = new CardHand();
    }

    /** Lägg till kort på spelarens hand */
    public function hit(CardGraphic $card): void
    {
        $this->hand->add($card);
    }

    /** Spelaren väljer stand – inga fler kort */
    public function stand(): void
    {
        // ev. flagga för att spelaren är klar
    }

    /** Returnerar spelarens hand */
    public function getHand(): CardHand
    {
        return $this->hand;
    }

    /** Kollar om spelaren gått bust */
    public function isBust(): bool
    {
        return $this->hand->getValue() > 21;
    }

    public function getValue(): int
    {
        return $this->hand->getValue();
    }
}
