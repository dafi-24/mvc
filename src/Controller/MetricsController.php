<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * MetricsController hanterar vy för analys av olika metriker.
 * Inkluderar rendering av dashboard-sidan för metrics.
 */
class MetricsController extends AbstractController
{
    /**
     * Visar metrics-analys sidan.
     *
     * @return Response Renderad Twig-mall med titeln för metrics-sidan
     */
    #[Route('/metrics', name: 'metrics')]
    public function index(): Response
    {
        return $this->render('metrics/index.html.twig', [
            'title' => 'Metrics Analys',
        ]);
    }
}
