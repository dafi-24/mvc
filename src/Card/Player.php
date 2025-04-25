<?php

namespace App\Card;

use App\Card\CardGraphic;
use App\Card\Card;
use App\Card\CardHand;

class Player
{
    private CardHand $hand;

    public function __construct()
    {
        $this->hand = new CardHand();
    }

    public function hit(Card $card): void
    {
        $this->hand->add($card);
    }

    public function stand(): void
    {
        // Player stands, no action needed
    }

    public function getHand(): CardHand
    {
        return $this->hand;
    }

    public function isBust(): bool
    {
        return $this->hand->getValue() > 21;
    }

    public function getValue(): int
    {
        return $this->hand->getValue();
    }
}
