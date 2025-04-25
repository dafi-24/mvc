<?php

namespace App\Dice;

use App\Dice\Dice;

class DiceHand
{
    /**
     * @var array<Dice>
     */
    private array $hand = [];

    public function add(Dice $die): void
    {
        $this->hand[] = $die;
    }

    public function roll(): void
    {
        foreach ($this->hand as $die) {
            $die->roll();
        }
    }

    public function getNumberDices(): int
    {
        return count($this->hand);
    }

    /**
     * Hämta värdena för alla tärningar.
     *
     * @return list<int> Array med tärningarnas värden
     */
    public function getValues(): array
    {
        $values = [];
        foreach ($this->hand as $die) {
            $values[] = $die->getValue();
        }
        return $values;
    }

    /**
     * Hämta strängrepresentationen för alla tärningar.
     *
     * @return list<string> Array med tärningarnas strängrepresentationer
     */
    public function getString(): array
    {
        $values = [];
        foreach ($this->hand as $die) {
            $values[] = $die->getAsString();
        }
        return $values;
    }
}
