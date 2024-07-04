<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Tournament;
use App\Form\RecordingCommandType;
use App\Form\TeamType;
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
    /** Главная страница турнира
     * @param $entityManager
     * @return Response
     */
    #[Route('/', name: 'app_tournament')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        /** Сортируем по дате (Возможно выкинуть в сервис)*/
        $tournamentList = $entityManager->getRepository(Tournament::class);
        $tournamentList = $tournamentList->findAll();
        return $this->render('tournament/index.html.twig', [
            'controller_name' => 'TournamentController',
            'tournamentList' => $tournamentList,
            ]);
    }

    /**
     * @param Request $request Для проверки формы
     * @param TournamentService $service Сервис по работе с турнирам
     * @return Response
     */
    #[Route('/new', name: 'app_tournament_new')]
    public function new(Request $request,TournamentService $service): Response
    {
        /** Создаем турнир */
        $tournament = new Tournament();

        /** Создание формы турнира */
        $formTournament = $this->createForm(TournamentType::class, $tournament);
        $formTournament->handleRequest($request);

        /** Обработка нажатие кнопки из формы */
        if ($formTournament->isSubmitted() && $formTournament->isValid()) {

            /** Проверяем имя на повторы */
            if ($service->identityVerificationName($tournament)){

                /** Записываем в базу данных */
                $service->addItem($tournament);

                /** Выводим сообщение об удачном сохранении */
                $this->addFlash(
                    'notice',
                    'Your changes were saved!'
                );

                /** Переходим на страницу добавлении команд */
                return $this->redirectToRoute('app_tournament_show',[
                    'id' => $tournament->getId(),
                ]);
            }else{
                /** Выводим сообщение об ошибке */
                $this->addFlash(
                    'notice',
                    'Турнир с таким именем уже существует'
                );
            }
        }

        /** Отправляю данные в шаблон
         * @formTournament Данные формы для турнира
         */
        return $this->render('tournament/new.html.twig', [
            'formTournament' => $formTournament
        ]);
    }

    /** Просмотр турнира и добавление в турнир команды
     * @param int $id ID турнира
     * @param TournamentService $service Сервис по работе с турниром
     * @return Response
     */
    #[Route('/show/{id}', name: 'app_tournament_show')]
    public function show(int $id, TournamentService $service): Response
    {
        /** Найдем турнир по id */
        $tournament = $service->searchTournamentID($id);;

        $listTour = $service->listTourNumTournament($tournament);
        foreach ($listTour as $item) {
            if ($item->getName() == 'Тур 1'){
                $tour = $item;
            }
        }

        /** Список игр в турнире */
        $listGame = $service->listGame($tour);

        /** Форма для регистрации команды на турнир */
        $formRecordingTeam = $this->createForm(RecordingCommandType::class);

        $formTeam = $this->createForm(TeamType::class);

        /** Отправляем данные в шаблон
         * @formTeam Форма для добавления команды в турнир
         * @tournament Данные турнира
         * @listTeam Список команд в турнире
         */
        return $this->render('tournament/show.html.twig', [
            'formRecordingTeam' => $formRecordingTeam,
            'formTeam' => $formTeam,
            'tournament' => $tournament,
            'listGame' => $listGame,
            'tour' => $tour
        ]);
    }

    /** Добавление команды в турнире
     * @param Request $request Данные request
     * @param TournamentService $service Сервис по работе с турниром
     * @return Response
     */
    #[Route('/addTeamTournament/', name: 'app_add_team_tournament')]
    public function addTeam(Request $request,TournamentService $service): Response
    {
        /** Получаем из request имя команды */
        $team = new Team();
        $nameTeam = $request->request->get('nameTeam');

        /** Проверяем что есть такая команда */
        if ($service->identityVerificationName($team->setName($nameTeam))){
            return new Response('not Team', Response::HTTP_OK);
        }

        /** Ищем команду по имени и возвращаем команду из базы данных */
        $team = $service->searchTeam($nameTeam);

        /** Получаем турнир по ID */
        $tournamentID = $request->request->get('tournamentID');
        /** Берем турнир из базы данных */
        $tournament = $service->searchTournamentID($tournamentID);

        /** Записываем в базу данных добавление команды в турнир */
        $service->addTeamTournament($team, $tournament);

        return new Response( $team->getName(), Response::HTTP_OK);
    }

    #[Route('/delete/game/', name: 'app_Game_delete')]
    public function deleteGame(Request $request, TournamentService $service): Response
    {
        $idGame = $request->request->get('gemeID');
        $game = $service->searchGameByID($idGame);
        $service->deleteItem($game);
        return new Response($idGame, Response::HTTP_OK);

    }
}
