<?php

namespace App\Card;

class CardHand
{
    private array $hand = [];

    public function add(Card $card): void
    {
        $this->hand[] = $card;
    }

    public function getHand(): array
    {
        return $this->hand;
    }
}
