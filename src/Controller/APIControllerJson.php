<?php

namespace App\Controller;

use App\Entity\Library;
use App\Repository\LibraryRepository;
use App\Card\DeckOfCards;
use App\Card\CardGraphic;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class APIControllerJson extends AbstractController
{
    #[Route("/api", name: "api_overview")]
    public function apiOverview(): Response
    {
        return $this->render('api_overview.html.twig', ['routes']);
    }

    public function __construct()
    {
        date_default_timezone_set('Europe/Stockholm');
    }

    #[Route('/api/lucky/number', name: 'api_lucky')]
    public function jsonNumber(): Response
    {
        $number = random_int(0, 100);

        $data = [
            'lucky-number' => $number,
            'lucky-message' => 'Hi there!',
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route('/api/quote', name: 'api_quote')]
    public function jsonQuote(): Response
    {
        $quotes = [
            "Behind every great man, there is a woman rolling her eyes. Jim Carrey",
            "I am so clever that sometimes I don’t understand a single word of what I am saying. Oscar Wilde",
            "Before you marry a person, you should first make them use a computer with slow internet to see who they really are. Will Ferrell",
            "I refuse to join any club that would have me as a member. Groucho Marx",
            "A day without laughter is a day wasted. Charlie Chaplin"
        ];

        $randomQuote = $quotes[array_rand($quotes)];

        $data = [
            'quote' => $randomQuote,
            'date' => date('Y-m-d'),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );

        return $response;
    }

    #[Route('/api/deck', name: 'api_deck', methods: ['GET'])]
    public function getDeck(SessionInterface $session): JsonResponse
    {
        if (!$session->has('card_deck')) {
            $deck = new DeckOfCards();
            $session->set('card_deck', $deck);
        }

        $deck = $session->get('card_deck');
        $valueOrder = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];

        $sortedDeck = [];
        foreach ($deck->getCards() as $card) {
            $sortedDeck[$card->getSuit()][] = $card;
        }

        foreach ($sortedDeck as &$cards) {
            usort($cards, function ($cardA, $cardB) use ($valueOrder) {
                return array_search($cardA->getValue(), $valueOrder) <=> array_search($cardB->getValue(), $valueOrder);
            });

            $cards = array_map(fn ($card) => $card->getUnicode(), $cards);
        }

        return new JsonResponse($sortedDeck, JsonResponse::HTTP_OK);
    }

    #[Route('/api/deck/shuffle', name: 'api_deck_shuffle', methods: ['POST'])]
    public function shuffleDeck(SessionInterface $session): JsonResponse
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set('card_deck', $deck);

        $shuffledDeck = array_map(
            fn ($card) => (new CardGraphic($card->getSuit(), $card->getValue()))->getUnicode(),
            $deck->getCards()
        );

        return new JsonResponse($shuffledDeck, JsonResponse::HTTP_OK);
    }

    #[Route('/api/deck/draw', name: 'api_deck_draw', methods: ['POST'])]

    #[Route('/api/deck/draw/{number<\d+>}', name: 'api_deck_draw_number', methods: ['GET', 'POST'])]
    public function drawCards(SessionInterface $session, int $number): JsonResponse
    {
        if (!$session->has('card_deck')) {
            $deck = new DeckOfCards();
            $session->set('card_deck', $deck);
        }

        $deck = $session->get('card_deck');

        if ($number > $deck->cardsLeft()) {
            return new JsonResponse(
                ['error' => 'Not enough cards left in the deck.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $drawnCards = $deck->draw($number);
        $session->set('card_deck', $deck);

        return new JsonResponse([
            'drawn_cards' => array_map(fn ($card) => $card->getUnicode(), $drawnCards),
            'cards_left' => $deck->cardsLeft(),
        ]);
    }

    #[Route('/api/deck/form-draw', name: 'api_deck_form_draw', methods: ['POST'])]
    public function formDrawRedirect(Request $request): RedirectResponse
    {
        $number = (int) $request->request->get('number', 1);

        if ($number < 1) {
            $number = 1;
        }

        return $this->redirectToRoute('api_deck_draw_number', ['number' => $number]);
    }

    #[Route('/api/game', name: 'api_game', methods: ['GET'])]
    public function gameStatus(SessionInterface $session): JsonResponse
    {
        $game = $session->get('game');

        if (!$game) {
            return new JsonResponse([
                'error' => 'Spelet har inte startats. Gå till /game/start för att starta spelet.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $playerHand = $game->getPlayer()->getHand()->getHand();
        $dealerHand = $game->getDealer()->getHand()->getHand();
        $playerValue = $game->getPlayer()->getValue();
        $dealerValue = $game->getDealer()->getValue();

        $data = [
            'player' => [
                'hand' => array_map(fn ($card) => $card->getUnicode(), $playerHand),
                'value' => $playerValue,
            ],
            'dealer' => [
                'hand' => array_map(fn ($card) => $card->getUnicode(), $dealerHand),
                'value' => $dealerValue,
            ],
            'result' => $game->determineWinner(),
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/api/library/books', name: 'api_library_books', methods: ['GET'])]
    public function listBooks(LibraryRepository $repo): JsonResponse
    {
        $books = $repo->findAll();
        $data = array_map(function(Library $book) {
            return [
                'id' => $book->getId(),
                'title' => $book->getTitel(),
                'author' => $book->getAuthor(),
                'isbn' => $book->getIsbn(),
                'imageUrl' => $book->getImageUrl(),
            ];
        }, $books);

        return $this->json(
            $data,
            JsonResponse::HTTP_OK,
            [],
            ['json_encode_options' => JSON_PRETTY_PRINT]
        );
    }
}
