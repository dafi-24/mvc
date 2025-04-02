<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyControllerJson
{
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
}