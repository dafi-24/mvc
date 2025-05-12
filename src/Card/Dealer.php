<?php

namespace App\Card;

use App\Card\Card;
use App\Card\CardHand;

/**
 * Klass Dealer
 *
 * Representerar dealern i spelet, som drar kort tills handens värde minst är 17.
 */
class Dealer extends Player
{
    /**
     * Spelar dealerens tur.
     *
     * Dealern drar kort från given kortlek så länge handens värde är under 17,
     * enligt standard blackjack-regler.
     *
     * @param DeckOfCards $deck Kortleken som dealern drar från.
     * @return void
     */
    public function playTurn(DeckOfCards $deck): void
    {
        while ($this->getHand()->getValue() < 17) {
            $this->hit($deck->draw(1)[0]);
        }
    }
}
