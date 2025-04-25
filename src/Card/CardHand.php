<?php

namespace App\Card;

class CardHand
{
    /**
     * @var array<Card> Array som innehåller kortobjekt
     */
    private array $hand = [];

    /**
     * Lägg till ett kort i handen.
     */
    public function add(Card $card): void
    {
        $this->hand[] = $card;
    }

    /**
     * Hämta alla kort i handen.
     *
     * @return array<Card> Array med kortobjekt
     */
    public function getHand(): array
    {
        return $this->hand;
    }

    public function getValue(): int
    {
        $total = 0;
        $aces  = 0;

        foreach ($this->hand as $card) {
            $value = $card->getValue();
            if (is_numeric($value)) {
                $total += (int)$value;
            } elseif (in_array($value, ['J','Q','K'], true)) {
                $total += 10;
            } elseif ($value === 'A') {
                $total += 11;
                $aces++;
            }
        }

        while ($total > 21 && $aces > 0) {
            $total -= 10;
            $aces--;
        }

        return $total;
    }

    public function isBust(): bool
    {
        return $this->getValue() > 21;
    }
}
