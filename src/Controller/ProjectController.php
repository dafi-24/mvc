<?php

namespace App\Controller;

use App\Repository\EnergyShareRepository;
use App\Repository\EnergyIntensityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * ProjectController hanterar projektet.
 * Inkluderar visning av energidata och intensitetsdata.
 */
class ProjectController extends AbstractController
{
    /**
     * Visar startsidan för projektet.
     *
     * @param EnergyShareRepository $energyShareRepo Repository för energidelares
     * @param EnergyIntensityRepository $energyIntensityRepo Repository för energintensitet
     * @return Response Renderad Twig-mall för projektstartsidan
     */
    #[Route('/proj', name: 'project')]
    public function index(
        EnergyShareRepository $energyShareRepo,
        EnergyIntensityRepository $energyIntensityRepo
    ): Response {
        $energyData = $energyShareRepo->findBy([], ['year' => 'DESC']);
        $intensityData = $energyIntensityRepo->findBy([], ['year' => 'DESC']);

        return $this->render('project/index.html.twig', [
            'controller_name' => 'Project',
            'energyData' => $energyData,
            'intensityData' => $intensityData,
        ]);
    }

    /**
     * Visar en about-sida för projektet.
     * 
     * Renderad Twig-mall för about-sidan
     */
    #[Route('/proj/about', name: 'project_about')]
    public function about(): Response
    {
        return $this->render('project/about.html.twig', [
            'controller_name' => 'About',
        ]);
    }

    /**
     * Visar databasinformation för projektet.
     *
     * Renderad Twig-mall för databasinformation
     */
    #[Route('/proj/about/database', name: 'project_database')]
    public function database(): Response
    {
        return $this->render('project/database.html.twig', [
            'controller_name' => 'Database',
        ]);
    }
}

