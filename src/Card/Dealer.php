<?php
namespace App\Card;

use App\Card\Card;
use App\Card\CardHand;

// Dealer är en special-Player med automatisk dragning
class Dealer extends Player
{
    /** Dra kort tills hand-värde är minst 17 */
    public function playTurn(DeckOfCards $deck): void
    {
        while ($this->getHand()->getValue() < 17) {
            $this->hit($deck->draw(1)[0]);
        }
    }
}
