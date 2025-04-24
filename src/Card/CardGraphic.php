<?php

namespace App\Card;

class CardGraphic extends Card
{
    public function __construct(string $suit, string $value)
    {
        parent::__construct($suit, $value);
    }

    public function getUnicode(): string
    {
        return $this->value . self::getSuitIcon($this->suit);
    }

    public function getColor(): string
    {
        // Returnera rött för hjärter och ruter, annars svart
        return in_array($this->suit, ['hearts', 'diamonds']) ? 'red' : 'black';
    }

    public function getColoredUnicode(): string
    {
        if ($this->getColor() === 'red') {
            // Endast hjärter och ruter får färg
            return sprintf('<span style="color:red;">%s</span>', $this->getUnicode());
        }

        // Spader och klöver skrivs ut utan extra HTML
        return $this->getUnicode();
    }

    public function __toString(): string
    {
        return $this->getColoredUnicode();
    }
}
