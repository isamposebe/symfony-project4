<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Tour;
use App\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;

class TournamentService
{
    /** Менеджер сущностей
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ){}

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

    /** Удаление элемента из базы данных
     * @param $item - Данные элемента
     * @return void
     */
    function deleteItem($item):void
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    /** Добавляет элемент в базу данных
     * @param $item - Данные элемента
     * @return void
     */
    function addItem($item):void
    {
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    /** Поиск турнира по ID
     * @param int $id ID турнира
     * @return Tournament Получаем турнир
     */
    public function searchTournamentID(int $id): Tournament
    {
        return $this->entityManager->getRepository(Tournament::class)->find($id);
    }

    /** Поиск команды по имени
     * @param string $nameTeam Название команды
     * @return Team Получаем команду из базы данных
     */
    public function searchTeam(string $nameTeam): Team
    {
        return $this->entityManager->getRepository(Team::class)->find($nameTeam);
        //$trams = $this->entityManager->getRepository(Team::class)->findBy(['name' => $nameTeam]);
        //return $trams[0];
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

    /** Добавление команды в турнир и первый тур
     * @param Team $team Данные команды
     * @param Tournament $tournament Данные турнира
     * @return Game Данные Игры
     */
    public function addTeamTournament(Team $team, Tournament $tournament): Game
    {
        /** Создаем тур */
        $nameTour = 'Тур 1';
        $tour = $this->addTour($tournament, $nameTour);

        /** Заполняем в игру команду */
        return $this->addGame($team, $tour);
    }

    /** Добавляем в базу данных тур или берем его из базы
     * @param Tournament $tournament Данные турнира
     * @return Tour Данные первого тура
     */
    private function addTour(Tournament $tournament, string $nameTour):Tour
    {
        /** Создаем новый первый тур */
        $tour = new Tour();
        $tour->setTournament($tournament);

        /** Берем всех туров по tournament */
        //$listTour = $this->entityManager->getRepository(Tour::class)->findBy(['tournament' => $tournament]);
        $listTour = $this->listTourNumTournament($tournament);

        foreach ($listTour as $item) {
            /** Проверяем на существование в базе данных тура */
            if ($item->getName() == $nameTour) {
                return $item;
            }
        }
        $tour->setName($nameTour);
        $this->addItem($tour);
        return $tour;
    }

    /** Добавление команды в тур
     * @param Team $team Данные команды
     * @param Tour $tour Данные тура
     * @return Game Данные игры
     */
    private function addGame(Team $team, Tour $tour):Game
    {
        $game = new Game();

        /** Берем всех игр по tour */
        $listGame = $this->entityManager->getRepository(Game::class)->findBy([
            'tour' => $tour,
        ]);
        /** Проверки на дублирование команды в турнире */
        if (!$this->checkTeamGame($team, $listGame) ){
            foreach ($listGame as $item) {
                if ($item->getTeamLeft() == null ){
                    $game = $item;
                    $game->setTeamLeft($team);
                    $this->addItem($game);
                    return $game;
                }elseif ($item->getTeamRight() == null ){
                    $game = $item;
                    $game->setTeamRight($team);
                    $this->addItem($game);
                    return $game;
                }
            }
            $game->setTour($tour);
            $game->setTeamLeft($team);
            $this->addItem($game);
        }
        return $game;
    }

    /** Проверка команды в играх
     * @param Team $team Данные команды
     * @param array $listGame Список игр
     * @return bool Если нашел команду, то true иначе false
     */
    private function checkTeamGame(Team $team, array $listGame): bool
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
}