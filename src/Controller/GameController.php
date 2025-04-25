<?php

namespace App\Controller;

use App\Card\BlackjackGame;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/game', name: 'game_home')]
    public function home(): Response
    {
        return $this->render('game/index.html.twig');
    }

    #[Route('/game/start', name: 'game_start')]
    public function start(SessionInterface $session): Response
    {
        $game = new BlackjackGame();
        $game->start();
        $session->set('game', $game);

        return $this->redirectToRoute('game_play');
    }

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

    #[Route('/game/stand', name: 'game_stand')]
    public function stand(SessionInterface $session): Response
    {
        $game = $session->get('game');
        $game->playerTurn('stand');
        $game->dealerTurn();
        $session->set('game', $game);

        return $this->redirectToRoute('game_result');
    }

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

    #[Route('/game/doc', name: 'game_doc')]
    public function doc(): Response
    {
        return $this->render('game/doc.html.twig');
    }
}
