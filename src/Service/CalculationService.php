<?php

namespace App\Service;



use App\Entity\Game;
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
}