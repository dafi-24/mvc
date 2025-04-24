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
            $this->doubled = true;
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
        $pv = $this->player->getValue();
        $dv = $this->dealer->getValue();

        if ($pv > 21) {
            return 'Dealer wins – player busts';
        }
        if ($dv > 21 || $pv > $dv) {
            return 'Player wins';
        }
        if ($pv < $dv) {
            return 'Dealer wins';
        }
        return 'Draw';
    }

    public function getPlayer(): Player { return $this->player; }
    public function getDealer(): Dealer { return $this->dealer; }
}
