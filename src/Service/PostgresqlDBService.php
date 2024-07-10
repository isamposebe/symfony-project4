<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Tour;
use App\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;
class PostgresqlDBService
{


    /** Менеджер сущностей
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CalculationService $calculationService,
    ){}

    /** Добавляет элемент в базу данных
     * @param $item - Данные элемента
     * @return void
     */
    public function addItem($item):void
    {
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    /** Удаление элемента из базы данных
     * @param $item - Данные элемента
     * @return void
     */
    public function deleteItem($item):void
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    /** Удаление турнира
     * @param Tournament $tournament Данные турнира
     * @return void
     */
    public function deleteTournament(Tournament $tournament):void
    {
        /** Берем список всех туров по турниру */
        $listTour = $this->listTourNumTournament($tournament);

        /** Проходим по списку и удаляем тур */
        foreach ($listTour as $item) {

            /** Получаем список игр по туру */
            $listGame = $this->listGame($item);

            /** Проходим по списку и удаляем игры */
            foreach ($listGame as $game) {
                /** Удаляем игру */
                $this->entityManager->remove($game);
            }
            /** Удаляем тур */
            $this->entityManager->remove($item);
        }
        /** Удаляем турнир */
        $this->entityManager->remove($tournament);
        /** Отправляем в базу данных запрос на удаление */
        $this->entityManager->flush();
    }

    /** Удаление тура
     * @param Tour $tour Данные тура
     * @return void
     */
    private function deleteTour(Tour $tour):void
    {

        $listGame = $this->listGame($tour);
        foreach ($listGame as $game) {
            $this->entityManager->remove($game);
        }
        $this->entityManager->remove($tour);
        $this->entityManager->flush();
    }

    /** Проверка на идентичность (Проверяется Name)
     * - Поиск элемента по его классу (Team, Tournament, Tour, User)
     * @param $item - Данные элемента
     * @return bool Если есть совпадения, то false, иначе true
     */
    public function identityVerificationName($item):bool
    {
        $list = $this->entityManager->getRepository($item::class)->findAll();
        foreach ($list as $t) {
            if ($t->getName() == $item->getName()){
                return false;
            }
        }
        return true;
    }
    /** Поиск турнира по ID
     * @param int $id ID турнира
     * @return Tournament Получаем турнир
     */
    public function searchTournamentID(int $id): Tournament
    {
        return $this->entityManager->getRepository(Tournament::class)->find($id);
    }
    /** Поиск тура по ID
     * @param int $id ID тура
     * @return Tour Получаем тур
     */
    public function searchTourID(int $id): Tour
    {
        return $this->entityManager->getRepository(Tour::class)->find($id);
    }

    /** Поиск тура по имени
     * @param int $numTour
     * @param Tournament $tournament
     * @return Tour Получаем тур
     */
    public function searchTourNumOfTournament(int $numTour, Tournament $tournament): Tour
    {
        $listTour = $this->listTourNumTournament($tournament);
        foreach ($listTour as $item) {
            if ($numTour === $item->getNum()) {
                return $item;
            }
        }
        $tour = new Tour();
        $tour->setTournament($tournament);
        $tour->setNum($numTour);
        $this->addItem($tour);
        return $tour;
    }

    /** Поиск команды по имени
     * @param string $nameTeam Название команды
     * @return Team Получаем команду из базы данных
     */
    public function searchTeam(string $nameTeam): Team
    {
        $trams = $this->entityManager->getRepository(Team::class)->findBy(['name' => $nameTeam]);
        return $trams[0];
    }

    public function searchGameByID(int $idGame): Game
    {
        return $this->entityManager->getRepository(Game::class)->find($idGame);
    }

    /** Запрос на получение всех турниров
     * @return array Массив из сущностей Tournament
     */
    public function listTournaments(): array
    {
        $tournamentList = $this->entityManager->getRepository(Tournament::class);
        return $tournamentList->findAll();
    }

    /** Запрос на получение всех Туров по определенному турниру
     * @param Tournament $tournament Данные турнира
     * @return array Массив из сущностей Tour
     */
    public function listTourNumTournament(Tournament $tournament): array
    {
        /** Получаем из базы данных список комментариев по новости */
        return $this->entityManager->getRepository(Tour::class)->findBy(
            ['tournament' => $tournament],
            ['id' => 'DESC']
        );
    }

    /** Выбрать список игр по туру
     * @param Tour $tour Данные тура
     * @return array Массив игр по определенному туру
     */
    public function listGame(Tour $tour): array
    {
        return $this->entityManager->getRepository(Game::class)->findBy(['tour' => $tour]);
    }

    /** Список всех игр в турнире
     * @param Tournament $tournament Данные турнира
     * @return array Список игр
     */
    public function listGameTournament(Tournament $tournament): array
    {
        $listGame = [];
        //$listGame = $this->entityManager->getRepository(Game::class)->findBy(['team' => $team]);
        $listTour = $this->listTourNumTournament($tournament);
        foreach ($listTour as $tour) {
            if ($tour->getNum() !== 0) {
                $listGame = $this->listGame($tour);
                foreach ($listGame as $game) {
                    $listGame[] = $game;
                }
            }

        }
        return $listGame;
    }

    /** Список команд в туре
     * @param Tour $oldTour Данные тура
     * @return array Массив данных команд
     */
    public function listTeam(Tour $oldTour):array
    {
        $listTeam = [];
        /**  Получаем список игр по туру */
        $listGame = $this->listGame($oldTour);
        /** Перебирая список иг вытаскиваем команды из игр */
        foreach ($listGame as $game) {
            $listTeam[] = $game->getTeamRight();
            $listTeam[] = $game->getTeamLeft();
        }
        return $listTeam;
    }
}