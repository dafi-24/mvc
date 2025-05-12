<?php

namespace App\Controller;

use App\Card\BlackjackGame;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * GameController hanterar Blackjack-spel via webbrutter.
 * Inkluderar start, spelåtgärder och visning av resultat.
 */
class GameController extends AbstractController
{
    /**
     * Visar startsidan för spelet.
     *
     * @return Response Renderad Twig-mall för spelsidan
     */
    #[Route('/game', name: 'game_home')]
    public function home(): Response
    {
        return $this->render('game/index.html.twig');
    }

    /**
     * Startar ett nytt Blackjack-spel, sparar det i sessionen och omdirigerar till spelvyn.
     *
     * @param SessionInterface $session Symfony-session för att lagra spelobjektet
     * @return Response Omdirigering till 'game_play'
     */
    #[Route('/game/start', name: 'game_start')]
    public function start(SessionInterface $session): Response
    {
        $game = new BlackjackGame();
        $game->start();
        $session->set('game', $game);

        return $this->redirectToRoute('game_play');
    }

    /**
     * Visar aktuell spelvy med spelar- och dealerhand samt deras poäng.
     *
     * @param SessionInterface $session Symfony-session innehållande spelobjektet
     * @return Response Renderad Twig-mall för spelvy
     */
    #[Route('/game/play', name: 'game_play')]
    public function play(SessionInterface $session): Response
    {
        $game = $session->get('game');

        return $this->render('game/play.html.twig', [
            'playerHand'  => $game->getPlayer()->getHand()->getHand(),
            'dealerHand'  => $game->getDealer()->getHand()->getHand(),
            'playerValue' => $game->getPlayer()->getValue(),
            'dealerValue' => $game->getDealer()->getValue(),
        ]);
    }

    /**
     * Hanterar spelarens "hit"-åtgärd (dra kort).
     * Sparar uppdaterat spel i sessionen och omdirigerar beroende på om spelaren är vid liv.
     *
     * @param SessionInterface $session Symfony-session för att lagra spelobjektet
     * @return Response Omdirigering till spelvy eller resultatvy
     */
    #[Route('/game/hit', name: 'game_hit')]
    public function hit(SessionInterface $session): Response
    {
        $game = $session->get('game');
        $alive = $game->playerTurn('hit');
        $session->set('game', $game);

        if (! $alive) {
            return $this->redirectToRoute('game_result');
        }

        return $this->redirectToRoute('game_play');
    }

    /**
     * Hanterar spelarens "stand"-åtgärd och genomför dealerns tur.
     * Sparar sedan spelets slutliga tillstånd och omdirigerar till resultatet.
     *
     * @param SessionInterface $session Symfony-session för att lagra spelobjektet
     * @return Response Omdirigering till resultatvy
     */
    #[Route('/game/stand', name: 'game_stand')]
    public function stand(SessionInterface $session): Response
    {
        $game = $session->get('game');
        $game->playerTurn('stand');
        $game->dealerTurn();
        $session->set('game', $game);

        return $this->redirectToRoute('game_result');
    }

    /**
     * Hanterar spelarens "double"-åtgärd (dubbla insatsen och dra ett kort),
     * sedan dealerns tur om spelaren fortfarande är vid liv.
     *
     * @param SessionInterface $session Symfony-session för att lagra spelobjektet
     * @return Response Omdirigering till resultatvy
     */
    #[Route('/game/double', name: 'game_double')]
    public function double(SessionInterface $session): Response
    {
        $game = $session->get('game');

        $alive = $game->playerTurn('double');
        if ($alive) {
            $game->dealerTurn();
        }

        $session->set('game', $game);
        return $this->redirectToRoute('game_result');
    }

    /**
     * Visar resultaten av spelet inklusive händer, poäng och vinnare.
     *
     * @param SessionInterface $session Symfony-session innehållande spelobjektet
     * @return Response Renderad Twig-mall för resultatsida
     */
    #[Route('/game/result', name: 'game_result')]
    public function result(SessionInterface $session): Response
    {
        $game = $session->get('game');

        return $this->render('game/result.html.twig', [
            'playerHand'  => $game->getPlayer()->getHand()->getHand(),
            'dealerHand'  => $game->getDealer()->getHand()->getHand(),
            'playerValue' => $game->getPlayer()->getValue(),
            'dealerValue' => $game->getDealer()->getValue(),
            'result'      => $game->determineWinner(),
        ]);
    }

    /**
     * Visar dokumentationssidan för spelet.
     *
     * @return Response Renderad Twig-mall för dokumentationssidan
     */
    #[Route('/game/doc', name: 'game_doc')]
    public function doc(): Response
    {
        return $this->render('game/doc.html.twig');
    }
}
