<?php

namespace App\Card;

class Card
{
    protected string $suit;
    protected string $value;

    protected static array $suitIcons = [
        'spades' => '♠',
        'hearts' => '♥',
        'diamonds' => '♦',
        'clubs' => '♣'
    ];

    public function __construct(string $suit, string $value)
    {
        $this->suit = $suit;
        $this->value = $value;
    }

    public function getSuit(): string
    {
        return $this->suit;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function getSuitIcon(string $suit): string
    {
        return self::$suitIcons[$suit] ?? $suit;
    }
}
