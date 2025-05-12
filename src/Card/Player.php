<?php

namespace App\Card;

use App\Card\Card;
use App\Card\CardHand;

/**
 * Klass Player
 *
 * Representerar en spelare i blackjack, med en hand av kort och grundläggande spelhandlingar.
 */
class Player
{
    /**
     * @var CardHand $hand Spelarens hand med kort.
     */
    private CardHand $hand;

    /**
     * Konstruktor för Player.
     *
     * Skapar en ny CardHand-instans för spelaren.
     */
    public function __construct()
    {
        $this->hand = new CardHand();
    }

    /**
     * Dra ett kort (hit) och lägg det i spelarens hand.
     *
     * @param Card $card Kortet som spelaren drar.
     * @return void
     */
    public function hit(Card $card): void
    {
        $this->hand->add($card);
    }

    /**
     * Spelaren står (stand) – inga fler kort dras.
     *
     * @return void
     */
    public function stand(): void
    {
        // Inga åtgärder behövs när spelaren står.
    }

    /**
     * Hämta spelarens hand.
     *
     * @return CardHand Spelarens kortsamling.
     */
    public function getHand(): CardHand
    {
        return $this->hand;
    }

    /**
     * Kontrollera om spelaren har bust (över 21 poäng).
     *
     * @return bool True om spelarens handsvärde är över 21, annars false.
     */
    public function isBust(): bool
    {
        return $this->hand->getValue() > 21;
    }

    /**
     * Hämta spelarens nuvarande poängvärde.
     *
     * @return int Spelarens handvärde enligt blackjack-regler.
     */
    public function getValue(): int
    {
        return $this->hand->getValue();
    }
}
