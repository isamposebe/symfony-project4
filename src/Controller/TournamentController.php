<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Tour;
use App\Entity\Tournament;
use App\Form\RecordingCommandType;
use App\Form\TeamType;
use App\Form\TournamentType;
use App\Form\TourType;
use App\Service\PostgresqlDBService;
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
     * @param EntityManagerInterface $entityManager
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
     * @param PostgresqlDBService $serviceDB
     * @return Response
     */
    #[Route('/new', name: 'app_tournament_new')]
    public function new(Request $request, PostgresqlDBService $serviceDB): Response
    {
        /** Создаем турнир */
        $tournament = new Tournament();

        /** Создание формы турнира */
        $formTournament = $this->createForm(TournamentType::class, $tournament);
        $formTournament->handleRequest($request);

        /** Обработка нажатие кнопки из формы */
        if ($formTournament->isSubmitted() && $formTournament->isValid()) {

            /** Проверяем имя на повторы */
            if ($serviceDB->identityVerificationName($tournament)){

                /** Записываем в базу данных */
                $serviceDB->addItem($tournament);
                $tour = new Tour();
                $tour->setTournament($tournament);
                $tour->setNum(1);
                $serviceDB->addItem($tour);
                /** Выводим сообщение об удачном сохранении */
                $this->addFlash(
                    'notice',
                    'Your changes were saved!'
                );

                /** Переходим на страницу добавлении команд */
                return $this->redirectToRoute('app_tournament_show',[
                    'id' => $tournament->getId(),
                    'nameTour' => $tour->getNum()
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
     * @param PostgresqlDBService $serviceDB Сервис по работе с турниром
     * @return Response
     */
    #[Route('/show/{id}/{numTour}', name: 'app_tournament_show')]
    public function show(int $id, int $numTour, PostgresqlDBService $serviceDB): Response
    {
        /** Найдем турнир по id */
        $tournament = $serviceDB->searchTournamentID($id);

        $numTour = 1 + $numTour;

        $tour = $serviceDB->searchTourNumOfTournament($numTour, $tournament);

        /** Список игр в турнире */
        $listGame = $serviceDB->listGame($tour);

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
            'tournament' => $tournament,
            'listGame' => $listGame,
            'tour' => $tour
        ]);
    }

    /** Просмотр турнира и добавление в турнир команды
     * @param int $id ID турнира
     * @param PostgresqlDBService $serviceDB Сервис по работе с турниром
     * @return Response
     */
    #[Route('/addTour/{id}/', name: 'app_addTour')]
    public function addTour(int $id, PostgresqlDBService $serviceDB): Response
    {
        $oldTour = $serviceDB->searchTourID($id);
        $tour = new Tour();

        $num = 1 + $oldTour->getNum();

        $tour = $serviceDB->searchTourNumOfTournament($num, $oldTour->getTournament());

        $listGame = $serviceDB->listGame($tour);


        /** Форма для регистрации команды на турнир */
        $formRecordingTeam = $this->createForm(RecordingCommandType::class);

        /** Отправляем данные в шаблон
         * @formTeam Форма для добавления команды в турнир
         * @tournament Данные турнира
         * @listTeam Список команд в турнире
         */
        return $this->render('tournament/show.html.twig', [
            'tournament' => $oldTour->getTournament(),
            'listGame' => $listGame,
            'formRecordingTeam' => $formRecordingTeam,
            'tour' => $tour
        ]);
    }

    /** Добавление команды в турнире
     * @param Request $request Данные request
     * @param TournamentService $service Сервис по работе с турниром
     * @param PostgresqlDBService $serviceDB
     * @return Response
     */
    #[Route('/addTeamTournament/', name: 'app_add_team_tournament')]
    public function addTeam(Request $request,TournamentService $service, PostgresqlDBService $serviceDB): Response
    {
        /** Получаем из request имя команды */
        $team = new Team();
        $nameTeam = $request->request->get('nameTeam');

        /** Проверяем что есть такая команда */
        if ($serviceDB->identityVerificationName($team->setName($nameTeam))){
            return new Response('Нету такой команды', Response::HTTP_OK);
        }

        /** Ищем команду по имени и возвращаем команду из базы данных */
        $team = $serviceDB->searchTeam($nameTeam);

        /** Получаем турнир по ID */
        $tournamentID = $request->request->get('tournamentID');
        /** Берем турнир из базы данных */
        $tournament = $serviceDB->searchTournamentID($tournamentID);

        /** Записываем в базу данных добавление команды в турнир */
        $service->addTeamTournament($team, $tournament);

        return new Response( $team->getName(), Response::HTTP_OK);
    }

    /** Удаление Турнира
     * @param Request $request Данные страницы
     * @param PostgresqlDBService $serviceDB Работа с базой данных
     * @param Tournament $tournament Данные турнира
     * @return Response
     */
    #[Route('/delete/{id}', name: 'app_tournament_delete')]
    public function delete(Request $request, PostgresqlDBService $serviceDB, Tournament $tournament): Response
    {
        /** Проверяем новость которую надо удалить */
        if ($this->isCsrfTokenValid('delete'.$tournament->getId(), $request->getPayload()->getString('_token')))
        {
            /** Удаляем турнир */
            $serviceDB->deleteTournament($tournament);
        }
        /** Переходим на страницу добавлении команд */
        return $this->redirectToRoute('app_tournament',[]);
    }

}
