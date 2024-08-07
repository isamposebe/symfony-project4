<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Team;
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
     * @param TournamentService $service
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

    #[Route('/addTeamTournament/', name: 'app_add_team_tournament')]
    public function addTeam(Request $request,TournamentService $service,EntityManagerInterface $entityManager): Response
    {
        /** Получаем из request имя команды */
        $team = new Team();
        $team->setName($request->request->get('nameTeam'));

        /** Проверяем что есть такая команда */
        if ($service->identityVerificationName($team)){
            return new Response('not Team', Response::HTTP_OK);
        }
        /** Получаем турнир по ID */
        $tournamentID = $request->request->get('tournamentID');
        $tournament = $service->searchTournamentID($tournamentID);

        /** Записываем в базу данных добавление команды в турнир */
        $service->addTeamTournament($team, $tournament);







        return new Response('Add Team', Response::HTTP_OK);
    }
}
