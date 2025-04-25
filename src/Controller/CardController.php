<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
    #[Route('/card', name: 'card_home')]
    public function home(SessionInterface $session): Response
    {
        return $this->render('card/index.html.twig', [
            'session' => $session->all(),
        ]);
    }

    #[Route('/session', name: 'session_index')]
    public function index(SessionInterface $session): Response
    {
        $sessionData = $session->all();

        return $this->render('card/session.html.twig', [
            'session' => $sessionData,
        ]);
    }

    #[Route('/session/delete', name: 'session_delete')]
    public function sessionDelete(SessionInterface $session): Response
    {
        $session->clear();
        $this->addFlash('notice', 'Sessionen är raderad!');
        return $this->redirectToRoute('card_home');
    }

    #[Route('/card/deck', name: 'card_deck')]
    public function deck(SessionInterface $session): Response
    {
        if (!$session->has('card_deck')) {
            $deck = new DeckOfCards();
            $session->set('card_deck', $deck);
        }

        $deck = $session->get('card_deck');
        $cards = $deck->getCards();
        $sorted = [];

        foreach ($cards as $card) {
            $sorted[$card->getSuit()][] = $card;
        }
        ksort($sorted);

        return $this->render('card/deck.html.twig', [
            'sortedDeck' => $sorted,
        ]);
    }

    #[Route('/card/deck/shuffle', name: 'card_deck_shuffle')]
    public function deckShuffle(SessionInterface $session): Response
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set('card_deck', $deck);

        return $this->render('card/shuffle.html.twig', [
            'cards' => $deck->getCards(),
        ]);
    }

    #[Route('/card/deck/draw', name: 'card_deck_draw')]
    public function deckDraw(SessionInterface $session): Response
    {
        if (!$session->has('card_deck')) {
            $deck = new DeckOfCards();
            $session->set('card_deck', $deck);
        }

        $deck = $session->get('card_deck');
        $drawn = $deck->draw(1);
        $session->set('card_deck', $deck);

        return $this->render('card/draw.html.twig', [
            'drawnCards' => $drawn,
            'cardsLeft' => $deck->cardsLeft(),
        ]);
    }

    #[Route('/card/deck/draw/{number<\d+>}', name: 'card_deck_draw_number')]
    public function deckDrawNumber(int $number, SessionInterface $session): Response
    {
        if ($number > 52) {
            throw new Exception("Du kan inte dra mer än antalet kort i leken!");
        }

        if (!$session->has('card_deck')) {
            $deck = new DeckOfCards();
            $session->set('card_deck', $deck);
        }

        $deck = $session->get('card_deck');
        $drawn = $deck->draw($number);
        $session->set('card_deck', $deck);

        return $this->render('card/draw.html.twig', [
            'drawnCards' => $drawn,
            'cardsLeft' => $deck->cardsLeft(),
        ]);
    }
}
