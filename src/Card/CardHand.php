<?php
namespace App\Card;

class CardHand
{
    /** @var Card[] */
    private array $hand = [];

    /** Lägg till ett kort i handen */
    public function add(Card $card): void
    {
        $this->hand[] = $card;
    }

    /** Returnerar alla kort i handen */
    public function getHand(): array
    {
        return $this->hand;
    }

    /** Beräkna handens totala värde, inkl. ess-justering */
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

        // Justera ess (A) från 11 till 1 vid behov
        while ($total > 21 && $aces > 0) {
            $total -= 10;
            $aces--;
        }

        return $total;
    }

    /** Hjälp för att kolla bust */
    public function isBust(): bool
    {
        return $this->getValue() > 21;
    }
}
