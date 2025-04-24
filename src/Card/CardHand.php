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

    public function getValue(): int
    {
        $total = 0;
        $aces  = 0;

        foreach ($this->hand as $card) {
            $v = $card->getValue();
            if (is_numeric($v)) {
                $total += (int)$v;
            } elseif (in_array($v, ['J','Q','K'], true)) {
                $total += 10;
            } elseif ($v === 'A') {
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
