<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * LuckyControllerTwig hanterar grundläggande sidor och ett lyckonummer via Twig.
 * Inkluderar hem, om-sida, rapport och visning av ett slumpmässigt nummer.
 */
class LuckyControllerTwig extends AbstractController
{
    /**
     * Visar hemsidan.
     *
     * @return Response Renderad Twig-mall för startsidan
     */
    #[Route("/", name: "home")]
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    /**
     * Visar om-sidan med information om applikationen.
     *
     * @return Response Renderad Twig-mall för om-sidan
     */
    #[Route("/about", name: "about")]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }

    /**
     * Visar rapport-sidan.
     *
     * @return Response Renderad Twig-mall för rapporten
     */
    #[Route("/report", name: "report")]
    public function report(): Response
    {
        return $this->render('report.html.twig');
    }

    /**
     * Genererar och visar ett slumpmässigt lyckonummer mellan 0 och 100.
     *
     * @return Response Renderad Twig-mall med nummerdata
     */
    #[Route("/lucky", name: "lucky_number")]
    public function number(): Response
    {
        $number = random_int(0, 100);

        $data = [
            'number' => $number
        ];

        return $this->render('lucky_number.html.twig', $data);
    }
}
