<?php

namespace App\Card;

class BlackjackGame
{
    private DeckOfCards $deck;
    private Player $player;
    private Dealer $dealer;

    public function __construct()
    {
        $this->deck   = new DeckOfCards();
        $this->player = new Player();
        $this->dealer = new Dealer();
    }

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

    public function playerTurn(string $choice): bool
    {
        if ($choice === 'hit') {
            $cards = $this->deck->draw(1);
            $this->player->hit($cards[0]);
            return ! $this->player->isBust();
        }
        if ($choice === 'double') {
            $this->player->hit(...$this->deck->draw(1));
            return ! $this->player->isBust();
        }
        return true;
    }

    public function dealerTurn(): void
    {
        $this->dealer->playTurn($this->deck);
    }

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

    public function getPlayer(): Player
    {
        return $this->player;
    }
    public function getDealer(): Dealer
    {
        return $this->dealer;
    }
}
