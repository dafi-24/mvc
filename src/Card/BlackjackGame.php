<?php

namespace App\Card;

/**
 * Class BlackjackGame
 *
 * Hanterar logiken för ett Blackjack-spel, inklusive kortlek, spelare, dealer och spelgång.
 */
class BlackjackGame
{
    private DeckOfCards $deck;
    private Player $player;
    private Dealer $dealer;

    /**
     * BlackjackGame constructor.
     *
     * Initierar kortlek, spelare och dealer.
     */
    public function __construct()
    {
        $this->deck   = new DeckOfCards();
        $this->player = new Player();
        $this->dealer = new Dealer();
    }

    /**
     * Startar spelet genom att blanda kortleken och dela ut två kort till både spelare och dealer.
     *
     * @return void
     */
    public function start(): void
    {
        $this->deck->shuffle();
        for ($i = 0; $i < 2; $i++) {
            $cards = $this->deck->draw(1);
            $this->player->hit($cards[0]);
            $cards = $this->deck->draw(1);
            $this->dealer->hit($cards[0]);
        }
    }

    /**
     * Hanterar spelarens val (hit eller double) och returnerar om spelaren fortfarande är kvar i spelet.
     *
     * @param string $choice Spelarens val, t.ex. 'hit' eller 'double'.
     * @return bool True om spelaren inte bustat, annars false.
     */
    public function playerTurn(string $choice): bool
    {
        if ($choice === 'hit') {
            $cards = $this->deck->draw(1);
            $this->player->hit($cards[0]);
            return ! $this->player->isBust();
        }
        if ($choice === 'double') {
            $cards = $this->deck->draw(1);
            $this->player->hit($cards[0]);
            return ! $this->player->isBust();
        }
        return true;
    }

    /**
     * Låter dealern spela sin tur enligt spelets regler.
     *
     * @return void
     */
    public function dealerTurn(): void
    {
        $this->dealer->playTurn($this->deck);
    }

    /**
     * Avgör vinnaren baserat på spelvärdena.
     *
     * @return string En sträng som beskriver vem som vann.
     */
    public function determineWinner(): string
    {
        $playervalue = $this->player->getValue();
        $dealervalue = $this->dealer->getValue();

        if ($playervalue > 21) {
            return 'Dealer wins – player busts';
        }
        if ($dealervalue > 21 || $playervalue > $dealervalue) {
            return 'Player wins';
        }
        if ($playervalue < $dealervalue) {
            return 'Dealer wins';
        }
        return 'Draw';
    }

    /**
     * Returnerar spelaren.
     *
     * @return Player Spelarobjektet.
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * Returnerar dealern.
     *
     * @return Dealer Dealerobjektet.
     */
    public function getDealer(): Dealer
    {
        return $this->dealer;
    }
}
