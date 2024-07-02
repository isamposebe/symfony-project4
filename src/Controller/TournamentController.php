<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Form\RecordingCommandType;
use App\Form\TournamentType;
use App\Service\TournamentService;
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
    public function new(Request $request,TournamentService $service, EntityManagerInterface $entityManager): Response
    {
        /** Создаем турнир */
        $tournament = new Tournament();
        /** Создание формы турнира */
        $formTournament = $this->createForm(TournamentType::class, $tournament);
        $formTournament->handleRequest($request);

        if ($formTournament->isSubmitted() && $formTournament->isValid()) {
            /** Проверяем имя на повторы */
            if ($service->identityVerificationName($tournament)){
                /** Записываем в базу данных */
                $service->addItem($tournament);
                $this->addFlash(
                    'notice',
                    'Your changes were saved!'
                );
                return $this->redirectToRoute('app_tournament_show',[
                    'id' => $tournament->getId(),
                ]);
            }else{
                $this->addFlash(
                    'notice',
                    'Турнир с таким именем уже существует'
                );
            }

        }


        return $this->render('tournament/new.html.twig', [
            'formTournament' => $formTournament
        ]);
    }
    #[Route('/show/{id}', name: 'app_tournament_show')]
    public function show(int $id, TournamentService $service,EntityManagerInterface $entityManager): Response
    {
        /** Найдем турнир по id */
        $tournament = $service->searchTournamentID($id);;

        $listTeam = [];

        /** Форма для регистрации команды на турнир */
        $formTeam = $this->createForm(RecordingCommandType::class);

        return $this->render('tournament/show.html.twig', [
            'formTeam' => $formTeam,
            'tournament' => $tournament,
            'listTeam' => $listTeam
        ]);
    }
}
