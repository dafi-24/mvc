<?php
// src/Card/BlackjackGame.php
namespace App\Card;

// Spelmotor som sköter dealing, turordning och resultat
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

    /** Initierar lek, blandar och delar ut 2 kort var */
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

    /** Kör spelarens tur och returnerar false om bust */
    public function playerTurn(string $choice): bool
    {
        if ($choice === 'hit') {
            $cards = $this->deck->draw(1);
            $this->player->hit($cards[0]);
            return ! $this->player->isBust();
        }
        return true;
    }

    /** Kör dealer-logik */
    public function dealerTurn(): void
    {
        $this->dealer->playTurn($this->deck);
    }

    /** Jämför poäng och returnerar resultat-sträng */
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
