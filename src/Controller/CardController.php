<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CardController hanterar visning och hantering av kortlek via webbsidor.
 * Metoderna inkluderar visning av hemsida, sessionhantering samt kortleksoperationer
 * såsom visa, blanda och dra kort.
 */
class CardController extends AbstractController
{
    /**
     * Visar startsidan för kortapplikationen med nuvarande sessiondata.
     *
     * @param SessionInterface $session Symfony-session innehållande användardata
     * @return Response Renderad Twig-mall med sessionsinnehåll
     */
    #[Route('/card', name: 'card_home')]
    public function home(SessionInterface $session): Response
    {
        return $this->render('card/index.html.twig', [
            'session' => $session->all(),
        ]);
    }

    /**
     * Visar alla sessionens data på en egen sida.
     *
     * @param SessionInterface $session Symfony-session innehållande användardata
     * @return Response Renderad Twig-mall med sessionsdata
     */
    #[Route('/session', name: 'session_index')]
    public function index(SessionInterface $session): Response
    {
        $sessionData = $session->all();

        return $this->render('card/session.html.twig', [
            'session' => $sessionData,
        ]);
    }

    /**
     * Rensar all data i sessionen och omdirigerar till startsidan.
     *
     * @param SessionInterface $session Symfony-session som ska rensas
     * @return Response Omdirigering till 'card_home'
     */
    #[Route('/session/delete', name: 'session_delete')]
    public function sessionDelete(SessionInterface $session): Response
    {
        $session->clear();
        $this->addFlash('notice', 'Sessionen är raderad!');
        return $this->redirectToRoute('card_home');
    }

    /**
     * Visar hela kortleken sorterad efter färg.
     * Skapar en ny kortlek om ingen finns i sessionen.
     *
     * @param SessionInterface $session Symfony-session för att lagra kortlek
     * @return Response Renderad Twig-mall med sorterad kortlek
     */
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

    /**
     * Blandar kortleken, sparar i sessionen och visar den.
     *
     * @param SessionInterface $session Symfony-session för att lagra blandad kortlek
     * @return Response Renderad Twig-mall med blandade kort
     */
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

    /**
     * Drar ett kort från kortleken, uppdaterar sessionen och visar resultatet.
     *
     * @param SessionInterface $session Symfony-session för att lagra kortlek
     * @return Response Renderad Twig-mall med draget kort och antal kvar
     */
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

    /**
     * Drar ett angivet antal kort från kortleken, uppdaterar sessionen och visar resultatet.
     *
     * @param int $number Antal kort att dra
     * @param SessionInterface $session Symfony-session för att lagra kortlek
     * @throws Exception Om antal kort överstiger 52
     * @return Response Renderad Twig-mall med dragna kort och antal kvar
     */
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
