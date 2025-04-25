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
        return in_array($this->suit, ['hearts', 'diamonds']) ? 'red' : 'black';
    }

    public function getColoredUnicode(): string
    {
        if ($this->getColor() === 'red') {
            return sprintf('<span style="color:red;">%s</span>', $this->getUnicode());
        }

        return $this->getUnicode();
    }

    public function __toString(): string
    {
        return $this->getColoredUnicode();
    }
}
