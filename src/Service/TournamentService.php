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
     * @param Team $item Данные элемента
     * @return void
     */
    function deleteItem($item):void
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    /** Добавляет элемент в базу данных
     * @param Team $item Данные элемента
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
    /** Поиск турнира по ID
     * @param int $id ID турнира
     * @return Tournament Получаем турнир
     */
    public function searchTeam($nameTeam): Team
    {
        $trams = $this->entityManager->getRepository(Team::class)->findBy(['name' => $nameTeam]);
        return $trams[0];
    }

    /** Запрос на получение всех комментариев по определенной новости
     * @param Tournament $tournament
     * @return array Массив из сущностей Comment
     */
    private function listTourNumTournament(Tournament $tournament): array
    {
        /** Получаем из базы данных список комментариев по новости */
        return $this->entityManager->getRepository(Tour::class)->findBy(
            ['tournament' => $tournament],
            ['id' => 'DESC']
        );
    }

    /** Добавление команды в турнир
     * @param Team $team Данные команды
     * @param Tournament $tournament Данные турнира
     * @return Game Данные Игры
     */
    public function addTeamTournament(Team $team,Game $game, Tournament $tournament): Game
    {
        /** Создаем тур */
        $strNameTour = 'Тур 1';
        $tour = $this->addTour($tournament, $strNameTour);

        /** Заполняем в игру команду */
        //$game = $this->addGame($team, $game, $tour);
        $game->setTeamRight($team);
        $game->setTeamLeft($team);
        $game->setTour($tour);
        $this->addItem($game);

        return $game;
    }

    /** Добавляем в базу данных тур или берем его из базы
     * @param Tournament $tournament Данные турнира
     * @return Tour Данные первого тура
     */
    private function addTour(Tournament $tournament, $strNameTour):Tour
    {
        /** Создаем новый первый тур */
        $tour = new Tour();
        $tour->setTournament($tournament);

        /** Берем всех туров по tournament */
        $listTour = $this->entityManager->getRepository(Tour::class)->findBy(['tournament' => $tournament]);

        foreach ($listTour as $item) {
            /** Проверяем на существование в базе данных тура */
            if ($item->getName() === $strNameTour) {
                return $item;
            }
        }
        $tour->setName($strNameTour);
        $this->entityManager->persist($tour);
        $this->entityManager->flush();
        return $tour;
    }

    /** Добавление команды в тур
     * @param Team $team Данные команды
     * @param Tour $tour Данные тура
     * @return Game Данные игры
     */
    private function addGame(Team $team, Game $game, Tour $tour):Game
    {
        $game->setTour($tour);

        /** Берем всех игр по tour */
        $listGame = $this->entityManager->getRepository(Game::class)->findBy([
            'tour' => $tour,
        ]);

        /** Если есть игра с командой $team, то вытаскиваем эту игру */
        return $this->checkTeamGame($team, $listGame, $game);
    }

    private function checkTeamGame(Team $team, array $listGame, Game $game):Game
    {
        /** Если есть игра с командой $team, то вытаскиваем эту игру */
        foreach ($listGame as $item) {
            /** Проверяю заполнена ли игра */
            if ($this->checkGameFull($item)){
                if ($item->getTeamLeft() === $team ){
                    $game = $item;
                }
                else{
                    if ($item->getTeamRight() == $team) {
                        $game = $item;
                    }
                }
            }else{
                /** Если не заполнена, то заполняю  */
                if ($item->getTeamLeft() == null ){
                    $game->setTeamLeft($team);
                    return $game;
                }
                else{
                    if ($item->getTeamRight() == null) {
                        $game->setTeamRight($team);
                        return $game;
                    }
                }
            }
        }
        return $game;
    }

    /** Проверка заполнения игры
     * @return bool Если не заполнена, то false иначе true
     */
    private function checkGameFull(Game $game):bool
    {
        if ($game->getTeamLeft() == Null){
            return false;
        }else{
            if ($game->getTeamRight() == Null){
                return false;
            }
        }
        return true;
    }
}