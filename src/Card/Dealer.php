<?php
namespace App\Card;

use App\Card\Card;
use App\Card\CardHand;

class Dealer extends Player
{
    public function playTurn(DeckOfCards $deck): void
    {
        while ($this->getHand()->getValue() < 17) {
            $this->hit($deck->draw(1)[0]);
        }
    }
}
