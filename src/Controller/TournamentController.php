<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tournament',)]
#[IsGranted('ROLE_USER')]
class TournamentController extends AbstractController
{
    #[Route('/', name: 'app_tournament')]
    public function index(): Response
    {


        return $this->render('tournament/index.html.twig', [
            'controller_name' => 'TournamentController',
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'app_tournament_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        return $this->render('tournament/new.html.twig', []);
    }
}
