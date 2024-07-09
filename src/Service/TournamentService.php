<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Tour;
use App\Entity\Tournament;

class TournamentService
{
    /** Менеджер сущностей
     * @param PostgresqlDBService $postgresqlDBService
     */
    public function __construct(
        private readonly PostgresqlDBService $postgresqlDBService
    ){}

    /** Добавление команды в турнир и первый тур
     * @param Team $team Данные команды
     * @param Tournament $tournament Данные турнира
     * @return Game Данные Игры
     */
    public function addTeamTournament(Team $team, Tournament $tournament): Game
    {
        /** Создаем тур */
        $numTour = 0;
        $tour = $this->addTour($tournament, $numTour);

        /** Заполняем в игру команду */
        return $this->addGame($team, $tour);
    }

    /** Добавляем в базу данных тур или берем его из базы
     * @param Tournament $tournament Данные турнира
     * @return Tour Данные первого тура
     */
    public function addTour(Tournament $tournament, string $numTour):Tour
    {
        /** Создаем новый первый тур */
        $tour = new Tour();
        $tour->setTournament($tournament);

        /** Берем всех туров по tournament */
        $listTour = $this->postgresqlDBService->listTourNumTournament($tournament);

        foreach ($listTour as $item) {
            /** Проверяем на существование в базе данных тура */
            if ($item->getNum() == $numTour) {
                return $item;
            }
        }
        $tour->setNum($numTour);
        $this->postgresqlDBService->addItem($tour);
        return $tour;
    }

    /** Добавление команды в тур
     * @param Team $team Данные команды
     * @param Tour $tour Данные тура
     * @return Game Данные игры
     */
    public function addGame(Team $team, Tour $tour):Game
    {
        $game = new Game();

        /** Берем всех игр по tour */
        $listGame = $this->postgresqlDBService->listGame($tour);
        /** Проверки на дублирование команды в турнире */
        if (!$this->checkTeamGame($team, $listGame) ){
            foreach ($listGame as $item) {
                if ($item->getTeamLeft() == null ){
                    $game = $item;
                    $game->setTeamLeft($team);
                    $this->postgresqlDBService->addItem($game);
                    return $game;
                }elseif ($item->getTeamRight() == null ){
                    $game = $item;
                    $game->setTeamRight($team);
                    $this->postgresqlDBService->addItem($game);
                    return $game;
                }
            }
            $game->setTour($tour);
            $game->setTeamLeft($team);
            $this->postgresqlDBService->addItem($game);
        }
        return $game;
    }

    /** Проверка команды в играх
     * @param Team $team Данные команды
     * @param array $listGame Список игр
     * @return bool Если нашел команду, то true иначе false
     */
    public function checkTeamGame(Team $team, array $listGame): bool
    {
        /** Если есть игра с командой $team, то вытаскиваем эту игру */
        foreach ($listGame as $item) {
            if ($item->getTeamLeft() != null){
                if ($item->getTeamLeft()->getName() === $team->getName()){
                    return true;
                }
            }
            if ($item->getTeamRight() != null){
                if ($item->getTeamRight()->getName() === $team->getName())
                {
                    return true;
                }
            }
        }
        return false;
    }

    /** Проверка на уникальность команды
     * @param array $games Список игр
     * @return true Все игры уникальны
     * @throws \Exception
     */
    public function checkDuplicateGames(array $games):bool
    {
        $playedTeams = [];

        foreach ($games as $game) {
            $teamRightId = $game->getTeamRight()->getId();
            $teamLeftId = $game->getTeamLeft()->getId();

            // Проверяем, была ли уже сыграна игра между этими командами
            if (isset($playedTeams[$teamRightId][$teamLeftId]) || isset($playedTeams[$teamLeftId][$teamRightId])) {
                throw new \Exception('Дублирование игры между командами');
            }

            // Регистрируем игру между этими командами
            $playedTeams[$teamRightId][$teamLeftId] = true;
            $playedTeams[$teamLeftId][$teamRightId] = true;
        }
        dump($playedTeams);

        return true;
    }
}