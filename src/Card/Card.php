<?php

namespace App\Card;

/**
 * Class Card
 *
 * Representerar ett kort i ett kortspel med en färg, värde och suit.
 */
class Card
{
    protected string $suit;
    protected string $value;

    /**
     * @var array<string, string>
     * En associerad array som lagrar symboler för varje kortsuit.
     */
    protected static array $suitIcons = [
        'spades' => '♠',
        'hearts' => '♥',
        'diamonds' => '♦',
        'clubs' => '♣'
    ];

    /**
     * Card constructor.
     *
     * Skapar ett kort med en specifik suit och värde.
     *
     * @param string $suit Kortets suit (t.ex. spades, hearts, diamonds, clubs).
     * @param string $value Kortets värde (t.ex. 2, 3, 4, ..., King, Ace).
     */
    public function __construct(string $suit, string $value)
    {
        $this->suit = $suit;
        $this->value = $value;
    }

    /**
     * Hämtar kortets suit.
     *
     * @return string Kortets suit.
     */
    public function getSuit(): string
    {
        return $this->suit;
    }

    /**
     * Hämtar kortets värde.
     *
     * @return string Kortets värde.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Hämtar suitens symbol baserat på suitens namn.
     *
     * @param string $suit Namnet på suiten (spades, hearts, diamonds, clubs).
     * @return string suitens symbol (t.ex. ♠, ♥, ♦, ♣).
     */
    public static function getSuitIcon(string $suit): string
    {
        return self::$suitIcons[$suit] ?? $suit;
    }

    /**
     * Hämtar kortets färg baserat på suiten.
     *
     * Röda kort är 'hearts' och 'diamonds', medan svarta kort är 'spades' och 'clubs'.
     *
     * @return string Kortets färg ('red' eller 'black').
     */
    public function getColor(): string
    {
        return in_array($this->suit, ['hearts', 'diamonds']) ? 'red' : 'black';
    }
}
