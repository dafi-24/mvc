<?php

namespace App\Card;

/**
 * Klass CardGraphic
 *
 * Utökar basklassen Card för att ge grafiska och färgade representationer
 * av spelkort med Unicode-symboler.
 */
class CardGraphic extends Card
{
    /**
     * Konstruktor för CardGraphic.
     *
     * Skapar en ny instans av CardGraphic med angivet färg och värde.
     *
     * @param string $suit  Färgen på kortet (t.ex. 'hearts', 'spades', 'diamonds', 'clubs').
     * @param string $value Värdet på kortet (t.ex. 'A', '2', 'K', 'Q', 'J').
     */
    public function __construct(string $suit, string $value)
    {
        parent::__construct($suit, $value);
    }

    /**
     * Hämtar den Unicode-representation av kortet.
     *
     * Sammansätter kortets värde med dess färgsymbol.
     *
     * @return string En sträng som innehåller kortvärdet följt av färgsymbolen.
     */
    public function getUnicode(): string
    {
        return $this->value . self::getSuitIcon($this->suit);
    }

    /**
     * Bestämmer kortets färg baserat på dess färg.
     *
     * @return string Returnerar 'red' för hjärter och rutor; 'black' för spader och klöver.
     */
    public function getColor(): string
    {
        return in_array($this->suit, ['hearts', 'diamonds'], true) ? 'red' : 'black';
    }

    /**
     * Hämtar HTML-formaterad Unicode-representation av kortet med färg.
     *
     * Om kortet är rött, omsluts Unicode-strängen i en <span> med inline CSS-färg.
     *
     * @return string En sträng med Unicode-symbolen, eventuellt innesluten i ett färgat <span>.
     */
    public function getColoredUnicode(): string
    {
        if ($this->getColor() === 'red') {
            return sprintf('<span style="color:red;">%s</span>', $this->getUnicode());
        }

        return $this->getUnicode();
    }

    /**
     * Magisk metod för att konvertera kortet till sträng.
     *
     * När objektet kastas till sträng returneras den färgade Unicode-representationen.
     *
     * @return string HTML-strängen för kortets färgade Unicode-symbol.
     */
    public function __toString(): string
    {
        return $this->getColoredUnicode();
    }
}
