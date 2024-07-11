<?php

namespace App\Controller;

use App\Service\CalculationService;
use App\Service\PostgresqlDBService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    /** Главная страница для не авторизованного пользователя
     * @param PostgresqlDBService $DBService Сервис по работе с базой данных
     * @return Response
     */
    #[Route('/main', name: 'app_main')]
    public function index(PostgresqlDBService $DBService): Response
    {
        $listTournaments = $DBService->listTournaments();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'listTournaments' => $listTournaments
        ]);
    }

    #[Route('/show/{id}', name: 'app_show')]
    public function show(int $id, PostgresqlDBService $DBService, CalculationService $calculationService): Response
    {
        /** Берем из базы данных турнир по ID */
        $tournament = $DBService->searchTournamentID($id);
        /** Список данных по команде с расчетами */
        $listTeamCalculation = $DBService->listTeamCalculation($tournament, $calculationService);
        dump($listTeamCalculation);
        return $this->render('main/show.html.twig', [
            'tournament' => $tournament,
            'listTeamCalculation' => $listTeamCalculation
        ]);
    }
}
