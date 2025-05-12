<?php

namespace App\Card;

/**
 * Klass CardHand
 *
 * Hanterar en samling kort och beräknar dess värde enligt blackjack-regler.
 */
class CardHand
{
    /**
     * @var array<Card> $hand Array som innehåller kortobjekt i handen.
     */
    private array $hand = [];

    /**
     * Lägg till ett kort i handen.
     *
     * @param Card $card Kortobjekt som ska läggas till.
     * @return void
     */
    public function add(Card $card): void
    {
        $this->hand[] = $card;
    }

    /**
     * Hämta alla kort i handen.
     *
     * @return array<Card> Array med samtliga kortobjekt.
     */
    public function getHand(): array
    {
        return $this->hand;
    }

    /**
     * Beräkna handens totala värde enligt blackjack-regler.
     *
     * Numeriska kort ger sitt numeriska värde,
     * knekt, dam och kung ger 10 poäng vardera,
     * ess ger initialt 11 poäng men justeras automatiskt till 1 om summan överstiger 21.
     *
     * @return int Handens totala poäng.
     */
    public function getValue(): int
    {
        $total = 0;
        $aces  = 0;

        foreach ($this->hand as $card) {
            $value = $card->getValue();
            if (is_numeric($value)) {
                $total += (int)$value;
            } elseif (in_array($value, ['J', 'Q', 'K'], true)) {
                $total += 10;
            } elseif ($value === 'A') {
                $total += 11;
                $aces++;
            }
        }

        // Justera ess (A) från 11 till 1 poäng vid behov
        while ($total > 21 && $aces > 0) {
            $total -= 10;
            $aces--;
        }

        return $total;
    }

    /**
     * Kontrollera om handen är "bust" (över 21 poäng).
     *
     * @return bool True om handens värde är större än 21, annars false.
     */
    public function isBust(): bool
    {
        return $this->getValue() > 21;
    }
}
