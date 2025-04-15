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

    public function __toString(): string
    {
        return $this->getUnicode();
    }
}
