<?php

namespace App\Service;



use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Tournament;


class CalculationService
{
    public function __construct(
        private readonly PostgresqlDBService $DBService,
        private readonly TournamentService $tournamentService,

    ){}
    /**
     * @param array $a
     * @param array $b
     * @return array[]
     */
    public function matrixTour(array $listTeam): array
    {

        $countlistTeam = count($listTeam);
        $matrixTour = [$countlistTeam][$countlistTeam];
        for ($i = 0; $i < $countlistTeam; $i++) {
            $matrixTour[0][0] = $listTeam[$i];
        }
        $flowers = [ [ "Название" => "фиалки",
            "Стоимость" => 100,
            "Количество" => 15
        ],
            [ "Название" => "астры",
                "Стоимость" => 60,
                "Количество" => 25,
            ],
            [ "Название" => "каллы",
                "Стоимость" => 180,
                "Количество" => 7
            ]
        ];

        return $flowers;
    }

    /** Генерация Игр в турнире
     * @param Tournament $tournament Данные турнира
     * @param array $teams Список команд
     * @return array список игр по турниру
     * @throws \Exception
     */
    public function generateGamesForTournament(Tournament $tournament, array $teams):array
    {
        $countTeams = count($teams);

        if ( $countTeams % 2 !== 0) {// $numTeams < 10 (и не менее 10)||
            throw new \Exception('Количество команд должно быть четным ');
        }

        $numTours = $countTeams - 1; // Количество туров
        $games = [];

        for ($tourNum = 1; $tourNum <= $numTours; $tourNum++) {
            shuffle($teams); // Перемешиваем команды для каждого тура

            for ($i = 0; $i < $countTeams / 2; $i++) {
                $tour = $this->tournamentService->addTour($tournament, $tourNum);

                $game = new Game();
                $game = $this->tournamentService->addGame($teams[$i], $tour);

                $game = $this->tournamentService->addGame($teams[$countTeams - 1 - $i], $tour);

                $games[] = $game;
            }
        }
        return $games;
    }

    /** Подсчет количество сыгранных мачей
     * @param Team $team Данные команды
     * @param Tournament $tournament Данные турнира
     * @return int Количество матчей
     */
    public function numberMatches(Team $team, Tournament $tournament):int
    {
        $numMatches = 0;
        $listGame = $this->DBService->listGameTournament($tournament);
        foreach ($listGame as $game) {
            if ($game->getTeamRight() === $team || $game->getTeamLeft() === $team ){
                $numMatches++;
            }
        }
        return $numMatches;
    }

    /** Расчет побед команды в турнире
     * @param mixed $team Данные команды
     * @param Tournament $tournament Данные Турнира
     * @return int Кол-во побед
     */
    public function wins(mixed $team, Tournament $tournament):int
    {
        $wins = 0;
        $listGame = $this->DBService->listGameTournament($tournament);
        foreach ($listGame as $game) {
            $statusGame = $this->statusGame($game, $team);
            if ($statusGame === 3) {
                $wins++;
            }
        }
        return $wins;
    }

    /** Расчет статуса игры по команде
     * @param Game $game Данные игры
     * @param Team $team Данные команды
     * @return int Если команда победила 3, проиграла 0, ничья 1. При ошибке -1
     */
    public function statusGame(Game $game, Team $team):int
    {
        $status = -1;
        if ($game->getTeamLeft() === $team){
            if ($game->getGoalsScoredLeft() > $game->getGoalsScoredRight()) {
                $status = 3;
            }elseif ($game->getGoalsScoredLeft() < $game->getGoalsScoredRight()){
                $status = 0;
            }elseif($game->getGoalsScoredLeft() === $game->getGoalsScoredRight()){
                $status = 1;
            }
        }
        if ($game->getTeamRight() === $team){
            if ($game->getGoalsScoredRight() > $game->getGoalsScoredLeft()){
                $status = 3;
            }elseif ($game->getGoalsScoredRight()< $game->getGoalsScoredLeft()){
                $status = 0;
            }elseif($game->getGoalsScoredLeft() === $game->getGoalsScoredRight()){
                $status = 1;
            }
        }
        return $status;
    }

    /** Кол-во ничей у команды в турнире
     * @param Team $team Данные команды
     * @param Tournament $tournament Данные турнира
     * @return int Кол-во ничей
     */
    public function draws(Team $team, Tournament $tournament):int
    {
        $draws = 0;
        $listGame = $this->DBService->listGameTournament($tournament);
        foreach ($listGame as $game) {
            $statusGame = $this->statusGame($game, $team);
            if ($statusGame === 1) {
                $draws++;
            }
        }
        return $draws;
    }

    /** Кол-во проигрышей команды в турнире
     * @param Team $team Данные команды
     * @param Tournament $tournament Данные турнира
     * @return int Кол-во проигрышей
     */
    public function defeats(Team $team, Tournament $tournament): int
    {
        $defeats = 0;
        $listGame = $this->DBService->listGameTournament($tournament);
        foreach ($listGame as $game) {
            $statusGame = $this->statusGame($game, $team);
            if ($statusGame === 0) {
                $defeats++;
            }
        }
        return $defeats;
    }
}